<?php

namespace Dte\BtsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project
 *
 *
 * @ORM\Table(name="project")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Dte\BtsBundle\Entity\Repository\ProjectRepository")
 * @UniqueEntity("code")
 */
class Project
{
    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(max = 255)
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(max = 255)
     */
    private $summary;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=false, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(max = 255)
     */
    private $code;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="projects", cascade={"persist"})
     * @ORM\JoinTable(name="project_members",
     *      joinColumns={@ORM\JoinColumn(name="project_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     *      )
     */
    private $members;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="project")
     */
    private $issues;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->issues  = new ArrayCollection();
    }

    /**
     * Set label
     *
     * @param string $label
     * @return Project
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set summary
     *
     * @param string $summary
     * @return Project
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
     * @return Project
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get project members
     *
     * @return User[]
     */
    public function getMembers()
    {
        return $this->members->toArray();
    }

    /**
     * Add project's member
     *
     * @param  User $member
     * @return Project
     */
    public function addMember(User $member)
    {
        if ($this->members->contains($member)) {
            return;
        }

        $this->members->add($member);

        return $this;
    }

    /**
     * Remove project's member
     *
     * @param  User $member
     */
    public function removeMember(User $member)
    {
        $this->members->removeElement($member);
    }

    /**
     * Get project issues
     *
     * @return array
     */
    public function getIssues()
    {
        return $this->issues->toArray();
    }

    /**
     * Return label for dropdown lists
     *
     * @return string
     */
    public function getSelectLabel()
    {
        return sprintf('( %s ) %s', $this->getCode(), $this->getLabel());
    }
}
