<?php

namespace Dte\BtsBundle\Tests\Unit\Entity;

use Dte\BtsBundle\Entity\Comment;
use Dte\BtsBundle\Entity\Issue;
use Dte\BtsBundle\Entity\IssuePriority;
use Dte\BtsBundle\Entity\IssueResolution;
use Dte\BtsBundle\Entity\IssueStatus;
use Dte\BtsBundle\Entity\Project;
use Dte\BtsBundle\Entity\User;

class IssueTest extends \PHPUnit_Framework_TestCase
{

    public function testSummarySetterGetter()
    {
        $entity = new Issue();

        $this->assertNull($entity->getSummary());

        $entity->setSummary('Summary');

        $this->assertEquals('Summary', $entity->getSummary());
    }

    public function testCodeSetterGetter()
    {
        $entity = new Issue();

        $this->assertNull($entity->getCode());

        $entity->setCode('Code');

        $this->assertEquals('Code', $entity->getCode());
    }

    public function testDescriptionSetterGetter()
    {
        $entity = new Issue();

        $this->assertNull($entity->getDescription());

        $entity->setDescription('Description');

        $this->assertEquals('Description', $entity->getDescription());
    }

    public function testTypeSetterGetter()
    {
        $entity = new Issue();

        $this->assertNull($entity->getType());

        $entity->setType(1);

        $this->assertEquals(1, $entity->getType());
    }

    public function testReporterSetterGetter()
    {
        $entity = new Issue();

        $this->assertNull($entity->getReporter());

        $user = new User();

        $entity->setReporter($user);

        $this->assertEquals($user, $entity->getReporter());
    }

    public function testAssigneeSetterGetter()
    {
        $entity = new Issue();

        $this->assertNull($entity->getAssignee());

        $user = new User();

        $entity->setAssignee($user);

        $this->assertEquals($user, $entity->getAssignee());
    }

    public function testProjectSetterGetter()
    {
        $entity = new Issue();

        $this->assertNull($entity->getProject());

        $project = new Project();

        $entity->setProject($project);

        $this->assertEquals($project, $entity->getProject());
    }

    public function testPrioritySetterGetter()
    {
        $entity = new Issue();

        $this->assertNull($entity->getPriority());

        $priority = new IssuePriority();

        $entity->setPriority($priority);

        $this->assertEquals($priority, $entity->getPriority());
    }

    public function testStatusSetterGetter()
    {
        $entity = new Issue();

        $this->assertNull($entity->getStatus());

        $status = new IssueStatus();

        $entity->setStatus($status);

        $this->assertEquals($status, $entity->getStatus());
    }

    public function testResolutionSetterGetter()
    {
        $entity = new Issue();

        $this->assertNull($entity->getResolution());

        $resolution = new IssueResolution();

        $entity->setResolution($resolution);

        $this->assertEquals($resolution, $entity->getResolution());
    }

    public function testparentSetterGetter()
    {
        $entity = new Issue();

        $this->assertNull($entity->getparent());

        $parent = new Issue();

        $entity->setparent($parent);

        $this->assertEquals($parent, $entity->getparent());
    }

    public function testChildrenGetter()
    {
        $entity = new Issue();

        $this->assertEquals(array(), $entity->getChildren());
    }

    public function testCommentsGetter()
    {
        $entity = new Issue();

        $this->assertEquals(array(), $entity->getComments());
    }

    public function testActivitiesGetter()
    {
        $entity = new Issue();

        $this->assertEquals(array(), $entity->getActivities());
    }

    public function testCollaboratorsGetter()
    {
        $entity = new Issue();

        $this->assertEquals(array(), $entity->getCollaborators());
    }

    public function testAddCollaboratorFunction()
    {
        $issue = new Issue();

        $this->assertCount(0, $issue->getCollaborators());

        $user = new User();

        $issue->addCollaborator($user);

        $this->assertCount(1, $issue->getCollaborators());
    }

    public function testRemoveCollaboratorFunction()
    {
        $issue = new Issue();

        $this->assertCount(0, $issue->getCollaborators());

        $user = new User();

        $issue->addCollaborator($user);

        $this->assertCount(1, $issue->getCollaborators());

        $issue->removeCollaborator($user);

        $this->assertCount(0, $issue->getCollaborators());
    }

    public function testHasCollaboratorFunction()
    {
        $issue = new Issue();

        $this->assertCount(0, $issue->getCollaborators());

        $user1 = new User();
        $user1->setEmail('email1@');

        $user2 = new User();
        $user2->setEmail('email2@');

        $issue->addCollaborator($user1);

        $this->assertCount(1, $issue->getCollaborators());
        $this->assertTrue($issue->hasCollaborator($user1));
        $this->assertFalse($issue->hasCollaborator($user2));
    }

    public function testAddCommentFunction()
    {
        $issue = new Issue();

        $this->assertCount(0, $issue->getComments());

        $comment = new Comment();

        $issue->addComment($comment);

        $this->assertCount(1, $issue->getComments());
    }

    public function testRemoveCommentFunction()
    {
        $issue = new Issue();

        $this->assertCount(0, $issue->getComments());

        $comment = new Comment();

        $issue->addComment($comment);

        $this->assertCount(1, $issue->getComments());

        $issue->removeComment($comment);

        $this->assertCount(0, $issue->getComments());
    }

    public function testGetSelectLabel()
    {
        $issue = new Issue();

        $issue->setCode('PPP');
        $issue->setSummary('Summary test string');

        $this->assertEquals('( PPP ) Summary test string', $issue->getSelectLabel());
    }
}
