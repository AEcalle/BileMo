<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('api/users')]
final class UserController
{
    #[Route(name:'api_users_collection_get', methods:['GET'])]
    public function collection (
        UserRepository $userRepository, 
        SerializerInterface $serializer
        ): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize(
                $userRepository->findBy(['customer'=>1]), 
                'json', 
                ['groups' => 'get']
            ),
            200,
            [],
            true
        );
    }

    #[Route(name:'api_users_collection_post', methods:['POST'])]
    public function post (
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator,
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

        $user->setCustomer($entityManager->getRepository(Customer::class)->findOneBy([]));
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(
            $serializer->serialize(
                $user, 
                'json', 
                ['groups' => 'get']
            ),
            201,
            ['Location' => $urlGenerator->generate('api_users_item_get', ['id' => $user->getId()])],
            true
        );
    }

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

        $entityManager->flush();

        return new JsonResponse(
            null,
            204
        );
    }

    #[Route('/{id}', name:'api_users_item_get', methods:['GET'])]
    public function item (
        User $user, 
        SerializerInterface $serializer
        ): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($user, 'json'),
            200,
            [],
            true
        );
    }

    #[Route('/{id}', name:'api_users_item_delete', methods:['DELETE'])]
    public function delete (
        User $user, 
        EntityManagerInterface $entityManager
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
