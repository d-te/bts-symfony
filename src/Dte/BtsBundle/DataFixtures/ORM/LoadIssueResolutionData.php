<?php

namespace Dte\BtsBundle\DataFixtures\ORM;

use Dte\BtsBundle\Entity\IssueResolution;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadIssueResolutionData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $issueResolutionFixed = new IssueResolution();
        $issueResolutionFixed->setLabel('Fixed');
        $issueResolutionFixed->setOrder(10);
        $manager->persist($issueResolutionFixed);

        $issueResolutionDuplicate = new IssueResolution();
        $issueResolutionDuplicate->setLabel('Duplicate');
        $issueResolutionDuplicate->setOrder(20);
        $manager->persist($issueResolutionDuplicate);

        $issueResolutionWontFix = new IssueResolution();
        $issueResolutionWontFix->setLabel('Won\'t Fix');
        $issueResolutionWontFix->setOrder(30);
        $manager->persist($issueResolutionWontFix);

        $issueResolutionIncomplete = new IssueResolution();
        $issueResolutionIncomplete->setLabel('Incomplete');
        $issueResolutionIncomplete->setOrder(40);
        $manager->persist($issueResolutionIncomplete);

        $issueResolutionReproduce = new IssueResolution();
        $issueResolutionReproduce->setLabel('Cannot reproduce');
        $issueResolutionReproduce->setOrder(50);
        $manager->persist($issueResolutionReproduce);

        $issueResolutionDesigned = new IssueResolution();
        $issueResolutionDesigned->setLabel('Works as designed');
        $issueResolutionDesigned->setOrder(60);
        $manager->persist($issueResolutionDesigned);

        $manager->flush();

        $this->addReference('issue-resolution-fixed', $issueResolutionFixed);
        $this->addReference('issue-resolution-duplicate', $issueResolutionDuplicate);
        $this->addReference('issue-resolution-wont-fix', $issueResolutionWontFix);
        $this->addReference('issue-resolution-incomplete', $issueResolutionIncomplete);
        $this->addReference('issue-resolution-reproduce', $issueResolutionReproduce);
        $this->addReference('issue-resolution-designed', $issueResolutionDesigned);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 4;
    }
}
