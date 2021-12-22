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

    public function create(Paginator $paginator, int $page, int $limit): ItemsList
    {
        $itemsList = new ItemsList();

        $itemsList->setPage($page);
        $itemsList->setPages((int) ceil(count($paginator) / $limit));
        $itemsList->setLimit($limit);

        $itemsList->setLinks(
            [
                'first' => $this->generateUrl(1),
                'next' => $this->generateUrl(
                    $page + 1 < $itemsList->getPages() ? $page + 1 : $itemsList->getPages()
                ),
                'previous' => $this->generateUrl(
                    $page - 1 > 0 ? $page - 1 : 1
                ),
                'last' => $this->generateUrl($itemsList->getPages())
            ]
        );
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
