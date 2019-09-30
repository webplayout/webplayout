<?php

namespace App\DataFixtures;

use App\Entity\Setting;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class SettingFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $setting = new Setting();

        $setting
            ->setName('logo')
            ->setValue('');
        
        $manager->persist($setting);
        $manager->flush();
    }
}
