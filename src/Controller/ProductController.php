<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('api/products')]
final class ProductController
{
    #[Route(name:'api_products_collection_get', methods:['GET'])]
    public function collection (
        ProductRepository $productRepository, 
        SerializerInterface $serializer
        ): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($productRepository->findAll(), 'json'),
            200,
            [],
            true
        );
    }

    #[Route('/{id}', name:'api_products_item_get', methods:['GET'])]
    public function item (
        Product $product, 
        SerializerInterface $serializer
        ): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($product, 'json'),
            200,
            [],
            true
        );
    }
}
