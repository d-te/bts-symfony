<?php

namespace Dte\BtsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectMembers
 *
 * @ORM\Table(name="project_members", uniqueConstraints={@ORM\UniqueConstraint(name="project_id_user_id", columns={"project_id", "user_id"})}, indexes={@ORM\Index(name="IDX_FK_MEMBER_USER_ID", columns={"user_id"}), @ORM\Index(name="IDX_FK_MEMBER_PROJECT_ID", columns={"project_id"})})
 * @ORM\Entity
 */
class ProjectMembers
{
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
     * @var \Dte\BtsBundle\Entity\Project
     *
     * @ORM\ManyToOne(targetEntity="Dte\BtsBundle\Entity\Project")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     * })
     */
    private $project;



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
     * @return ProjectMembers
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
     * Set project
     *
     * @param \Dte\BtsBundle\Entity\Project $project
     * @return ProjectMembers
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
}
