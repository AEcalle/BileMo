<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LoginTest extends WebTestCase
{
    public function testIfLoginReturnsToken(): void
    {
        $client = static::createClient();
        $client->request(
          'POST',
          'api/login_check',
          array(),
          array(),
          array('CONTENT_TYPE' => 'application/json'),
          json_encode(array(
            'username' => 'firstCustomer',
            'password' => 'password',
            ))
          );

        $data = json_decode($client->getResponse()->getContent(), true);
    
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        $this->assertResponseStatusCodeSame(200);

    }
}