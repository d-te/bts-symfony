<?php

namespace Dte\BtsBundle\DataFixtures\ORM;

use Dte\BtsBundle\Entity\Comment;
use Dte\BtsBundle\Entity\Issue;
use Dte\BtsBundle\Entity\IssueTaskType;
use Dte\BtsBundle\Entity\Project;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadProjectsData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->project1($manager);
        $this->project2($manager);

        $manager->flush();
    }

    /**
     * Add first test project
     * @param  ObjectManager $manager
     */
    public function project1(ObjectManager $manager)
    {
        $project = new Project();
        $project->setCode('BTS');
        $project->setLabel('Bug tracking system - Academic');
        $project->setSummary('Academic project on symfony');
        $project->addMember($this->getReference('admin-user'));
        $project->addMember($this->getReference('manager-user'));
        $project->addMember($this->getReference('operator1-user'));
        $manager->persist($project);

        $issue1 = new Issue();
        $issue1->setProject($project);
        $issue1->setSummary('Add manager of systems guides');
        $issue1->setDescription('Add ability to manage system guides: priorities, statuses, resolutions');
        $issue1->setType(IssueTaskType::STORY_TYPE);
        $issue1->setReporter($this->getReference('manager-user'));
        $issue1->setAssignee($this->getReference('operator1-user'));
        $issue1->setPriority($this->getReference('issue-priority-major'));
        $issue1->setStatus($this->getReference('issue-status-open'));
        $manager->persist($issue1);

        $issue2 = new Issue();
        $issue2->setProject($project);
        $issue2->setSummary('Add manager of priorities');
        $issue2->setDescription('Add ability to manage issue priorities');
        $issue2->setType(IssueTaskType::SUBTASK_TYPE);
        $issue2->setParent($issue1);
        $issue2->setReporter($this->getReference('manager-user'));
        $issue2->setAssignee($this->getReference('operator1-user'));
        $issue2->setPriority($this->getReference('issue-priority-major'));
        $issue2->setStatus($this->getReference('issue-status-in-progress'));
        $manager->persist($issue2);

        $issue3 = new Issue();
        $issue3->setProject($project);
        $issue3->setSummary('Add manager of statuses');
        $issue3->setDescription('Add ability to manage issue statuses');
        $issue3->setType(IssueTaskType::SUBTASK_TYPE);
        $issue3->setParent($issue1);
        $issue3->setReporter($this->getReference('manager-user'));
        $issue3->setAssignee($this->getReference('operator1-user'));
        $issue3->setPriority($this->getReference('issue-priority-major'));
        $issue3->setStatus($this->getReference('issue-status-open'));
        $manager->persist($issue3);

        $issue4 = new Issue();
        $issue4->setProject($project);
        $issue4->setSummary('Add manager of resolutions');
        $issue4->setDescription('Add ability to manage issue resolutions');
        $issue4->setType(IssueTaskType::SUBTASK_TYPE);
        $issue4->setParent($issue1);
        $issue4->setReporter($this->getReference('manager-user'));
        $issue4->setAssignee($this->getReference('operator1-user'));
        $issue4->setPriority($this->getReference('issue-priority-major'));
        $issue4->setStatus($this->getReference('issue-status-open'));
        $manager->persist($issue4);

        $issue5 = new Issue();
        $issue5->setProject($project);
        $issue5->setSummary('Prepare server to deploying the project');
        $issue5->setDescription('Make all preparations to deploy the project');
        $issue5->setType(IssueTaskType::TASK_TYPE);
        $issue5->setReporter($this->getReference('manager-user'));
        $issue5->setAssignee($this->getReference('admin-user'));
        $issue5->setPriority($this->getReference('issue-priority-critical'));
        $issue5->setStatus($this->getReference('issue-status-in-progress'));
        $manager->persist($issue5);

        $comment1 = new Comment();
        $comment1->setBody('Please add to the task required settings for web server');
        $comment1->setUser($this->getReference('admin-user'));
        $comment1->setIssue($issue5);
        $manager->persist($comment1);

        $comment2 = new Comment();
        $comment2->setBody('I\'ll add them tommorow after the project meeting');
        $comment2->setUser($this->getReference('manager-user'));
        $comment2->setIssue($issue5);
        $manager->persist($comment2);
    }

    /**
     * Add second test project
     * @param  ObjectManager $manager
     */
    public function project2(ObjectManager $manager)
    {
        $project = new Project();
        $project->setCode('CRM');
        $project->setLabel('CRM - Academic project');
        $project->setSummary('Academic project on crm framework');
        $project->addMember($this->getReference('admin-user'));
        $project->addMember($this->getReference('manager-user'));
        $project->addMember($this->getReference('operator1-user'));
        $project->addMember($this->getReference('operator2-user'));
        $manager->persist($project);

        $issue1 = new Issue();
        $issue1->setProject($project);
        $issue1->setSummary('Investigation task for Operator 1');
        $issue1->setDescription('Make investigation of crm documentation');
        $issue1->setType(IssueTaskType::TASK_TYPE);
        $issue1->setReporter($this->getReference('manager-user'));
        $issue1->setAssignee($this->getReference('operator1-user'));
        $issue1->setPriority($this->getReference('issue-priority-major'));
        $issue1->setStatus($this->getReference('issue-status-open'));
        $manager->persist($issue1);

        $issue1 = new Issue();
        $issue1->setProject($project);
        $issue1->setSummary('Investigation task for Operator 2');
        $issue1->setDescription('Make investigation of crm documentation');
        $issue1->setType(IssueTaskType::TASK_TYPE);
        $issue1->setReporter($this->getReference('manager-user'));
        $issue1->setAssignee($this->getReference('operator2-user'));
        $issue1->setPriority($this->getReference('issue-priority-major'));
        $issue1->setStatus($this->getReference('issue-status-open'));
        $manager->persist($issue1);
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return array(
            'Dte\BtsBundle\DataFixtures\ORM\LoadIssuePriorityData',
            'Dte\BtsBundle\DataFixtures\ORM\LoadIssueResolutionData',
            'Dte\BtsBundle\DataFixtures\ORM\LoadIssueStatusData',
        );
    }
}
