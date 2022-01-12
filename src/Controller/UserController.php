<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\ItemsListFactory;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security as NelmioSecurity;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('api/users')]
final class UserController
{
    #[Route(name:'api_users_collection_get', methods:['GET'])]
    /**
     * @OA\Get(summary="Get list of a users")
     * @OA\Response(
     *     response=200,
     *     description="Return users list",
     *     @OA\JsonContent(
     *        @OA\Property(property="page", description="Current page",  type="integer", example=1),
     *        @OA\Property(property="pages" ,description="number total of pages", type="integer", example=10),
     *        @OA\Property(property="limit",description="Users per page", type="integer", example=1),
     *        @OA\Property(property="_links",
     *               @OA\Property(
     *                   property="href", 
     *                   @OA\Property(property="first", type="string", example="https://127.0.0.1:8000/api/users?page=1"),
     *                   @OA\Property(property="next", type="string", example="https://127.0.0.1:8000/api/users?page=2"),
     *                   @OA\Property(property="previous", type="string", example="https://127.0.0.1:8000/api/users?page=1"),
     *                   @OA\Property(property="last", type="string", example="https://127.0.0.1:8000/api/users?page=10"),
     *                )
     *          ),
     *      @OA\Property(property="_embedded",
     *      @OA\Property(property="items",type="array",
     *        @OA\Items(
     *          @OA\Property(property="id", type="integer", example=1),
     *          @OA\Property(property="email", type="string", example="email1@email.com"),
     *          @OA\Property(property="firstName", type="string", example="firstName1"),
     *          @OA\Property(property="lastName", type="string", example="lastName1"),
     *          @OA\Property(property="updatedAt", type="string", format="date-time"),
     *          @OA\Property(property="_links", 
     *              @OA\Property(property="self", type="string", example="https://127.0.0.1:8000/api/users/1"),
     *              @OA\Property(property="post", type="string", example="https://127.0.0.1:8000/api/users"),
     *              @OA\Property(property="put", type="string", example="https://127.0.0.1:8000/api/users/1"),
     *              @OA\Property(property="delete", type="string", example="https://127.0.0.1:8000/api/users/1"),
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
     * @OA\Tag(name="Users")
     * 
     * @NelmioSecurity(name="Bearer")
     */
    public function collection (
        UserRepository $userRepository, 
        SerializerInterface $serializer,
        ItemsListFactory $itemsListFactory,
        Request $request,
        Security $security,
        ): JsonResponse
    {
        $response = new JsonResponse();
        $response->setEtag(
                    $userRepository->findOneBy([], ['updatedAt' => 'DESC'])
                        ->getUpdatedAt()
                            ->format("Y-m-dH:i:s").$security->getUser()->getUserIdentifier()
                    );
        $response->setPublic();

        if ($response->isNotModified($request)) {
            return $response;
        }

        $page = null !== $request->query->get('page') ? 
        (int) $request->query->get('page') : 1;

        $usersList = $itemsListFactory->create(
            $userRepository->paginate($security->getUser(), $page),
            $request->attributes->get('_route')
        );
        $response->setJson($serializer->serialize(
            $usersList, 
            'json',
        ));
        $response->setStatusCode(200);        

        return $response;
    }

    /**
     * @OA\Post(summary="Add a user")
     * @OA\RequestBody(
     *     description="The new user to create",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="application/Json",
     *         @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *                 property="email",
     *                 description="Email adress",
     *                 type="string",
     *                 example="email@email.com"
     *             ),
     *             @OA\Property(
     *                 property="firstName",
     *                 description="User's firstname",
     *                 type="string",
     *                 example="John"
     *             ),
     *             @OA\Property(
     *                 property="lastName",
     *                 description="User's lastname",
     *                 type="string",
     *                 example="Doe"
     *             ),
     *         )
     *     )
     * )
     * @OA\Response(
     *    response=201,
     *    description="Return a user",
     *    @Model(type=User::class)
     * )
     *  @OA\Response(
     *    response=422,
     *    description="Unprocessable Entity",
     * )
     * @OA\Response(
     *     response=401,
     *     description="JWT Token not found",    
     * )
     * @OA\Tag(name="Users")
     * 
     * @NelmioSecurity(name="Bearer")
     */
    #[Route(name:'api_users_collection_post', methods:['POST'])]
    public function post (
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator,
        Security $security,
        ): JsonResponse
    {
        $user = $serializer->deserialize(
            $request->getContent(),
            User::class, 
            'json'
        );

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            return new JsonResponse(
                $serializer->serialize($errors,'json'), 
                422, 
                [], 
                true
            );
        }

