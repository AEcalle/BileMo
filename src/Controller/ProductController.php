<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\ItemsListFactory;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security as NelmioSecurity;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('api/products')]
final class ProductController
{
    #[Route(name:'api_products_collection_get', methods:['GET'])]
/**
     * @OA\Get(summary="Get list of a products")
     * @OA\Response(
     *     response=200,
     *     description="Return products list",
     *     @OA\JsonContent(
     *        @OA\Property(property="page", description="Current page",  type="integer", example=1),
     *        @OA\Property(property="pages" ,description="number total of pages", type="integer", example=10),
     *        @OA\Property(property="limit",description="Products per page", type="integer", example=1),
     *        @OA\Property(property="_links",
     *               @OA\Property(
     *                   property="href", 
     *                   @OA\Property(property="first", type="string", example="https://127.0.0.1:8000/api/products?page=1"),
     *                   @OA\Property(property="next", type="string", example="https://127.0.0.1:8000/api/products?page=2"),
     *                   @OA\Property(property="previous", type="string", example="https://127.0.0.1:8000/api/products?page=1"),
     *                   @OA\Property(property="last", type="string", example="https://127.0.0.1:8000/api/products?page=10"),
     *                )
     *          ),
     *      @OA\Property(property="_embedded",
     *      @OA\Property(property="items",type="array",
     *        @OA\Items(
     *          @OA\Property(property="id", type="integer", example=1),
     *          @OA\Property(property="name", type="string", example="name0"),
     *          @OA\Property(property="description", type="string", example="description0"),
     *          @OA\Property(property="price", type="integer", example=335),
     *          @OA\Property(property="tva", type="integer", example=20),
     *          @OA\Property(property="color", type="string", example="color0"),
     *          @OA\Property(property="brand", type="string", example="brand0"),
     *          @OA\Property(property="os", type="string", example="os0"),
     *          @OA\Property(property="memory", type="integer", example=8373),
     *          @OA\Property(property="stock", type="integer", example=340),
     *          @OA\Property(property="updatedAt", type="string", format="date-time"),
     *          @OA\Property(property="_links", 
     *              @OA\Property(property="self", type="string", example="https://127.0.0.1:8000/api/products/1"),
     *          ),
     *       ),
     *     )
     *   )
     *  )
     * )
     * @OA\Response(
     *     response=401,
     *     description="JWT Token not found",    
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Numéro de page",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Products")
     * 
     * @NelmioSecurity(name="Bearer")
     */
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
    /**
     * @OA\Get(summary="Get details of a product")
     * @OA\Response(
     *     response=200,
     *     description="Return a product",
     *     @Model(type=Product::class)
     * )
     * @OA\Response(
     *    response=401,
     *    description="JWT Token not found",
     * )
     * @OA\Tag(name="Products")
     * 
     * @NelmioSecurity(name="Bearer")
     */
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
