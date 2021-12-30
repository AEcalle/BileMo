<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\ItemsListFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('api/products')]
final class ProductController
{
    #[Route(name:'api_products_collection_get', methods:['GET'])]
    public function collection (
        ProductRepository $productRepository, 
        SerializerInterface $serializer,
        ItemsListFactory $itemsListFactory,
        Request $request,
        ): JsonResponse
    {
        $page = null !== $request->query->get('page') ? 
        (int) $request->query->get('page') : 1;

        $productsList = $itemsListFactory->create(
            $productRepository->paginate($page),
            $request->attributes->get('_route')
        );

        return new JsonResponse(
            $serializer->serialize($productsList, 'json'),
            200,
            [],
            true
        );
    }

    #[Route('/{id}', name:'api_products_item_get', methods:['GET'])]
    public function item (
        Product $product, 
        SerializerInterface $serializer,
        ): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize(
                $product, 
                'json'
            ),
            200,
            [],
            true
        );
    }
}
