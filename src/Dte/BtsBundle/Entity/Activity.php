<?php

namespace Dte\BtsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Activity
 *
 * @ORM\Table(name="activity", indexes={@ORM\Index(name="IDX_AC74095A5E7AA58C", columns={"issue_id"}), @ORM\Index(name="IDX_AC74095AA76ED395", columns={"user_id"})})
 * @ORM\Entity
 */
class Activity
{
    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255, nullable=false)
     */
    private $message;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @var \Dte\BtsBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Dte\BtsBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;



    /**
     * Set message
     *
     * @param string $message
     * @return Activity
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
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
     * Set issue
     *
     * @param \Dte\BtsBundle\Entity\Issue $issue
     * @return Activity
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

    /**
     * Set user
     *
     * @param \Dte\BtsBundle\Entity\User $user
     * @return Activity
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
}
