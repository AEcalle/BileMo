<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\ItemsListFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
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
        $response = new JsonResponse();
        $response->setLastModified($productRepository->findOneBy([],['updatedAt' => 'DESC'])->getUpdatedAt());
        $response->setPublic();

        if ($response->isNotModified($request)) {
            return $response;
        }

        $page = null !== $request->query->get('page') ? 
        (int) $request->query->get('page') : 1;

        $productsList = $itemsListFactory->create(
            $productRepository->paginate($page),
            $request->attributes->get('_route')
        );
        $response->setJson( $serializer->serialize($productsList, 'json'));
        $response->setStatusCode(200);
        return $response;
    }

    #[Cache(lastModified: 'product.getUpdatedAt()', public: true)]
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
