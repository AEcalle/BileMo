<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ItemsList;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class Pagination
{
    const LIMIT = 3;

    private UrlGeneratorInterface $router;

    private RequestStack $requestStack;

    public function __construct(UrlGeneratorInterface $router, RequestStack $requestStack)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    public function currentPage(): int
    {
        return null !== $this->requestStack->getCurrentRequest()->query->get('page') ? (int) $this->requestStack->getCurrentRequest()->query->get('page') : 1;
    }

    public function lastPage(int $nbItems): int
    {
        return (int) ceil($nbItems / self::LIMIT);
    }

    public function setLinks(ItemsList $itemsList, int $nbItems): ItemsList
    {
        $itemsList->setLinks(
            [
                'first' => $this->generateUrl(1),
                'next' => $this->generateUrl(
                    $this->currentPage() + 1 < $this->lastPage($nbItems) ? $this->currentPage() + 1 : $this->lastPage($nbItems)
                ),
                'previous' => $this->generateUrl(
                    $this->currentPage() - 1 > 0 ? $this->currentPage() - 1 : 1
                ),
                'last' => $this->generateUrl($this->lastPage($nbItems))
            ]
        );

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
