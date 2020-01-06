<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AlbumRepository")
 */
class Album
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $compilation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Artist", inversedBy="albums")
     */
    private $artist;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $normalizedName;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AudioFile", mappedBy="album", orphanRemoval=true)
     */
    private $audioFiles;

    public function __construct()
    {
        $this->audioFiles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCompilation(): ?bool
    {
        return $this->compilation;
    }

    public function setCompilation(bool $compilation): self
    {
        $this->compilation = $compilation;

        return $this;
    }

    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    public function setArtist(?Artist $artist): self
    {
        $this->artist = $artist;

        return $this;
    }

    public function getNormalizedName(): ?string
    {
        return $this->normalizedName;
    }

    public function setNormalizedName(string $normalizedName): self
    {
        $this->normalizedName = $normalizedName;

        return $this;
    }

    /**
     * @return Collection|AudioFile[]
     */
    public function getAudioFiles(): Collection
    {
        return $this->audioFiles;
    }

    public function addAudioFile(AudioFile $audioFile): self
    {
        if (!$this->audioFiles->contains($audioFile)) {
            $this->audioFiles[] = $audioFile;
            $audioFile->setAlbum($this);
        }

        return $this;
    }

    public function removeAudioFile(AudioFile $audioFile): self
    {
        if ($this->audioFiles->contains($audioFile)) {
            $this->audioFiles->removeElement($audioFile);
            // set the owning side to null (unless already changed)
            if ($audioFile->getAlbum() === $this) {
                $audioFile->setAlbum(null);
            }
        }

        return $this;
    }
}