        $user->setCustomer($security->getUser());
        $user->setUpdatedAt(new \DateTimeImmutable());
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(
            $serializer->serialize(
                $user,
                'json', 
                ['groups' => '*']
            ),
            201,
            ['Location' => $urlGenerator->generate('api_users_item_get', ['id' => $user->getId()])],
            true
        );
    }

    /**
    * @OA\Put(summary="Update a user")
     * @OA\RequestBody(
     *     description="The user to update",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="application/Json",
     *         @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *                 property="email",
     *                 description="Email adress",
     *                 type="string",
     *                 example="email@email.com"
     *             ),
     *             @OA\Property(
     *                 property="firstName",
     *                 description="User's firstname",
     *                 type="string",
     *                 example="John"
     *             ),
     *             @OA\Property(
     *                 property="lastName",
     *                 description="User's lastname",
     *                 type="string",
     *                 example="Doe"
     *             ),
     *         )
     *     )
     * )
    * @OA\Response(
    *    response=204,
    *    description="No content",
    * )
    *  @OA\Response(
    *    response=422,
    *    description="Unprocessable Entity",
    * )
    * @OA\Response(
    *    response=404,
    *    description="The ressource requested doesn't exist"
    * ),
    * @OA\Response(
    *    response=401,
    *    description="JWT Token not found",
    * )
     * @OA\Tag(name="Users")
     * 
     * @NelmioSecurity(name="Bearer")
     */
    #[IsGranted(subject: 'user', statusCode: 404)]
    #[Route('/{id}', name:'api_users_item_put', methods:['PUT'])]
    public function put(
        User $user,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $serializer->deserialize(
            $request->getContent(),
            User::class, 
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $user]
        );

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            return new JsonResponse(
                $serializer->serialize($errors,'json'), 
                422, 
                [], 
                true
            );
        }
        $user->setUpdatedAt(new \DateTimeImmutable());
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(
            null,
            204
        );
    }

    /**
    * @OA\Get(summary="Get details of a user")
    * @OA\Response(
    *     response=200,
    *     description="Return a user",
    *     @Model(type=User::class)
    * )
    * @OA\Response(
    *    response=404,
    *    description="The ressource requested doesn't exist"
    * ),
    * @OA\Response(
    *    response=401,
    *    description="JWT Token not found",
    * )
    * @OA\Tag(name="Users")
    *
    * @NelmioSecurity(name="Bearer")
    */
    #[IsGranted(subject: 'user', statusCode: 404)]
    #[Cache(lastModified: 'user.getUpdatedAt()')]
    #[Route('/{id}', name:'api_users_item_get', methods:['GET'])]
    public function item (
        User $user, 
        SerializerInterface $serializer,
        ): JsonResponse
    {

        return new JsonResponse(
            $serializer->serialize(
                $user,
                'json',
                ['groups' => '*']
            ),
            200,
            [],
            true
        );
    }

/**
    * @OA\Delete(
    *     summary="Delete a user",
    *     @OA\Parameter(
    *         description="User id to delete",
    *         in="path",
    *         name="id",
    *         required=true,
    *         @OA\Schema(
    *             type="integer",
    *         )
    *     ),
    * )
    * @OA\Response(
    *    response=204,
    *    description="No content"
    * ),
    * @OA\Response(
    *    response=404,
    *    description="The ressource requested doesn't exist"
    * ),
    * @OA\Response(
    *    response=401,
    *    description="JWT Token not found",
    * )
    * @OA\Tag(name="Users")
*/
    #[IsGranted(subject: 'user', statusCode: 404)]
    #[Route('/{id}', name:'api_users_item_delete', methods:['DELETE'])]
    public function delete (
        User $user, 
        EntityManagerInterface $entityManager,
        ): JsonResponse
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(
            null,
            204
        );
    }
}
