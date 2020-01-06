<?php

namespace App\Service;

use App\Entity\Artist;
use App\Repository\ArtistRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class ArtistManager
{
    /** @var ArtistRepository */
    protected $artistRepo;
    /**
     * @var StringHelper
     */
    protected $stringHelper;

    /**
     * ArtistManager constructor.
     * @param ArtistRepository $artistRepo
     * @param StringHelper     $stringHelper
     */
    public function __construct(ArtistRepository $artistRepo, StringHelper $stringHelper)
    {
        $this->artistRepo = $artistRepo;
        $this->stringHelper = $stringHelper;
    }

    /**
     * @param string $name
     * @return Artist
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function findByName(string $name): Artist
    {
        $normalizedName = $this->stringHelper->normalize($name, true);
        $artist = $this->artistRepo->findOneBy(['normalizedName' => $normalizedName]);
        if (null === $artist) {
            $artist = (new Artist())
                ->setName($name)
                ->setNormalizedName($normalizedName);
            $this->artistRepo->save($artist);
        }
        return $artist;
    }
}
