<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadEventCategory
 *
 * @author michael
 */
class LoadRelationship extends Fixture implements DependentFixtureInterface {
    //put your code here
    public function load(ObjectManager $manager) {
    }

    public function getDependencies(): array {
        return array(
            LoadPerson::class,
            LoadRelationshipCategory::class,
        );
    }

}
