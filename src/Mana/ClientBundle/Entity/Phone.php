<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mana\ClientBundle\Validator\Constraints as ManaAssert;

/**
 * Phone.
 *
 * @ORM\Table(name="phone")
 * @ORM\Entity
 */
class Phone
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
     * @ORM\Column(name="areacode", type="string", length=45, nullable=true)
     * @ManaAssert\AreaCode
     */
    protected $areacode;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_number", type="string", length=8, nullable=true)
     * @ManaAssert\PhoneNumber
     */
    protected $phoneNumber;

    /**
     * @var \Mana\ClientBundle\Entity\Household
     *
     * @ORM\ManyToOne(targetEntity="Mana\ClientBundle\Entity\Household", inversedBy="phones")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="household_id", referencedColumnName="id")
     * })
     */
    protected $household;

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
     * Set areacode.
     *
     * @param string $areacode
     *
     * @return Phone
     */
    public function setAreacode($areacode)
    {
        $this->areacode = $areacode;

        return $this;
    }

    /**
     * Get areacode.
     *
     * @return string
     */
    public function getAreacode()
    {
        return $this->areacode;
    }

    /**
     * Set phoneNumber.
     *
     * @param string $phoneNumber
     *
     * @return Phone
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber.
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set household.
     *
     * @param \Mana\ClientBundle\Entity\Household $household
     *
     * @return Phone
     */
    public function setHousehold(\Mana\ClientBundle\Entity\Household $household = null)
    {
        $this->household = $household;

        return $this;
    }

    /**
     * Get household.
     *
     * @return \Mana\ClientBundle\Entity\Household
     */
    public function getHousehold()
    {
        return $this->household;
    }
}
