<?php

namespace App\Service;

use App\Entity\Album;
use App\Entity\Artist;
use App\Repository\AlbumRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class AlbumManager
{
    /** @var AlbumRepository */
    protected $albumRepo;
    /**
     * @var StringHelper
     */
    protected $stringHelper;

    /**
     * AlbumManager constructor.
     * @param AlbumRepository $albumRepo
     * @param StringHelper    $stringHelper
     */
    public function __construct(AlbumRepository $albumRepo, StringHelper $stringHelper)
    {
        $this->albumRepo = $albumRepo;
        $this->stringHelper = $stringHelper;
    }

    /**
     * @param Artist $artist
     * @param string $name
     * @return Album
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function getArtistAlbum(Artist $artist, string $name): Album
    {
        $normalizedName = $this->stringHelper->normalize($name);
        $album = $this->albumRepo->findOneBy(['normalizedName' => $normalizedName, 'artist' => $artist]);
        if (null === $album) {
            $album = (new Album())
                ->setName($name)
                ->setNormalizedName($normalizedName)
                ->setArtist($artist)
                ->setCompilation(false);
            $this->albumRepo->save($album);
        }
        return $album;
    }

    /**
     * @param string $name
     * @return Album
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function getCompilationAlbum(string $name): Album
    {
        $normalizedName = $this->stringHelper->normalize($name);
        $album = $this->albumRepo->findOneBy(['normalizedName' => $normalizedName, 'compilation' => true]);
        if (null === $album) {
            $album = (new Album())
                ->setName($name)
                ->setNormalizedName($normalizedName)
                ->setCompilation(true);
            $this->albumRepo->save($album);
        }
        return $album;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isCompilationAlbum(string $name): bool
    {
        $normalizedName = $this->stringHelper->normalize($name);
        return $this->albumRepo->findOneBy(['normalizedName' => $normalizedName, 'compilation' => true]) !== null;
    }
}
