<?php

namespace App\Service;

use App\Entity\Artist;
use App\Entity\AudioFile;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use getID3;
use getid3_exception;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use SplFileInfo;

class AudioFileFactory implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var getID3 */
    protected $getID3;

    /** @var string */
    protected $currentFile = '';

    protected const WHITELIST_MIME_TYPE = [
        'audio/mpeg',
        'audio/x-m4a',
        'audio/ogg',
        'video/mp4',
        'audio/x-wav',
        'image/jpeg',
    ];

    protected const AUDIO_FORMAT = [
        'audio/mpeg',
        'audio/x-m4a',
        'audio/ogg',
        'video/mp4',
        'audio/x-wav',
    ];

    protected const COMPILATION_FOLDER_NAMES = [
        'Compilations',
        '=BO=',
        '=Compilations='
    ];

    /**
     * @var ArtistManager
     */
    protected $artistManager;
    /**
     * @var AlbumManager
     */
    protected $albumManager;

    /**
     * ID3TagManager constructor.
     * @param ArtistManager $artistManager
     * @param AlbumManager  $albumManager
     * @throws getid3_exception
     */
    public function __construct(ArtistManager $artistManager, AlbumManager $albumManager)
    {
        $this->getID3 = new getID3();
        $this->artistManager = $artistManager;
        $this->albumManager = $albumManager;
    }

    /**
     * @param SplFileInfo $fileInfo
     * @return AudioFile|null
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function fromFile(SplFileInfo $fileInfo): ?AudioFile
    {
        $this->currentFile = $fileInfo->getRealPath();
        $id3 = $this->getID3->analyze($this->currentFile);

        $mimeType = $this->getMime($fileInfo, $id3);

        if (!in_array($mimeType, self::WHITELIST_MIME_TYPE, true)) {
            $this->logger->warning(sprintf('File: %s ==> Unknown type %s (Skipping)', $this->currentFile, $mimeType));
            return null;
        }

        $audioFile = new AudioFile();
        $audioFile->setPath($this->currentFile);
        $audioFile->setMd5(md5_file($this->currentFile));
        $audioFile->setMimeType($mimeType);
        $audioFile->setFileSize($fileInfo->getSize());
        $audioFile->setAudio(in_array($mimeType, self::AUDIO_FORMAT, true));

        $albumName = $this->getAlbumName($id3);

        $audioFile->setArtist($this->getArtist($id3))
            ->setCompilation($this->isFromCompilation($id3, $albumName));

        $album = $audioFile->isCompilation() ?
            $this->albumManager->getCompilationAlbum($albumName) :
            $this->albumManager->getArtistAlbum($audioFile->getArtist(), $albumName);
        $audioFile->setAlbum($album);

        if ($audioFile->isAudio()) {
            $audioFile
                ->setTitle($this->getTitle($id3))
                ->setTrackNumber($this->getTrackNumber($id3))
                ->setCodec($this->getCodec($id3))
                ->setBitrate($this->getSampleRate($id3))
                ->setChannels($this->getChannels($id3))
                ->setBitDepth($this->getBitDepth($id3))
                ->setPlaytime($this->getPlaytime($id3));
        } else {
            $audioFile->setTitle($fileInfo->getBasename('.'.$fileInfo->getExtension()));
        }
        return $audioFile;
    }

    /**
     * @param SplFileInfo $fileInfo
     * @param array       $id3
     * @return string
     */
    protected function getMime(SplFileInfo $fileInfo, array $id3): string
    {
        $mime = mime_content_type($fileInfo->getRealPath());
        if ($mime === 'application/octet-stream' && isset($id3['mime_type'])) {
            return $id3['mime_type'];
        }
        return $mime;
    }

    /**
     * @param array $id3
     * @return string
     */
    protected function getAlbumName(array $id3): string
    {
        $album = $this->getFromHtmlTag($id3, 'album', 'album');
        if (null === $album) {
            $album = basename(dirname($this->currentFile));
            $this->logger->warning(sprintf('File: %s ==> Unable to find album, set to "%s"', $this->currentFile, $album));
        }
        return $album;
    }

    /**
     * @param array $id3
     * @return string|null
     */
    protected function getTitle(array $id3): ?string
    {
        $title = $this->getFromHtmlTag($id3, 'title', 'title');
        if (null === $title) {
            $matches = [];
            preg_match('/[0-9\-\s]*-?(.*)\.([a-zA-Z0-9]{3,4})$/m', basename($this->currentFile), $matches);
            if (count($matches) > 0) {
                $title = $matches[1];
            }
            $this->logger->warning(sprintf('File: %s ==> Unable to find title, set to "%s"', $this->currentFile, $title));
        }
        return $title;
    }

    /**
     * @param array $id3
     * @return Artist
     * @throws ORMException
     * @throws OptimisticLockException
     */
    protected function getArtist(array $id3): Artist
    {
        $artist = $this->getFromHtmlTag($id3, 'artist', 'artist');
        if (null === $artist) {
            $artist = basename(dirname($this->currentFile, 2));
            if (in_array($artist, self::COMPILATION_FOLDER_NAMES, true)) {
                $artist = 'Unknown artist';
            }
            $this->logger->warning(sprintf('File: %s ==> Unable to find artist, set to "%s"', $this->currentFile, $artist));
        }
        return $this->artistManager->findByName($artist);
    }

    /**
     * @param array $id3
     * @return int|null
     */
    protected function getTrackNumber(array $id3): ?int
    {
        return (int) $this->getFromHtmlTag($id3, 'track_number', 'track_number');
    }

    /**
     * @param array $id3
     * @return bool
     */
    protected function isFromCompilation(array $id3, string $albumName): bool
    {
        if (in_array(basename(dirname($this->currentFile, 2)), self::COMPILATION_FOLDER_NAMES, true)) {
            return true;
        }

        if ($this->albumManager->isCompilationAlbum($albumName)) {
            return true;
        }

        $compilation = $this->getFromHtmlTag($id3, 'part_of_a_compilation');
        if (null !== $compilation) {
            return $compilation === '1';
        }
        return $this->getFromHtmlTag($id3, 'compilation') === '1';
    }

    /**
     * @param array $id3
     * @return string|null
     */
    protected function getCodec(array $id3): ?string
    {
        return $id3['audio']['dataformat'] ?? null;
    }

    /**
     * @param array $id3
     * @return int|null
     */
    protected function getSampleRate(array $id3): ?int
    {
        if (isset($id3['audio']['sample_rate'])) {
            return (int) $id3['audio']['sample_rate'];
        }
        return null;
    }

    /**
     * @param array $id3
     * @return int|null
     */
    protected function getChannels(array $id3): ?int
    {
        if (isset($id3['audio']['channels'])) {
            return (int) $id3['audio']['channels'];
        }
        return null;
    }

    /**
     * @param array $id3
     * @return int|null
     */
    protected function getBitDepth(array $id3): ?int
    {
        if (isset($id3['audio']['bits_per_sample'])) {
            return (int) $id3['audio']['bits_per_sample'];
        }
        return null;
    }

    /**
     * @param array $id3
     * @return int|null
     */
    protected function getPlaytime(array $id3): ?int
    {
        if (isset($id3['playtime_seconds'])) {
            return (int) $id3['playtime_seconds'];
        }
        return null;
    }

    /**
     * @param array       $id3
     * @param string      $v1Key
     * @param string|null $v2Key
     * @return string|null
     */
    protected function getFromHtmlTag(array $id3, string $v2Key, ?string $v1Key = null): ?string
    {
        $groupComment = ['id3v2', 'quicktime', 'vorbiscomment'];

        if (isset($id3['tags'])) {
            $tagsHtml = $id3['tags'];

            foreach($groupComment as $group) {
                if (isset($tagsHtml[$group][$v2Key])) {
                    if (count($tagsHtml[$group][$v2Key]) > 1) {
                        $this->logger->warning(sprintf('File: %s ==> %s/%s has more than one value: %s', $this->currentFile, $group, $v2Key, json_encode($tagsHtml[$group][$v2Key])));
                        return null;
                    }
                    return $tagsHtml[$group][$v2Key][0];
                }
            }

            if ($v1Key !== null && isset($tagsHtml['id3v1'][$v1Key])) {
                if (count($tagsHtml['id3v1'][$v1Key]) > 1) {
                    $this->logger->warning(sprintf('File: %s ==> id3v1/%s has more than one value: %s', $this->currentFile, $v1Key, json_encode($tagsHtml['id3v1'][$v1Key])));
                    return null;
                }
                return $tagsHtml['id3v1'][$v1Key][0];
            }
        }

        if (isset($id3['quicktime']['comments'][$v2Key])) {
            if (count($id3['quicktime']['comments'][$v2Key]) > 1) {
                $this->logger->warning(sprintf('File: %s ==> quicktime/comments/%s has more than one value: %s', $this->currentFile, $v2Key, json_encode($id3['quicktime']['comments'][$v2Key])));
                return null;
            }
            return $id3['quicktime']['comments'][$v2Key][0];
        }

        return null;
    }
}
