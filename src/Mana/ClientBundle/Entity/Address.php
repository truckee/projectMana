<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Address
 *
 * @ORM\Table(name="address", indexes={@ORM\Index(name="idx_address_household_idx", columns={"household_id"}), @ORM\Index(name="idx_address_state_idx", columns={"state_id"})})
 * @ORM\Entity
 */
class Address
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
     * @ORM\Column(name="line1", type="string", length=45, nullable=true)
     */
    protected $line1;

    /**
     * @var string
     *
     * @ORM\Column(name="line2", type="string", length=45, nullable=true)
     */
    protected $line2;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=45, nullable=true)
     */
    protected $city;

//    /**
//     * @var integer
//     *
//     * @ORM\Column(name="state_id", type="integer", nullable=true)
//     */
//    protected $stateId;
//
    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=9, nullable=true)
     */
    protected $zip;

    /**
     * @var \Mana\ClientBundle\Entity\Household
     *
     * @ORM\ManyToOne(targetEntity="Mana\ClientBundle\Entity\Household", inversedBy="addresses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="household_id", referencedColumnName="id")
     * })
     */
    protected $household;

    /**
     * @var \Mana\ClientBundle\Entity\State
     *
     * @ORM\ManyToOne(targetEntity="Mana\ClientBundle\Entity\State", inversedBy="addresses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="state_id", referencedColumnName="id")
     * })
     */
    protected $state;



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
     * Set line1
     *
     * @param string $line1
     * @return Address
     */
    public function setLine1($line1)
    {
        $this->line1 = $line1;

        return $this;
    }

    /**
     * Get line1
     *
     * @return string 
     */
    public function getLine1()
    {
        return $this->line1;
    }

    /**
     * Set line2
     *
     * @param string $line2
     * @return Address
     */
    public function setLine2($line2)
    {
        $this->line2 = $line2;

        return $this;
    }

    /**
     * Get line2
     *
     * @return string 
     */
    public function getLine2()
    {
        return $this->line2;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Address
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

//    /**
//     * Set stateId
//     *
//     * @param integer $stateId
//     * @return Address
//     */
//    public function setStateId($stateId)
//    {
//        $this->stateId = $stateId;
//
//        return $this;
//    }
//
//    /**
//     * Get stateId
//     *
//     * @return integer 
//     */
//    public function getStateId()
//    {
//        return $this->stateId;
//    }
//
    /**
     * Set zip
     *
     * @param string $zip
     * @return Address
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string 
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set household
     *
     * @param \Mana\ClientBundle\Entity\Household $household
     * @return Address
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
     * Set state
     *
     * @param \Mana\ClientBundle\Entity\State $state
     * @return Address
     */
    public function setState(\Mana\ClientBundle\Entity\State $state = null)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return \Mana\ClientBundle\Entity\State 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @var \Mana\ClientBundle\Entity\County
     *
     * @ORM\ManyToOne(targetEntity="County", inversedBy="addresses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="county_id", referencedColumnName="id")
     * })
     */
    protected $county;

    /**
     * Set county
     *
     * @param \Mana\ClientBundle\Entity\County $county
     * @return Contact
     */
    public function setCounty(County $county = null)
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

    /**
     * @var \Mana\ClientBundle\Entity\AddressType
     *
     * @ORM\ManyToOne(targetEntity="AddressType", inversedBy="addresses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="addresstype_id", referencedColumnName="id")
     * })
     */
    protected $addresstype;

    /**
     * Set addresstype
     *
     * @param \Mana\ClientBundle\Entity\OneSideEntity $addresstype
     * @return Contact
     */
    public function setAddresstype(Addresstype $addresstype = null)
    {
        $this->addresstype = $addresstype;

        return $this;
    }

    /**
     * Get addresstype
     *
     * @return \Mana\ClientBundle\Entity\OneSideEntity 
     */
    public function getAddresstype()
    {
        return $this->addresstype;
    }
}
