<?php

namespace App\Repository;

use App\Entity\AudioFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * @method AudioFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method AudioFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method AudioFile[]    findAll()
 * @method AudioFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AudioFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AudioFile::class);
    }

    /**
     * @param AudioFile $audioFile
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(AudioFile $audioFile): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($audioFile);
        $entityManager->flush();
    }


    // /**
    //  * @return AudioFile[] Returns an array of AudioFile objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AudioFile
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
