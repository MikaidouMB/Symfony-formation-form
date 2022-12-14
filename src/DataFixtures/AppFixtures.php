<?php

namespace App\DataFixtures;

use App\Entity\Formation;
use App\Entity\User;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(UserPasswordHasherInterface $hasher)
    {
       $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {

        $faker = Faker\Factory::create();

        for($j = 1; $j <= 10; $j++){
            $user = new User();
            
            $user->setEmail($faker->email);
            $user->setPassword($this->hasher->hashPassword($user,'00000000'));
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setCreatedAt(new DateTimeImmutable('now'));
            $user->setPhoneNumber('0709863256');
            
            $formation = new Formation();

            $formation->setUser($user);
            $formation->setName($faker->name);
            $formation->setCreatedAt(new DateTimeImmutable('now'));

            $manager->persist($user);
            
            $manager->persist($formation);
        }
        $manager->flush();
    }

}
