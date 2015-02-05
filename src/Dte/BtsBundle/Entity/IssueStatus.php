<?php

namespace Dte\BtsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IssueStatus
 *
 * @ORM\Table(name="issue_status")
 * @ORM\Entity
 */
class IssueStatus
{
    /**
     * @var integer
     *
     * @ORM\Column(name="label", type="integer", nullable=false)
     */
    private $label;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set label
     *
     * @param integer $label
     * @return IssueStatus
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return integer
     */
    public function getLabel()
    {
        return $this->label;
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
}
