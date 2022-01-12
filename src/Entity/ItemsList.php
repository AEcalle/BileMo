<?php

declare(strict_types=1);

namespace App\Entity;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="itemsList")
 */
class ItemsList
{
    /**
     * @OA\Property()
     * @var int
     */
    private int $page;
    /**
     * @OA\Property()
     * @var int
     */
    private int $pages;
    /**
     * @OA\Property()
     * @var int
     */
    private int $limit = 5;

    /**
     * @OA\Property()
     * @var array{href: array<array-key, string>} $links
     */
    private array $links;

    /**
     * @OA\Property()
     * @var array{items: array<array-key, object>} $embedded
     */
    private array $embedded;

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    public function getPages(): int
    {
        return $this->pages;
    }

    public function setPages(int $pages): void
    {
        $this->pages = $pages;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @return array{href: array<array-key, string>}
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @param array{href: array<array-key, string>} $links
     */
    public function setLinks(array $links): void
    {
        $this->links = $links;
    }

    /**
     * @return array{items: array<array-key, object>}
     */
    public function getEmbedded(): array
    {
        return $this->embedded;
    }

    /**
     * @param array{items: array<array-key, object>} $embedded
     */
    public function setEmbedded(array $embedded): void
    {
        $this->embedded = $embedded;
    }
}
