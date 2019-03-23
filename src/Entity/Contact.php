<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as ManaAssert;

/**
 * Contact.
 *
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\ContactRepository")
 * @ORM\Table(name="contact", indexes={@ORM\Index(name="idx_contact_household_idx", columns={"household_id"}), @ORM\Index(name="idx_contactdesc_idx", columns={"contactdesc_id"}), @ORM\Index(name="idx_contact_center_idx", columns={"center_id"})})
 */
class Contact
{
    public function __construct()
    {
        $this->contactDate = new \DateTime();
    }
    /**
     * @var int
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
     * @var bool
     *
     * @ORM\Column(name="first", type="boolean", nullable=true)
     */
    protected $first;

    /**
     * @var \App\Entity\Household
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Household", inversedBy="contacts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="household_id", referencedColumnName="id")
     * })
     */
    protected $household;

    /**
     * @var \App\Entity\Contactdesc
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Contactdesc", inversedBy="contacts")
     * @ORM\JoinColumn(name="contactdesc_id", referencedColumnName="id")
     * @Assert\NotBlank(message="Type must be selected")
     */
    protected $contactdesc;

    /**
     * @var \App\Entity\County
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\County", inversedBy="contacts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="county_id", referencedColumnName="id")
     * })
     */
    protected $county;

    /**
     * @var \App\Entity\Center
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Center", inversedBy="contacts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="center_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank(message="Site must be selected")
     */
    protected $center;

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
     * Set contactDate.
     *
     * @param \DateTime $contactDate
     *
     * @return Contact
     */
    public function setContactDate($contactDate)
    {
        $this->contactDate = $contactDate;

        return $this;
    }

    /**
     * Get contactDate.
     *
     * @return \DateTime
     */
    public function getContactDate()
    {
        return $this->contactDate;
    }

    /**
     * Set first.
     *
     * @param bool $first
     *
     * @return Contact
     */
    public function setFirst($first)
    {
        $this->first = $first;

        return $this;
    }

    /**
     * Get first.
     *
     * @return bool
     */
    public function getFirst()
    {
        return $this->first;
    }

    /**
     * Set center.
     *
     * @param \App\Entity\Center $center
     *
     * @return Contact
     */
    public function setCenter(\App\Entity\Center $center = null)
    {
        $this->center = $center;

        return $this;
    }

    /**
     * Get center.
     *
     * @return \App\Entity\Center
     */
    public function getCenter()
    {
        return $this->center;
    }

    /**
     * Set household.
     *
     * @param \App\Entity\Household $household
     *
     * @return Contact
     */
    public function setHousehold(\App\Entity\Household $household = null)
    {
        $this->household = $household;

        return $this;
    }

    /**
     * Get household.
     *
     * @return \App\Entity\Household
     */
    public function getHousehold()
    {
        return $this->household;
    }

    /**
     * Set contactdesc.
     *
     * @param \App\Entity\Contactdesc $contactdesc
     *
     * @return Contact
     */
    public function setContactdesc(\App\Entity\Contactdesc $contactdesc = null)
    {
        $this->contactdesc = $contactdesc;

        return $this;
    }

    /**
     * Get contactdesc.
     *
     * @return \App\Entity\Contactdesc
     */
    public function getContactdesc()
    {
        return $this->contactdesc;
    }

    /**
     * Set county.
     *
     * @param \App\Entity\County $county
     *
     * @return Contact
     */
    public function setCounty(\App\Entity\County $county = null)
    {
        $this->county = $county;

        return $this;
    }

    /**
     * Get county.
     *
     * @return \App\Entity\County
     */
    public function getCounty()
    {
        return $this->county;
    }
}
