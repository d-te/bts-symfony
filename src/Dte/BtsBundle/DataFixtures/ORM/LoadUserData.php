<?php

namespace Dte\BtsBundle\DataFixtures\ORM;

use Dte\BtsBundle\Entity\User;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $factory = $this->container->get('security.encoder_factory');

        $adminUser = new User();
        $adminUser->setUsername('admin');
        $adminUser->setFullname('Admin A.A.');
        $adminUser->setEmail('admin@bts.dev');
        $adminUser->setAvatar('https://avatars3.githubusercontent.com/u/3748000?v=3&s=460');
        $encoder = $factory->getEncoder($adminUser);
        $adminUser->setPassword($encoder->encodePassword('admin', $adminUser->getSalt()));
        $adminUser->addRole($this->getReference('admin-role'));

        $managerUser = new User();
        $managerUser->setUsername('manager');
        $managerUser->setFullname('Manager M.M.');
        $managerUser->setEmail('manager@bts.dev');
        $managerUser->setAvatar('https://avatars3.githubusercontent.com/u/3748001?v=3&s=460');
        $encoder = $factory->getEncoder($managerUser);
        $managerUser->setPassword($encoder->encodePassword('manager', $managerUser->getSalt()));
        $managerUser->addRole($this->getReference('manager-role'));

        $operator1User = new User();
        $operator1User->setUsername('operator1');
        $operator1User->setFullname('Operator the first O.O.');
        $operator1User->setEmail('operator1@bts.dev');
        $operator1User->setAvatar('https://avatars3.githubusercontent.com/u/3748002?v=3&s=460');
        $encoder = $factory->getEncoder($operator1User);
        $operator1User->setPassword($encoder->encodePassword('operator1', $operator1User->getSalt()));
        $operator1User->addRole($this->getReference('operator-role'));

        $operator2User = new User();
        $operator2User->setUsername('operator2');
        $operator2User->setFullname('Operator the second O.O.');
        $operator2User->setEmail('operator2@bts.dev');
        $operator2User->setAvatar('https://avatars3.githubusercontent.com/u/3748003?v=3&s=460');
        $encoder = $factory->getEncoder($operator2User);
        $operator2User->setPassword($encoder->encodePassword('operator2', $operator2User->getSalt()));
        $operator2User->addRole($this->getReference('operator-role'));

        $manager->persist($adminUser);
        $manager->persist($managerUser);
        $manager->persist($operator1User);
        $manager->persist($operator2User);
        $manager->flush();

        $this->addReference('admin-user', $adminUser);
        $this->addReference('manager-user', $managerUser);
        $this->addReference('operator1-user', $operator1User);
        $this->addReference('operator2-user', $operator2User);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}
