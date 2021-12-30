<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Repository\CustomerRepository;
use App\Service\ItemsListFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Serializer\SerializerInterface;

trait testFunctions
{
    public function authenticationFailure(string $uri): void
    {
        $client = static::createClient();

        $client->request('GET', $uri);
        $response = $client->getResponse();

        $this->assertResponseStatusCodeSame(401);
        $this->assertResponseHeaderSame('content-type', 'application/json'); 

        $this->assertEquals(
            '{"code":401,"message":"JWT Token not found"}',
            $response->getContent()
        );
    }

    public function itemGet(string $repositoryName, string $uri): void
    {
        $client = static::createClient();

        $repository = static::getContainer()->get($repositoryName);
        $serializer = static::getContainer()->get(SerializerInterface::class);

        $this->login($client);

        $item = $repository->findOneBy([]);

        $client->request('GET', $uri.'/'.$item->getId());
        $response = $client->getResponse();

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type','application/json');
        $this->assertEquals(
            $serializer->serialize($item, 'json'),
            $response->getContent()
        );
    }

    public function collectionGet(string $repositoryName, string $uri, $customerRepositoryName = null): void
    {
        $client = static::createClient();

        $repository = static::getContainer()->get($repositoryName);
        $itemsListFactory = static::getContainer()->get(ItemsListFactory::class);
        $serializer = static::getContainer()->get(SerializerInterface::class);

        $customer = null;
        if ($customerRepositoryName !== null) {
            $customerRepository = static::getContainer()->get($customerRepositoryName);
            $customer = $customerRepository->find(1);
        }

        $this->login($client);

        $client->request('GET', $uri);
        $request = $client->getRequest();

        $itemsList = $itemsListFactory->create(
            $customer === null ? $repository->paginate(1) : $repository->paginate($customer, 1),
            $request->attributes->get('_route')
        );

        $response = $client->getResponse();

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type','application/json');
        $this->assertEquals(
            $serializer->serialize($itemsList, 'json'),
            $response->getContent()
        );
    }

    public function login($client): KernelBrowser
    {
        $customerRepository = static::getContainer()->get(CustomerRepository::class);
        $testCustomer = $customerRepository->findOneByCompanyName('firstCustomer');

        return $client->loginUser($testCustomer);
    }
}