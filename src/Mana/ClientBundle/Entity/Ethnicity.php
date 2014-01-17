<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
//use Doctrine\Common\Collections\ArrayCollection;

/**
 * Ethnicity
 *
 * @ORM\Table(name="ethnicity")
 * @ORM\Entity
 */
class Ethnicity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="ethnicity", type="string", length=45, nullable=true)
     */
    protected $ethnicity;

    /**
     * @var string
     *
     * @ORM\Column(name="abbr", type="string", length=45, nullable=true)
     */
    protected $abbreviation;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Mana\ClientBundle\Entity\Member", mappedBy="ethnicity", cascade={"persist"})
     */
    protected $members;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->members = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set ethnicity
     *
     * @param string $ethnicity
     * @return Ethnicity
     */
    public function setEthnicity($ethnicity)
    {
        $this->ethnicity = $ethnicity;

        return $this;
    }

    /**
     * Get ethnicity
     *
     * @return string 
     */
    public function getEthnicity()
    {
        return $this->ethnicity;
    }

    /**
     * Set abbreviation
     *
     * @param string $abbreviation
     * @return Ethnicity
     */
    public function setAbbreviation($abbreviation)
    {
        $this->abbreviation = $abbreviation;

        return $this;
    }

    /**
     * Get abbreviation
     *
     * @return string 
     */
    public function getAbbreviation()
    {
        return $this->abbreviation;
    }

    /**
     * Add members
     *
     * @param \Mana\ClientBundle\Entity\Member $members
     * @return Ethnicity
     */
    public function addMember(\Mana\ClientBundle\Entity\Member $members)
    {
        $this->members[] = $members;

        return $this;
    }

    /**
     * Remove members
     *
     * @param \Mana\ClientBundle\Entity\Member $members
     */
    public function removeMember(\Mana\ClientBundle\Entity\Member $members)
    {
        $this->members->removeElement($members);
    }

    /**
     * Get members
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=true)
     */
    protected $enabled;

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return enabled
     */
    public function setEnabled($enabled) {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled() {
        return $this->enabled;
    }
}
