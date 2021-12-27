<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ItemsList;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ItemsListFactory
{
    private UrlGeneratorInterface $router;

    private RequestStack $requestStack;

    public function __construct(UrlGeneratorInterface $router, RequestStack $requestStack)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    public function create(Paginator $paginator): ItemsList
    {
        $itemsList = new ItemsList();

        $limit = $paginator->getQuery()->getMaxResults();
        $page = $paginator->getQuery()->getFirstResult() / $limit +1;


        $itemsList->setPage($page);
        $itemsList->setPages((int) ceil(count($paginator) / $limit));
        $itemsList->setLimit($limit);

        $links = ['href' => []];


        $links['href']['first'] = $this->generateUrl(1);
        $links['href']['next'] = $this->generateUrl(
            $page + 1 < $itemsList->getPages() ? $page + 1 : $itemsList->getPages()
        );
        $links['href']['previous'] = $this->generateUrl(
            $page - 1 > 0 ? $page - 1 : 1
        );
        $links['href']['last'] = $this->generateUrl($itemsList->getPages());

        $itemsList->setLinks($links);

        $items = ['items' => []];
        foreach ($paginator as $item)
        {
            $items['items'][] = $item;
        }
        $itemsList->setEmbedded($items);

        return $itemsList;
    }

    public function generateUrl(int $page): string
    {
        return $this->router->generate(
            $this->requestStack->getCurrentRequest()->attributes->get('_route'), 
            [
                'page' => $page,
            ], 
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}
