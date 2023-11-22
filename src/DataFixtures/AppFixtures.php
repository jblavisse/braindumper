<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Entity\Memory;
use App\Entity\Type;
use App\Entity\Category;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadTypes($manager);
        $this->loadCategories($manager);
        $this->loadMemories($manager);
    }


    public function loadUsers(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $userReferenceKey = 'user_' . $i;
            $user = new User();
            $user->setFirstname($faker->username);
            $user->setEmail($faker->email);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    'password'
                )
            );
            $manager->persist($user);
            $this->addReference($userReferenceKey, $user);
            $manager->flush();
        }

    }

    public function loadTypes(ObjectManager $manager)
    {
        $faker = Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $typeKey = 'type_' . $i;
            $type = new Type();
            $type->setName($faker->name);

            $manager->persist($type);
            $this->addReference($typeKey, $type);
            $manager->flush();
        }
    }

    public function loadMemories(ObjectManager $manager)
    {
        $faker = Factory::create();
        for ($j = 1; $j < 10; $j++) {
            $typesKey = 'type_' . $j;
            $userReferenceKey = 'user_' . $j;

            $type = $this->getReference($typesKey);
            $user = $this->getReference($userReferenceKey);
            for ($i = 1; $i < 10; $i++) {
                $categoryKey = 'category_' . $i;
                if ($this->hasReference($categoryKey)) {
                    $category = $this->getReference($categoryKey);
                    $memory = new Memory();
                    $memory->setTitle($faker->title);
                    // $memory->setDescription($faker->description);
                    $memory->setUser($user);
                    $memory->setType($type);
                    $memory->setCategory($category);

                    $manager->persist($memory);
                }
            }
        }
        $manager->flush();
    }


    public function loadCategories(ObjectManager $manager)
    {
        $faker = Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $memoriesKey = 'category_' . $i;
            $category = new Category();
            $category->setName($faker->name);

            $manager->persist($category);
            $this->addReference($memoriesKey, $category);

        }
        $manager->flush();
    }
}