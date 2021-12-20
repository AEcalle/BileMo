<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Serializer\PrefixNameConverter;
use App\Serializer\ProductNormalizer;
use App\Service\ItemsListFactory;
use App\Service\Pagination;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('api/products')]
final class ProductController
{
    #[Route(name:'api_products_collection_get', methods:['GET'])]
    public function collection (
        ProductRepository $productRepository, 
        ProductNormalizer $productNormalizer,
        PrefixNameConverter $nameConverter,
        Pagination $pagination,
        ItemsListFactory $itemsListFactory
        ): JsonResponse
    {
        $productsList = $itemsListFactory->create($productRepository->count([]));

        $products = $productRepository->findBy(
            [],
            [],
            $pagination::LIMIT,
            ($pagination->currentPage()-1)*$pagination::LIMIT
        );

        foreach ($products as $product)
        {
           $productsList->setEmbedded($productNormalizer->normalize($product));
        }

        $serializer = new Serializer(
            [new ObjectNormalizer(null, $nameConverter)], 
            [new JsonEncoder()]
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
        ProductNormalizer $productNormalizer,
        ): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize(
                $productNormalizer->normalize($product), 
                'json'
            ),
            200,
            [],
            true
        );
    }
}
