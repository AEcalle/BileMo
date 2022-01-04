<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\ItemsListFactory;
use Doctrine\ORM\EntityManagerInterface;
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
    public function collection (
        UserRepository $userRepository, 
        SerializerInterface $serializer,
        ItemsListFactory $itemsListFactory,
        Request $request,
        Security $security,
        ): JsonResponse
    {
        $response = new JsonResponse();
        $response->setLastModified($userRepository->findOneBy([],['updatedAt' => 'DESC'])->getUpdatedAt());

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
