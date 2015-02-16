<?php

namespace Dte\BtsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Issue
 *
 * @ORM\Table(name="issue", indexes={@ORM\Index(name="IDX_12AD233E6BF700BD", columns={"status_id"}), @ORM\Index(name="IDX_12AD233E59EC7D60", columns={"assignee_id"}), @ORM\Index(name="IDX_12AD233E497B19F9", columns={"priority_id"}), @ORM\Index(name="IDX_12AD233E12A1C43A", columns={"resolution_id"}), @ORM\Index(name="IDX_12AD233EE1CFE6F5", columns={"reporter_id"}), @ORM\Index(name="project_id", columns={"project_id"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Dte\BtsBundle\Entity\Repository\IssueRepository")
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
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type;

    /**
     * @var \Dte\BtsBundle\Entity\Issue
     *
     * @ORM\ManyToOne(targetEntity="Issue", inversedBy="children")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * })
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="parent")
     */
    private $children;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
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
     * @var \Dte\BtsBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Dte\BtsBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="reporter_id", referencedColumnName="id")
     * })
     */
    private $reporter;

    /**
     * @var \Dte\BtsBundle\Entity\Project
     *
     * @ORM\ManyToOne(targetEntity="Dte\BtsBundle\Entity\Project")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     * })
     */
    private $project;

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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="issue")
     * @ORM\OrderBy({"id" = "desc"})
     */
    private $comments;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

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
     * Set parent
     *
     * @param \Dte\BtsBundle\Entity\Issue $parent
     * @return Issue
     */
    public function setParent(\Dte\BtsBundle\Entity\Issue $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Dte\BtsBundle\Entity\Issue
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     *  Get children
     */
    public function getChildren()
    {
        return $this->children->toArray();
    }

    /**
     *  Get comments
     */
    public function getComments()
    {
        return $this->comments->toArray();
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

    /**
     * Set project
     *
     * @param \Dte\BtsBundle\Entity\Project $project
     * @return Issue
     */
    public function setProject(\Dte\BtsBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \Dte\BtsBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
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
     * Return label for dropdown lists
     *
     * @return string
     */
    public function getSelectLabel()
    {
        return sprintf('( %s ) %s', $this->getCode(), $this->getSummary());
    }

    /**
     * Generate issue code by project code and id
     *
     * @return string
     */
    public function generateCode()
    {
        return sprintf('%s-%d', $this->getProject()->getCode(), $this->getId());
    }

    /**
     * Add comment
     *
     */
    public function addComment(Comment $comment)
    {
        $this->comments->add($comment);

        return $this;
    }

    /**
     * Remove comment
     *
     */
    public function removeComment(Comment $comment)
    {
        $this->comments->removeElement($comment);
    }
}
