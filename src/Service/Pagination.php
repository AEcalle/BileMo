<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;

final class Pagination
{
    const LIMIT = 50;

    public function paginate(Query $query, int $page): Paginator
    {
        $paginator = new Paginator($query);

        $paginator
        ->getQuery()
        ->setFirstResult($page*self::LIMIT-self::LIMIT)
        ->setMaxResults(self::LIMIT);

        return $paginator;
    }

}
