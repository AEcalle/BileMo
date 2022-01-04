<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ItemsList;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ItemsListFactory
{
    private UrlGeneratorInterface $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function create(Paginator $paginator, string $route): ItemsList
    {
        $itemsList = new ItemsList();

        $limit = $paginator->getQuery()->getMaxResults();
        $page = $paginator->getQuery()->getFirstResult() / $limit +1;


        $itemsList->setPage($page);
        $itemsList->setPages((int) ceil(count($paginator) / $limit));
        $itemsList->setLimit($limit);

        $links = ['href' => []];


        $links['href']['first'] = $this->generateUrl(1, $route);
        $links['href']['next'] = $this->generateUrl(
            $page + 1 < $itemsList->getPages() ? $page + 1 : $itemsList->getPages(),
            $route
        );
        $links['href']['previous'] = $this->generateUrl(
            $page - 1 > 0 ? $page - 1 : 1,
            $route
        );
        $links['href']['last'] = $this->generateUrl($itemsList->getPages(), 
        $route);

        $itemsList->setLinks($links);

        $items = ['items' => []];
        foreach ($paginator as $item)
        {
            $items['items'][] = $item;
        }
        $itemsList->setEmbedded($items);

        return $itemsList;
    }

    public function generateUrl(int $page, string $route): string
    {
        return $this->router->generate(
            $route, 
            [
                'page' => $page,
            ], 
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}
