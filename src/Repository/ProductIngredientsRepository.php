<?php

namespace App\Repository;

use App\Entity\ProductIngredients;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductIngredients>
 *
 * @method ProductIngredients|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductIngredients|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductIngredients[]    findAll()
 * @method ProductIngredients[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductIngredientsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductIngredients::class);
    }

//    /**
//     * @return ProductIngredients[] Returns an array of ProductIngredients objects
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
//    public function findOneBySomeField($value): ?ProductIngredients
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
