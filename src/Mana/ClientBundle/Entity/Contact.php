<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Mana\ClientBundle\Validator\Constraints as ManaAssert;

/**
 * Contact
 *
 * @ORM\Entity
 * @ORM\Table(name="contact", indexes={@ORM\Index(name="idx_contact_household_idx", columns={"household_id"}), @ORM\Index(name="idx_contact_type_idx", columns={"contact_type_id"}), @ORM\Index(name="idx_contact_center_idx", columns={"center_id"})})
 * 
 */
class Contact
{
    
    public function __construct() {
        $this->contactDate = new \DateTime();
    }
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="contact_date", type="date", nullable=true)
     * @ManaAssert\NotFutureDate
     */
    protected $contactDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="first", type="boolean", nullable=true)
     */
    protected $first;

    /**
     * @var \Mana\ClientBundle\Entity\Household
     *
     * @ORM\ManyToOne(targetEntity="Mana\ClientBundle\Entity\Household", inversedBy="contacts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="household_id", referencedColumnName="id")
     * })
     */
    protected $household;

    /**
     * @var \Mana\ClientBundle\Entity\ContactDesc
     *
     * @ORM\ManyToOne(targetEntity="Mana\ClientBundle\Entity\ContactDesc", inversedBy="contacts")
     * @ORM\JoinColumn(name="contact_type_id", referencedColumnName="id")
     * @Assert\NotBlank(message="Type must be selected")
     */
    protected $contactDesc;

    /**
     * @var \Mana\ClientBundle\Entity\County
     *
     * @ORM\ManyToOne(targetEntity="Mana\ClientBundle\Entity\County", inversedBy="contacts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="county_id", referencedColumnName="id")
     * })
     */
    protected $county;

    /**
     * @var \Mana\ClientBundle\Entity\Center
     *
     * @ORM\ManyToOne(targetEntity="Mana\ClientBundle\Entity\Center", inversedBy="contacts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="center_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank(message="Site must be selected")
     */
    protected $center;

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
     * Set contactDate
     *
     * @param \DateTime $contactDate
     * @return Contact
     */
    public function setContactDate($contactDate)
    {
        $this->contactDate = $contactDate;

        return $this;
    }

    /**
     * Get contactDate
     *
     * @return \DateTime 
     */
    public function getContactDate()
    {
        return $this->contactDate;
    }

    /**
     * Set first
     *
     * @param boolean $first
     * @return Contact
     */
    public function setFirst($first)
    {
        $this->first = $first;

        return $this;
    }

    /**
     * Get first
     *
     * @return boolean 
     */
    public function getFirst()
    {
        return $this->first;
    }

    /**
     * Set center
     *
     * @param \Mana\ClientBundle\Entity\Center $center
     * @return Contact
     */
    public function setCenter(\Mana\ClientBundle\Entity\Center $center = null)
    {
        $this->center = $center;

        return $this;
    }

    /**
     * Get center
     *
     * @return \Mana\ClientBundle\Entity\Center 
     */
    public function getCenter()
    {
        return $this->center;
    }

    /**
     * Set household
     *
     * @param \Mana\ClientBundle\Entity\Household $household
     * @return Contact
     */
    public function setHousehold(\Mana\ClientBundle\Entity\Household $household = null)
    {
        $this->household = $household;

        return $this;
    }

    /**
     * Get household
     *
     * @return \Mana\ClientBundle\Entity\Household 
     */
    public function getHousehold()
    {
        return $this->household;
    }

    /**
     * Set contactDesc
     *
     * @param \Mana\ClientBundle\Entity\ContactDesc $contactDesc
     * @return Contact
     */
    public function setContactDesc(\Mana\ClientBundle\Entity\ContactDesc $contactDesc = null)
    {
        $this->contactDesc = $contactDesc;

        return $this;
    }

    /**
     * Get contactDesc
     *
     * @return \Mana\ClientBundle\Entity\ContactDesc 
     */
    public function getContactDesc()
    {
        return $this->contactDesc;
    }

    /**
     * Set county
     *
     * @param \Mana\ClientBundle\Entity\County $county
     * @return Contact
     */
    public function setCounty(\Mana\ClientBundle\Entity\County $county = null)
    {
        $this->county = $county;

        return $this;
    }

    /**
     * Get county
     *
     * @return \Mana\ClientBundle\Entity\County 
     */
    public function getCounty()
    {
        return $this->county;
    }
}
