<?php

declare(strict_types=1);

namespace App\Entity;

class ItemsList
{
    private int $page;

    private int $pages;

    private int $limit = 5;

    /**
     * @var array<string, string> $_links
     */
    private array $_links = [];

    /**
     * @var array<string, string> $_embedded
     */
    private array $_embedded = [];

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
     * @return array<string, string>
     */
    public function getLinks(): array
    {
        return $this->_links;
    }

    /**
     * @param array<string, string> $_links
     */
    public function setLinks(array $_links): void
    {
        $this->_links[] = $_links;
    }

    /**
     * @return array<string, string>
     */
    public function getEmbedded(): array
    {
        return $this->_embedded;
    }

    /**
     * @var array<string, string> $_embedded
     */
    public function setEmbedded(array $_embedded): void
    {
        $this->_embedded[] = $_embedded;
    }
}
