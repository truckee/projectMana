<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Truckee\ProjectmanaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Address.
 *
 * @ORM\Table(name="address", indexes={@ORM\Index(name="idx_address_household_idx", columns={"household_id"}), @ORM\Index(name="idx_address_state_idx", columns={"state_id"})})
 * @ORM\Entity
 */
class Address
{
    /**
     * @var int
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
     * @Assert\NotBlank(message = "Address may not be blank")
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
     * @Assert\NotBlank(message = "City may not be blank")
     */
    protected $city;

    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=9, nullable=true)
     */
    protected $zip;

    /**
     * @var \Truckee\ProjectmanaBundle\Entity\Household
     *
     * @ORM\ManyToOne(targetEntity="Truckee\ProjectmanaBundle\Entity\Household", inversedBy="addresses", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="household_id", referencedColumnName="id")
     * })
     */
    protected $household;

    /**
     * @var \Truckee\ProjectmanaBundle\Entity\State
     *
     * @ORM\ManyToOne(targetEntity="Truckee\ProjectmanaBundle\Entity\State", inversedBy="addresses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="state_id", referencedColumnName="id")
     * })
     */
    protected $state;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set line1.
     *
     * @param string $line1
     *
     * @return Address
     */
    public function setLine1($line1)
    {
        $this->line1 = $line1;

        return $this;
    }

    /**
     * Get line1.
     *
     * @return string
     */
    public function getLine1()
    {
        return $this->line1;
    }

    /**
     * Set line2.
     *
     * @param string $line2
     *
     * @return Address
     */
    public function setLine2($line2)
    {
        $this->line2 = $line2;

        return $this;
    }

    /**
     * Get line2.
     *
     * @return string
     */
    public function getLine2()
    {
        return $this->line2;
    }

    /**
     * Set city.
     *
     * @param string $city
     *
     * @return Address
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set zip.
     *
     * @param string $zip
     *
     * @return Address
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip.
     *
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set household.
     *
     * @param \Truckee\ProjectmanaBundle\Entity\Household $household
     *
     * @return Address
     */
    public function setHousehold(\Truckee\ProjectmanaBundle\Entity\Household $household = null)
    {
        $this->household = $household;

        return $this;
    }

    /**
     * Get household.
     *
     * @return \Truckee\ProjectmanaBundle\Entity\Household
     */
    public function getHousehold()
    {
        return $this->household;
    }

    /**
     * Set state.
     *
     * @param \Truckee\ProjectmanaBundle\Entity\State $state
     *
     * @return Address
     */
    public function setState(\Truckee\ProjectmanaBundle\Entity\State $state = null)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state.
     *
     * @return \Truckee\ProjectmanaBundle\Entity\State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @var \Truckee\ProjectmanaBundle\Entity\County
     *
     * @ORM\ManyToOne(targetEntity="County", inversedBy="addresses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="county_id", referencedColumnName="id")
     * })
     */
    protected $county;

    /**
     * Set county.
     *
     * @param \Truckee\ProjectmanaBundle\Entity\County $county
     *
     * @return Contact
     */
    public function setCounty(County $county = null)
    {
        $this->county = $county;

        return $this;
    }

    /**
     * Get county.
     *
     * @return \Truckee\ProjectmanaBundle\Entity\County
     */
    public function getCounty()
    {
        return $this->county;
    }

    /**
     * @var \Truckee\ProjectmanaBundle\Entity\AddressType
     *
     * @ORM\ManyToOne(targetEntity="AddressType", inversedBy="addresses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="addresstype_id", referencedColumnName="id")
     * })
     */
    protected $addresstype;

    /**
     * Set addresstype.
     *
     * @param \Truckee\ProjectmanaBundle\Entity\AddressType $addresstype
     *
     * @return Contact
     */
    public function setAddresstype(Addresstype $addresstype = null)
    {
        $this->addresstype = $addresstype;

        return $this;
    }

    /**
     * Get addresstype.
     *
     * @return \Truckee\ProjectmanaBundle\Entity\AddressType
     */
    public function getAddresstype()
    {
        return $this->addresstype;
    }
}
