<?php

namespace App\Repository;

use App\Entity\WikiCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WikiCategory>
 *
 * @method WikiCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method WikiCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method WikiCategory[]    findAll()
 * @method WikiCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WikiCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WikiCategory::class);
    }

//    /**
//     * @return WikiCategory[] Returns an array of WikiCategory objects
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

//    public function findOneBySomeField($value): ?WikiCategory
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
