<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\User;
use App\Service\Pagination;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
final class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function paginate(Customer $customer, int $page): Paginator
    {
        $query = $this->createQueryBuilder('u')->where('u.customer = '.$customer->getId().'')->orderBy('u.id','ASC')->getQuery();

        $pagination = new Pagination();

        return $pagination->paginate($query, $page);
    }
}
