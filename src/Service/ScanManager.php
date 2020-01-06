<?php

namespace App\Service;

use App\Entity\AudioFile;
use App\Repository\ArtistRepository;
use App\Repository\AudioFileRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class ScanManager implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var ID3TagManager */
    protected $id3TagManager;

    /** @var ArtistRepository */
    protected $artistRepo;

    /** @var AudioFileRepository */
    protected $audioFileRepo;
    /**
     * @var AudioFileFactory
     */
    protected $factory;

    /**
     * ScanManager constructor.
     * @param ID3TagManager       $id3TagManager
     * @param ArtistRepository    $artistRepo
     * @param AudioFileRepository $audioFileRepo
     * @param AudioFileFactory    $factory
     */
    public function __construct(ID3TagManager $id3TagManager, ArtistRepository $artistRepo, AudioFileRepository $audioFileRepo, AudioFileFactory $factory)
    {
        $this->id3TagManager = $id3TagManager;
        $this->artistRepo = $artistRepo;
        $this->audioFileRepo = $audioFileRepo;
        $this->factory = $factory;
    }

    /**
     * @param Finder $finder
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws \Throwable
     */
    public function scan(Finder $finder): void
    {
        /** @var SplFileInfo $file */
        foreach($finder->files() as $file) {
            if (null === $this->audioFileRepo->findOneBy(['path' => $file->getRealPath()])) {
                try {
                    $audioFile = $this->factory->fromFile($file);
                    if (null === $audioFile) {
                        continue;
                    }
                    $this->audioFileRepo->save($audioFile);
                } catch (\Throwable $e) {
                    $this->logger->error(sprintf('File: %s ==> error: %s', $file->getRealPath(), $e->getMessage()));
                    throw $e;
                }
            }
        }
    }
}
