<?php

namespace Dte\BtsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table(name="comment", indexes={@ORM\Index(name="IDX_FK_COMMENT_USER_ID", columns={"user_id"}), @ORM\Index(name="IDX_FK_COMMENT_ISSUE_ID", columns={"issue_id"})})
 * @ORM\Entity
 */
class Comment
{
    /**
     * @var string
     *
     * @ORM\Column(name="body", type="string", length=255, nullable=false)
     */
    private $body;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Dte\BtsBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Dte\BtsBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \Dte\BtsBundle\Entity\Issue
     *
     * @ORM\ManyToOne(targetEntity="Dte\BtsBundle\Entity\Issue")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="issue_id", referencedColumnName="id")
     * })
     */
    private $issue;



    /**
     * Set body
     *
     * @param string $body
     * @return Comment
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Comment
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param \Dte\BtsBundle\Entity\User $user
     * @return Comment
     */
    public function setUser(\Dte\BtsBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Dte\BtsBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set issue
     *
     * @param \Dte\BtsBundle\Entity\Issue $issue
     * @return Comment
     */
    public function setIssue(\Dte\BtsBundle\Entity\Issue $issue = null)
    {
        $this->issue = $issue;

        return $this;
    }

    /**
     * Get issue
     *
     * @return \Dte\BtsBundle\Entity\Issue
     */
    public function getIssue()
    {
        return $this->issue;
    }
}