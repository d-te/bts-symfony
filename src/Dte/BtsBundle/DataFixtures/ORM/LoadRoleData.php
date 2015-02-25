<?php

namespace Dte\BtsBundle\DataFixtures\ORM;

use Dte\BtsBundle\Entity\Role;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadRoleData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $adminRole = new Role();
        $adminRole->setName('Administrator');
        $adminRole->setRole('ROLE_ADMIN');

        $managerRole = new Role();
        $managerRole->setName('Manager');
        $managerRole->setRole('ROLE_MANAGER');

        $operatorRole = new Role();
        $operatorRole->setName('Operator');
        $operatorRole->setRole('ROLE_OPERATOR');

        $manager->persist($adminRole);
        $manager->persist($managerRole);
        $manager->persist($operatorRole);

        $manager->flush();

        $this->addReference('admin-role', $adminRole);
        $this->addReference('manager-role', $managerRole);
        $this->addReference('operator-role', $operatorRole);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
