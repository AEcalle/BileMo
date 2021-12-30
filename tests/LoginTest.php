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
            'api/login_check', [],
            [],
            ['content-type' => 'application/json'],'{
                "username" => "firstCustomer",
                "password" => "password"
            }',
        );

        $this->assertResponseStatusCodeSame(200);

    }
}