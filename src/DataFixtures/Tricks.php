<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Group;

class Tricks extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $groups = [
            'Figures de Base',
            'Sauts',
            'Grabs',
            'Rotations',
            'Inversions',
            'Rails et Boxes',
            'Slopestyle',
            'Halfpipe',
            'Freestyle Backcountry',
            'Freeride',
            'Big Air',
            'Old School',
        ];

        foreach ($groups as $groupName) {
            $group = new Group();
            $group->setName($groupName);
            $tab[] = $group;
            $manager->persist($group);
        }




        $manager->flush();
    }
}
