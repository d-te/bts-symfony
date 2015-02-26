<?php

namespace Dte\BtsBundle\DataFixtures\ORM;

use Dte\BtsBundle\Entity\IssuePriority;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadIssuePriorityData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $issuePriorityTrivial = new IssuePriority();
        $issuePriorityTrivial->setLabel('Trivial');
        $issuePriorityTrivial->setOrder(10);
        $manager->persist($issuePriorityTrivial);

        $issuePriorityMinor = new IssuePriority();
        $issuePriorityMinor->setLabel('Minor');
        $issuePriorityMinor->setOrder(20);
        $manager->persist($issuePriorityMinor);

        $issuePriorityMajor = new IssuePriority();
        $issuePriorityMajor->setLabel('Major');
        $issuePriorityMajor->setOrder(30);
        $manager->persist($issuePriorityMajor);

        $issuePriorityCritical = new IssuePriority();
        $issuePriorityCritical->setLabel('Critical');
        $issuePriorityCritical->setOrder(40);
        $manager->persist($issuePriorityCritical);

        $issuePriorityBlocker = new IssuePriority();
        $issuePriorityBlocker->setLabel('Blocker');
        $issuePriorityBlocker->setOrder(50);
        $manager->persist($issuePriorityBlocker);

        $manager->flush();

        $this->addReference('issue-priority-trivial', $issuePriorityTrivial);
        $this->addReference('issue-priority-minor', $issuePriorityMinor);
        $this->addReference('issue-priority-major', $issuePriorityMajor);
        $this->addReference('issue-priority-critical', $issuePriorityCritical);
        $this->addReference('issue-priority-blocker', $issuePriorityBlocker);
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return array('Dte\BtsBundle\DataFixtures\ORM\LoadUserData');
    }
}
