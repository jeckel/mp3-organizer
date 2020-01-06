<?php

namespace App\Service;

use App\Entity\AudioFile;
use getID3;
use getid3_lib;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use SplFileInfo;

class ID3TagManager implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var getID3 */
    protected $getID3;

    /**
     * ID3TagManager constructor.
     */
    public function __construct()
    {
        $this->getID3 = new getID3();
    }

    /**
     * @param SplFileInfo $file
     * @param AudioFile   $audioFile
     * @return AudioFile
     */
    public function analyse(SplFileInfo $file, AudioFile $audioFile): AudioFile
    {
        $fileInfo = $this->getID3->analyze($file->getPathname());
        getid3_lib::CopyTagsToComments($fileInfo);

        $audioFile->setPath($file->getRealPath());
        $audioFile->setMd5(md5_file($file->getRealPath()));
        $audioFile->setMetadata('');
//        $audioFile->setMetadata(json_encode($fileInfo, JSON_INVALID_UTF8_IGNORE | JSON_THROW_ON_ERROR));

        if (isset($fileInfo['comments'])) {
            $comments = $fileInfo['comments'];
            if (($title = $this->getFromComment($comments, 'title', $file)) !== null) {
                $audioFile->setTitle($title);
            }
            if (($album = $this->getFromComment($comments, 'album', $file)) !== null) {
                $audioFile->setAlbum($album);
            }
            if (($artist = $this->getFromComment($comments, 'artist', $file)) !== null) {
                $audioFile->setArtist($artist);
            }
            if (($trackNumber = $this->getFromComment($comments, 'iTunes_CDDB_TrackNumber', $file)) !== null) {
                $audioFile->setTrackNumber((int) $trackNumber);
            } else {
                $matches = [];
                preg_match('/^(\d+).*/m', $file->getFilename(), $matches);
                if (count($matches) > 0) {
                    $audioFile->setTrackNumber((int) $matches[1][0]);
                }
            }
            if (($compilation = $this->getFromComment($comments, 'compilation', $file)) !== null) {
                $audioFile->setCompilation((bool) $compilation);
            }
        }

        if (isset($fileInfo['audio'])) {
            $audioFile->setCodec($fileInfo['audio']['dataformat']);
            $audioFile->setBitrate($fileInfo['audio']['sample_rate']);
            $audioFile->setChannels($fileInfo['audio']['channels']);
            if (isset($fileInfo['audio']['bits_per_sample'])) {
                $audioFile->setBitDepth($fileInfo['audio']['bits_per_sample']);
            }
        }

        if (isset($fileInfo['playtime_seconds'])) {
            $audioFile->setPlaytime((int) $fileInfo['playtime_seconds']);
        }

        return $audioFile;
    }

    /**
     * @param array       $comments
     * @param string      $key
     * @param SplFileInfo $fileInfo
     * @return string
     */
    protected function getFromComment(array $comments, string $key, SplFileInfo $fileInfo): ?string
    {
        if (! isset($comments[$key])) {
//            $this->logger->warning(sprintf('File: %s ==> Comment %s not defined', $fileInfo->getRealPath(), $key));
            return null;
        }
        if (count($comments[$key]) > 1) {
            $this->logger->warning(sprintf('File: %s ==> Comment %s has more than one value: %s', $fileInfo->getRealPath(), $key, print_r($comments[$key], true)));
            return null;
        }
        return $comments[$key][0];
    }
}
