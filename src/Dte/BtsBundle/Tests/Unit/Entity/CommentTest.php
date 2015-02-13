<?php

namespace Dte\BtsBundle\Tests\Unit\Entity;

use Dte\BtsBundle\Entity\Comment;
use Dte\BtsBundle\Entity\Issue;
use Dte\BtsBundle\Entity\User;

class CommentTest extends \PHPUnit_Framework_TestCase
{

    public function testBodySetterGetter()
    {
        $comment = new Comment();

        $this->assertNull($comment->getBody());

        $comment->setBody('Body');

        $this->assertEquals('Body', $comment->getBody());
    }

    public function testIssueSetterGetter()
    {
        $comment = new Comment();

        $this->assertNull($comment->getIssue());

        $issue = new Issue();

        $comment->setIssue($issue);

        $this->assertEquals($issue, $comment->getIssue());
    }

    public function testUserSetterGetter()
    {
        $comment = new Comment();

        $this->assertNull($comment->getUser());

        $user = new User();

        $comment->setUser($user);

        $this->assertEquals($user, $comment->getUser());
    }
}
