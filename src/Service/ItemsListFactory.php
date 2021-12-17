<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ItemsList;

class ItemsListFactory
{
    private Pagination $pagination;

    public function __construct(Pagination $pagination)
    {
        $this->pagination = $pagination;
    }

    public function create(int $nbItems): ItemsList
    {
        $itemsList = $this->pagination->setLinks(new ItemsList(), $nbItems);
        $itemsList->setPage($this->pagination->currentPage());
        $itemsList->setPages($this->pagination->lastPage($nbItems));
        $itemsList->setLimit($this->pagination::LIMIT);

        return $itemsList;

    }
}
