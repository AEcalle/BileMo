<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

final class Pagination
{
    const LIMIT = 3;

    public function paginate(ServiceEntityRepository $repository, int $page): Paginator
    {
        $query = $repository
        ->createQueryBuilder('p')->orderBy('p.id','ASC')->getQuery();
        $paginator = new Paginator($query);

        $paginator
        ->getQuery()
        ->setFirstResult($page*self::LIMIT)
        ->setMaxResults(self::LIMIT);

        return $paginator;
    }

}
