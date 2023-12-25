<?php

namespace App\Repository;

use App\Entity\ProductTags;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductTags>
 *
 * @method ProductTags|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductTags|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductTags[]    findAll()
 * @method ProductTags[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductTagsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductTags::class);
    }

//    /**
//     * @return ProductTags[] Returns an array of ProductTags objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }
    public function findByProductId($productId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.productId = :val')
            ->setParameter('val', $productId)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
//    public function findOneBySomeField($value): ?ProductTags
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
