<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Memory;

class MemoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $memory = new Memory();
        $memory->setTitle("Rendez-vous CEF");
        $memory->setDescription("Le 15 novembre, en visio à 13h30");
        $manager->persist($memory);

        $memory = new Memory();
        $memory->setTitle("Acheter cadeaux de Noël");
        $manager->persist($memory);

        $manager->flush();
    }
}