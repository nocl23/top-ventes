<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findTop3() {
        $req = $this->getEntityManager()->getConnection()->prepare('
            SELECT p.id, c.name, SUM(oi.quantity) as qty, SUM(oi.quantity * p.price) as price
            FROM order_items AS oi
            JOIN product AS p ON oi.product_id = p.id
            JOIN category AS c ON c.id = p.category_id
            GROUP BY p.id, c.name
            ORDER BY c.name ASC,qty DESC, price DESC
        ');

        if ($req->execute()) {
            return $req->fetchAll();
        } else {
            return NULL;
        }
    }
}
