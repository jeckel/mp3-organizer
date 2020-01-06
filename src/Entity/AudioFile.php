<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AudioFileRepository")
 */
class AudioFile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string|null
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $md5;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int|null
     */
    private $trackNumber;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    private $compilation = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int|null
     */
    private $playtime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string|null
     */
    private $codec;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int|null
     */
    private $bitrate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int|null
     */
    private $channels;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int|null
     */
    private $bitDepth;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $fileSize = 0;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    private $audio = true;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mimeType;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Artist", inversedBy="audioFiles")
     * @ORM\JoinColumn(nullable=false)
     * @var Artist
     */
    private $artist;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Album", inversedBy="audioFiles")
     * @ORM\JoinColumn(nullable=false)
     * @var Album
     */
    private $album;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Artist|null
     */
    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    /**
     * @param Artist $artist
     * @return $this
     */
    public function setArtist(Artist $artist): self
    {
        $this->artist = $artist;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMd5(): ?string
    {
        return $this->md5;
    }

    /**
     * @param string $md5
     * @return $this
     */
    public function setMd5(string $md5): self
    {
        $this->md5 = $md5;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTrackNumber()
    {
        return $this->trackNumber;
    }

    /**
     * @param int $trackNumber
     * @return AudioFile
     */
    public function setTrackNumber(int $trackNumber): self
    {
        $this->trackNumber = $trackNumber;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getCompilation(): bool
    {
        return $this->compilation;
    }

    /**
     * @param bool $compilation
     * @return $this
     */
    public function setCompilation(bool $compilation): self
    {
        $this->compilation = $compilation;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCompilation(): bool
    {
        return $this->compilation;
    }

    /**
     * @return int|null
     */
    public function getPlaytime(): ?int
    {
        return $this->playtime;
    }

    /**
     * @param int $playtime
     * @return $this
     */
    public function setPlaytime(int $playtime): self
    {
        $this->playtime = $playtime;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCodec(): ?string
    {
        return $this->codec;
    }

    /**
     * @param string $codec
     * @return $this
     */
    public function setCodec(string $codec): self
    {
        $this->codec = $codec;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getBitrate(): ?int
    {
        return $this->bitrate;
    }

    /**
     * @param int $bitrate
     * @return $this
     */
    public function setBitrate(int $bitrate): self
    {
        $this->bitrate = $bitrate;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getChannels(): ?int
    {
        return $this->channels;
    }

    /**
     * @param int|null $channels
     * @return $this
     */
    public function setChannels(?int $channels): self
    {
        $this->channels = $channels;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getBitDepth(): ?int
    {
        return $this->bitDepth;
    }

    /**
     * @param int|null $bitDepth
     * @return $this
     */
    public function setBitDepth(?int $bitDepth): self
    {
        $this->bitDepth = $bitDepth;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    /**
     * @param int $fileSize
     * @return $this
     */
    public function setFileSize(int $fileSize): self
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    /**
     * @return bool
     */
    public function getAudio(): bool
    {
        return $this->audio;
    }

    /**
     * @return bool
     */
    public function isAudio(): bool
    {
        return $this->audio;
    }

    /**
     * @param bool $audio
     * @return $this
     */
    public function setAudio(bool $audio): self
    {
        $this->audio = $audio;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     * @return $this
     */
    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getAlbum(): ?Album
    {
        return $this->album;
    }

    public function setAlbum(Album $album): self
    {
        $this->album = $album;

        return $this;
    }
}
