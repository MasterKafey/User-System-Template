<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = (new User())
            ->setEmail('jean.marius@dyosis.com')
            ->setPlainPassword('admin')
        ;

        $manager->persist($user);
        $manager->flush();
    }
}
