<?php

namespace App\Repository;

use App\Entity\WikiImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WikiImage>
 *
 * @method WikiImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method WikiImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method WikiImage[]    findAll()
 * @method WikiImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WikiImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WikiImage::class);
    }

//    /**
//     * @return WikiImage[] Returns an array of WikiImage objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?WikiImage
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
