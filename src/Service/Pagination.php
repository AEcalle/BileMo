<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Security\Core\Security;

final class Pagination
{
    const LIMIT = 3;

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function paginate(ServiceEntityRepository $repository, int $page): Paginator
    {
        $query = $repository
        ->createQueryBuilder('p')->where('p.customer = '.$this->security->getUser()->getId().'')->orderBy('p.id','ASC')->getQuery();
        $paginator = new Paginator($query);

        $paginator
        ->getQuery()
        ->setFirstResult($page*self::LIMIT-self::LIMIT)
        ->setMaxResults(self::LIMIT);

        return $paginator;
    }

}
