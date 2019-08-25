<?php

namespace App\Repository;

use App\Entity\ClipFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ClipFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClipFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClipFile[]    findAll()
 * @method ClipFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClipFileRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ClipFile::class);
    }

    // /**
    //  * @return ClipFile[] Returns an array of ClipFile objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ClipFile
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
