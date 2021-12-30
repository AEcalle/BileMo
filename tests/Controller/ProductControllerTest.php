<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


final class ProductControllerTest extends WebTestCase
{
    use testFunctions;

    public function testAuthenticationFailure(): void
    {
        $this->authenticationFailure('api/products');
    }

    public function testCollectionGet(): void
    {
        $this->collectionGet(ProductRepository::class, 'api/products');
    }

    public function testItemGet(): void
    {
        $this->itemGet(ProductRepository::class,'api/products');
    }
}