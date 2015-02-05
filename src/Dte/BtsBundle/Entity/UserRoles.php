<?php

namespace Dte\BtsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserRoles
 *
 * @ORM\Table(name="user_roles", uniqueConstraints={@ORM\UniqueConstraint(name="user_id_role_id", columns={"user_id", "role_id"})}, indexes={@ORM\Index(name="IDX_FK_RU_ROLE_ID", columns={"role_id"}), @ORM\Index(name="IDX_FK_RU_USER_ID", columns={"user_id"})})
 * @ORM\Entity
 */
class UserRoles
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
     * @var \Dte\BtsBundle\Entity\Role
     *
     * @ORM\ManyToOne(targetEntity="Dte\BtsBundle\Entity\Role")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     * })
     */
    private $role;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set role
     *
     * @param \Dte\BtsBundle\Entity\Role $role
     * @return UserRoles
     */
    public function setRole(\Dte\BtsBundle\Entity\Role $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \Dte\BtsBundle\Entity\Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set user
     *
     * @param \Dte\BtsBundle\Entity\User $user
     * @return UserRoles
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
