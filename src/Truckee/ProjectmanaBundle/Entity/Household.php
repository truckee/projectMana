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
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Truckee\ProjectmanaBundle\Validator\Constraints as ManaAssert;

/**
 * Household.
 *
 * @ORM\Table(name="household")
 * @ORM\Entity(repositoryClass="Truckee\ProjectmanaBundle\Entity\HouseholdRepository")
 */
class Household
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
     * @var int
     *
     * @ORM\Column(name="hoh_id", type="integer", nullable=true)
     */
    protected $hohId;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    protected $active;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_added", type="date", nullable=true)
     */
    protected $dateAdded;

    /**
     * @var int
     *
     * @ORM\Column(name="arrivalMonth", type="integer", nullable=true)
     */
    protected $arrivalmonth;

    /**
     * @var int
     *
     * @ORM\Column(name="arrivalYear", type="integer", nullable=true)
     */
    protected $arrivalyear;

    /**
     * @var object Member as head of household
     * @ORM\OneToOne(targetEntity="Member")
     * @ORM\JoinColumn(name="hoh_id", referencedColumnName="id")
     *      */
    protected $head;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Member", mappedBy="household", cascade={"persist", "remove"}, orphanRemoval=true,  fetch="EAGER"  )
     * @ORM\OrderBy({"dob" = "ASC"})
     */
    protected $members;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Contact", mappedBy="household", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"contactDate" = "DESC"})
     */
    protected $contacts;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Address", mappedBy="household",cascade={"persist"})
     * @Assert\Valid
     */
    protected $addresses;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Phone", mappedBy="household",cascade={"persist"})
     * @Assert\Valid
     */
    protected $phones;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->addresses = new ArrayCollection();
        $this->phones = new ArrayCollection();
        $this->reasons = new ArrayCollection();
    }

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
     * Set active.
     *
     * @param bool $active
     *
     * @return Household
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active.
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set dateAdded.
     *
     * @param \DateTime $dateAdded
     *
     * @return Household
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get dateAdded.
     *
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * Set foodstamp.
     *
     * @param bool $foodstamp
     *
     * @return Household
     */
    public function setFoodstamp($foodstamp)
    {
        $this->foodstamp = $foodstamp;

        return $this;
    }

    /**
     * Get foodstamp.
     *
     * @return bool
     */
    public function getFoodstamp()
    {
        return $this->foodstamp;
    }

    /**
     * Set arrivalmonth.
     *
     * @param int $arrivalmonth
     *
     * @return Household
     */
    public function setArrivalmonth($arrivalmonth)
    {
        $this->arrivalmonth = $arrivalmonth;

        return $this;
    }

    /**
     * Get arrivalmonth.
     *
     * @return int
     */
    public function getArrivalmonth()
    {
        return $this->arrivalmonth;
    }

    /**
     * Set arrivalyear.
     *
     * @param int $arrivalyear
     *
     * @return Household
     */
    public function setArrivalyear($arrivalyear)
    {
        $this->arrivalyear = $arrivalyear;

        return $this;
    }

    /**
     * Get arrivalyear.
     *
     * @return int
     */
    public function getArrivalyear()
    {
        return $this->arrivalyear;
    }

    /**
     * Add members.
     *
     * @param \Truckee\ProjectmanaBundle\Entity\Member $members
     *
     * @return Household
     */
    public function addMember(Member $member)
    {
        $this->members[] = $member;
        $member->setHousehold($this);

        return $this;
    }

    /**
     * Remove members.
     *
     * @param \Truckee\ProjectmanaBundle\Entity\Member $members
     */
    public function removeMember(Member $member)
    {
        $this->members->removeElement($member);
    }

    /**
     * Get members.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * Add contacts.
     *
     * @param \Truckee\ProjectmanaBundle\Entity\Contact $contacts
     *
     * @return Household
     */
    public function addContact(\Truckee\ProjectmanaBundle\Entity\Contact $contact)
    {
        $this->contacts[] = $contact;
        $contact->setHousehold($this);

        return $this;
    }

    /**
     * Remove contacts.
     *
     * @param \Truckee\ProjectmanaBundle\Entity\Contact $contacts
     */
    public function removeContact(\Truckee\ProjectmanaBundle\Entity\Contact $contact)
    {
        $this->contacts->removeElement($contact);
    }

    /**
     * Get contacts.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Add address.
     *
     * @param \Truckee\ProjectmanaBundle\Entity\Address $address
     *
     * @return Household
     */
    public function addAddress(Address $address)
    {
        $this->addresses[] = $address;
        $address->setHousehold($this);

        return $this;
    }

    /**
     * Remove addresses.
     *
     * @param \Truckee\ProjectmanaBundle\Entity\Address $address
     */
    public function removeAddress(\Truckee\ProjectmanaBundle\Entity\Address $address)
    {
        $this->addresses->removeElement($address);
    }

    /**
     * Get addresses.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Add phone.
     *
     * @param \Truckee\ProjectmanaBundle\Entity\Phone $phone
     *
     * @return Household
     */
    public function addPhone(Phone $phone)
    {
        $this->phones[] = $phone;
        $phone->setHousehold($this);

        return $this;
    }

    /**
     * Remove phones.
     *
     * @param \Truckee\ProjectmanaBundle\Entity\Phone $phone
     */
    public function removePhone(\Truckee\ProjectmanaBundle\Entity\Phone $phone)
    {
        $this->phones->removeElement($phone);
    }

    /**
     * Get phones.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhones()
    {
        return $this->phones;
    }

    public function setHead($member)
    {
        $this->head = $member;

        return $this;
    }

    public function getHead()
    {
        return $this->head;
    }

    /**
     * @var \Truckee\ProjectmanaBundle\Entity\Housing
     *
     * @ORM\ManyToOne(targetEntity="Housing", inversedBy="households")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="housing_id", referencedColumnName="id")
     * })
     */
    protected $housing;

    /**
     * Set housing.
     *
     * @param \Truckee\ProjectmanaBundle\Entity\Housing $housing
     *
     * @return Contact
     */
    public function setHousing(Housing $housing = null)
    {
        $this->housing = $housing;

        return $this;
    }

    /**
     * Get housing.
     *
     * @return \Truckee\ProjectmanaBundle\Entity\Housing
     */
    public function getHousing()
    {
        return $this->housing;
    }

    /**
     * @var \Truckee\ProjectmanaBundle\Entity\Notfoodstamp
     *
     * @ORM\ManyToOne(targetEntity="Notfoodstamp", inversedBy="households")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="notfoodstamp_id", referencedColumnName="id")
     * })
     */
    protected $notfoodstamp;

    /**
     * Set notfoodstamp.
     *
     * @param \Truckee\ProjectmanaBundle\Entity\Notfoodstamp $notfoodstamp
     *
     * @return Contact
     */
    public function setNotfoodstamp(Notfoodstamp $notfoodstamp = null)
    {
        $this->notfoodstamp = $notfoodstamp;

        return $this;
    }

    /**
     * Get notfoodstamp.
     *
     * @return \Truckee\ProjectmanaBundle\Entity\Notfoodstamp
     */
    public function getNotfoodstamp()
    {
        return $this->notfoodstamp;
    }

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="FsStatus", inversedBy="households")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="foodstamp_id", referencedColumnName="id")
     * })     */
    protected $foodstamp;

    /**
     * @var \Truckee\ProjectmanaBundle\Entity\FsAmount
     *
     * @ORM\ManyToOne(targetEntity="FsAmount", inversedBy="households")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fsamount_id", referencedColumnName="id")
     * })
     */
    protected $fsamount;

    /**
     * Set fsamount.
     *
     * @param \Truckee\ProjectmanaBundle\Entity\FsAmount $fsamount
     *
     * @return Contact
     */
    public function setFsamount(FsAmount $fsamount = null)
    {
        $this->fsamount = $fsamount;

        return $this;
    }

    /**
     * Get fsamount.
     *
     * @return \Truckee\ProjectmanaBundle\Entity\Fsamount
     */
    public function getFsamount()
    {
        return $this->fsamount;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Reason", inversedBy="households", cascade={"persist"})
     * @ORM\JoinTable(name="household_reason",
     *      joinColumns={@ORM\JoinColumn(name="household_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="reason_id", referencedColumnName="id")}
     *      ))
     */
    protected $reasons;

    public function addReason(Reason $reason)
    {
        $reason->addHousehold($this); // synchronously updating inverse side
        $this->reasons[] = $reason;
    }

    public function getReasons()
    {
        return $this->reasons;
    }

    /**
     * @var bool
     *
     * @ORM\Column(name="compliance", type="boolean", nullable=true)
     */
    protected $compliance;

    /**
     * Set compliance.
     *
     * @param bool $compliance
     *
     * @return compliance
     */
    public function setCompliance($compliance)
    {
        $this->compliance = $compliance;

        return $this;
    }

    /**
     * Get compliance.
     *
     * @return bool
     */
    public function getCompliance()
    {
        return $this->compliance;
    }

    /**
     * @ORM\Column(name="compliance_date", type="date", nullable=true)
     * @ManaAssert\ComplianceDate
     * @ManaAssert\NotFutureDate
     */
    protected $complianceDate;

    /**
     * Set complianceDate.
     *
     * @param bool $complianceDate
     *
     * @return complianceDate
     */
    public function setComplianceDate($complianceDate)
    {
        $this->complianceDate = $complianceDate;

        return $this;
    }

    /**
     * Get complianceDate.
     *
     * @return bool
     */
    public function getComplianceDate()
    {
        return $this->complianceDate;
    }

    /**
     * @var bool
     *
     * @ORM\Column(name="shared", type="boolean", nullable=true)
     */
    protected $shared;

    /**
     * Set shared.
     *
     * @param bool $shared
     *
     * @return shared
     */
    public function setShared($shared)
    {
        $this->shared = $shared;

        return $this;
    }

    /**
     * Get shared.
     *
     * @return bool
     */
    public function getShared()
    {
        return $this->shared;
    }

    /**
     * @var bool
     *
     * @ORM\Column(name="shared_date", type="date", nullable=true)
     * @ManaAssert\SharedDate
     * @ManaAssert\NotFutureDate
     */
    protected $sharedDate;

    /**
     * Set shareddate.
     *
     * @param date $sharedDate
     *
     * @return shareddate
     */
    public function setSharedDate($sharedDate)
    {
        $this->sharedDate = $sharedDate;

        return $this;
    }

    /**
     * Get shareddate.
     *
     * @return bool
     */
    public function getSharedDate()
    {
        return $this->sharedDate;
    }

    /**
     * @var \Truckee\ProjectmanaBundle\Entity\Income
     *
     * @ORM\ManyToOne(targetEntity="Income", inversedBy="households")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="income_id", referencedColumnName="id")
     * })
     */
    protected $income;

    /**
     * Set income.
     *
     * @param \Truckee\ProjectmanaBundle\Entity\OneSideEntity $income
     *
     * @return Contact
     */
    public function setIncome(Income $income = null)
    {
        $this->income = $income;

        return $this;
    }

    /**
     * Get income.
     *
     * @return \Truckee\ProjectmanaBundle\Entity\OneSideEntity
     */
    public function getIncome()
    {
        return $this->income;
    }

    /**
     * @var \Truckee\ProjectmanaBundle\Entity\OneSideEntity
     *
     * @ORM\ManyToOne(targetEntity="Center", inversedBy="households")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="center_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank(message="First site may not be empty")
     */
    protected $center;

    /**
     * Set center.
     *
     * @param \Truckee\ProjectmanaBundle\Entity\Center $center
     *
     * @return Contact
     */
    public function setCenter(Center $center = null)
    {
        $this->center = $center;

        return $this;
    }

    /**
     * Get center.
     *
     * @return \Truckee\ProjectmanaBundle\Entity\Center
     */
    public function getCenter()
    {
        return $this->center;
    }
}