<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UserControllerTest extends WebTestCase
{
    use testFunctions;

    public function testAuthenticationFailure(): void
    {
        $this->authenticationFailure('api/users');
    }

    public function testCollectionGet(): void
    {
        $this->collectionGet(UserRepository::class, 'api/users', CustomerRepository::class);
    }

    public function testItemGet(): void
    {
        $this->itemGet(UserRepository::class,'api/users');
    }

    public function testCollectionPost()
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);

        $this->login($client);

        $client->request('POST', 'api/users', [], [], ['content-type' => 'application/json'], '{
            "email" : "email@email.com",
            "firstName" : "John",
            "lastName" : "Doe"
        }');

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $this->assertNotNull($userRepository->findByEmail('email@email.com'));
    }

    
    public function testItemPut(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);

        $id = $userRepository->findOneBy([])->getId();
        $this->login($client);

        $client->request('PUT', 'api/users/'.$id, [], [], ['content-type' => 'application/json'], '{
            "email" : "email@email.com",
            "firstName" : "updateName",
            "lastName" : "Doe"
        }');

        $this->assertResponseStatusCodeSame(204);

        $this->assertEquals('updateName',$userRepository->find($id)->getFirstName());
    }


    public function testItemDelete(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);

        $id = $userRepository->findOneBy([])->getId();
        $this->login($client);

        $client->request('DELETE', 'api/users/'.$id);

        $this->assertResponseStatusCodeSame(204);

        $this->assertNull($userRepository->find($id));
    }
}