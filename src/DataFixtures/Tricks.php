<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Group;

class Tricks extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create and persist your group entities
        $group1 = new Group();
        $group1->setName('Sauts');
        $manager->persist($group1);

        $group2 = new Group();
        $group2->setName('Pas Sauts');
        $manager->persist($group2);

        // Flush the changes to the database
        $manager->flush();
    }
}
