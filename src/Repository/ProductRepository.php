<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use App\Service\Pagination;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
final class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function paginate(int $page): Paginator
    {
        $query = $this->createQueryBuilder('p')->orderBy('p.id','ASC')->getQuery();

        $pagination = new Pagination();

        return $pagination->paginate($query, $page);
    }
}
