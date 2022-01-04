<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $customer = new Customer();
        $customer->setCompanyName('firstCustomer');
        $customer->setPassword($this->passwordHasher->hashPassword(
            $customer,
            'password'
        ));
        $customer2 = new Customer();
        $customer2->setCompanyName('secondCustomer');
        $customer2->setPassword($this->passwordHasher->hashPassword(
            $customer2,
            'password'
        ));

        $manager->persist($customer);
        $manager->persist($customer2);

        for ($i = 0; $i < 20; $i++)
        {
            $user = new User();
            $user->setEmail(sprintf('email%d@email.com', $i));
            $user->setFirstName(sprintf('firstName%d', $i));
            $user->setLastName(sprintf('lastName%d', $i));
            if ($i < 10) {
                $user->setCustomer($customer);
            } else {
                $user->setCustomer($customer2);
            }
            $user->setUpdatedAt(new DateTimeImmutable());
            $manager->persist($user);
        }

        for ($i = 0; $i < 100; $i++)
        {
            $product = new Product();
            $product->setBrand(sprintf('brand%d', $i));
            $product->setColor(sprintf('color%d', $i));
            $product->setDescription(sprintf('description%d', $i));
            $product->setMemory(mt_rand(1000, 10000));
            $product->setName(sprintf('name%d', $i));
            $product->setOs(sprintf('os%d', $i));
            $product->setPrice(mt_rand(100,1000));
            $product->setStock(mt_rand(0,500));
            $product->setTva(20);
            $product->setUpdatedAt(new \DateTimeImmutable());
            $manager->persist($product);
        }

        $manager->flush();
    }
}
