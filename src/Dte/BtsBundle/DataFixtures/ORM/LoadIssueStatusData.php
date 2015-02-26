<?php

namespace Dte\BtsBundle\DataFixtures\ORM;

use Dte\BtsBundle\Entity\IssueStatus;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadIssueStatusData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $issueStatusOpen = new IssueStatus();
        $issueStatusOpen->setLabel('Open');
        $issueStatusOpen->setOrder(10);
        $manager->persist($issueStatusOpen);

        $issueStatusInProgress = new IssueStatus();
        $issueStatusInProgress->setLabel('In progress');
        $issueStatusInProgress->setOrder(20);
        $manager->persist($issueStatusInProgress);

        $issueStatusClosed = new IssueStatus();
        $issueStatusClosed->setLabel('Closed');
        $issueStatusClosed->setOrder(30);
        $manager->persist($issueStatusClosed);

        $manager->flush();

        $this->addReference('issue-status-open', $issueStatusOpen);
        $this->addReference('issue-status-in-progress', $issueStatusInProgress);
        $this->addReference('issue-status-closed', $issueStatusClosed);
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return array('Dte\BtsBundle\DataFixtures\ORM\LoadUserData');
    }
}
