<?php

namespace Dte\BtsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Issue
 *
 * @ORM\Table(name="issue", indexes={@ORM\Index(name="IDX_FK_ISSUE_RESOLUTION_ID", columns={"resolution_id"}), @ORM\Index(name="IDX_FK_ISSUE_STATUS_ID", columns={"status_id"}), @ORM\Index(name="IDX_FK_PRIORITY_ID", columns={"priority_id"}), @ORM\Index(name="IDX_FK_ISSUE_ASSIGNEE_ID", columns={"assignee_id"}), @ORM\Index(name="IDX_FK_ISSUE_REPORTER_ID", columns={"reporter_id"})})
 * @ORM\Entity
 */
class Issue
{
    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="string", length=255, nullable=false)
     */
    private $summary;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=20, nullable=false)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="type", type="boolean", nullable=false)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=false)
     */
    private $parentId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=false)
     */
    private $updated;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Dte\BtsBundle\Entity\IssueStatus
     *
     * @ORM\ManyToOne(targetEntity="Dte\BtsBundle\Entity\IssueStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     * })
     */
    private $status;

    /**
     * @var \Dte\BtsBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Dte\BtsBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="assignee_id", referencedColumnName="id")
     * })
     */
    private $assignee;

    /**
     * @var \Dte\BtsBundle\Entity\IssuePriority
     *
     * @ORM\ManyToOne(targetEntity="Dte\BtsBundle\Entity\IssuePriority")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="priority_id", referencedColumnName="id")
     * })
     */
    private $priority;

    /**
     * @var \Dte\BtsBundle\Entity\IssueResolution
     *
     * @ORM\ManyToOne(targetEntity="Dte\BtsBundle\Entity\IssueResolution")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="resolution_id", referencedColumnName="id")
     * })
     */
    private $resolution;

    /**
     * @var \Dte\BtsBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Dte\BtsBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="reporter_id", referencedColumnName="id")
     * })
     */
    private $reporter;



    /**
     * Set summary
     *
     * @param string $summary
     * @return Issue
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Issue
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Issue
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set type
     *
     * @param boolean $type
     * @return Issue
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return boolean
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set parentId
     *
     * @param integer $parentId
     * @return Issue
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId
     *
     * @return integer
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Issue
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
     * Set updated
     *
     * @param \DateTime $updated
     * @return Issue
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
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
     * Set status
     *
     * @param \Dte\BtsBundle\Entity\IssueStatus $status
     * @return Issue
     */
    public function setStatus(\Dte\BtsBundle\Entity\IssueStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \Dte\BtsBundle\Entity\IssueStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set assignee
     *
     * @param \Dte\BtsBundle\Entity\User $assignee
     * @return Issue
     */
    public function setAssignee(\Dte\BtsBundle\Entity\User $assignee = null)
    {
        $this->assignee = $assignee;

        return $this;
    }

    /**
     * Get assignee
     *
     * @return \Dte\BtsBundle\Entity\User
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * Set priority
     *
     * @param \Dte\BtsBundle\Entity\IssuePriority $priority
     * @return Issue
     */
    public function setPriority(\Dte\BtsBundle\Entity\IssuePriority $priority = null)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return \Dte\BtsBundle\Entity\IssuePriority
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set resolution
     *
     * @param \Dte\BtsBundle\Entity\IssueResolution $resolution
     * @return Issue
     */
    public function setResolution(\Dte\BtsBundle\Entity\IssueResolution $resolution = null)
    {
        $this->resolution = $resolution;

        return $this;
    }

    /**
     * Get resolution
     *
     * @return \Dte\BtsBundle\Entity\IssueResolution
     */
    public function getResolution()
    {
        return $this->resolution;
    }

    /**
     * Set reporter
     *
     * @param \Dte\BtsBundle\Entity\User $reporter
     * @return Issue
     */
    public function setReporter(\Dte\BtsBundle\Entity\User $reporter = null)
    {
        $this->reporter = $reporter;

        return $this;
    }

    /**
     * Get reporter
     *
     * @return \Dte\BtsBundle\Entity\User
     */
    public function getReporter()
    {
        return $this->reporter;
    }
}
