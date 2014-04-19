<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Mana\ClientBundle\Entity\Member;
use Mana\ClientBundle\Entity\Address;
use Mana\ClientBundle\Entity\Phone;
use Mana\ClientBundle\Entity\Appliance;
use Mana\ClientBundle\Entity\Notfoodstamp;
use Mana\ClientBundle\Entity\Housing;
use Mana\ClientBundle\Entity\IncomeSource;
use Mana\ClientBundle\Entity\FsAmount;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Household
 *
 * @ORM\Table(name="household")
 * @ORM\Entity(repositoryClass="Mana\ClientBundle\Entity\HouseholdRepository")
 */
class Household {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="hoh_id", type="integer", nullable=true)
     */
    protected $hohId;

    /**
     * @var boolean
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

//    /**
//     * @var integer
//     *
//     * @ORM\Column(name="wic", type="boolean", nullable=true)
//     */
//    protected $wic;

    /**
     * @var integer
     *
     * @ORM\Column(name="food_stamps", type="integer", nullable=true)
     */
    protected $foodStamps;

    /**
     * @var integer
     *
     * @ORM\Column(name="arrivalMonth", type="integer", nullable=true)
     */
    protected $arrivalmonth;

    /**
     * @var integer
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
     * @Assert\Valid
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

//    /**
//     * @var \Doctrine\Common\Collections\Collection
//     *
//     * @ORM\OneToMany(targetEntity="Referral", mappedBy="household", cascade={"persist"}, orphanRemoval=true)
//     */
//    protected $referrals;
//    /**
//     * @var \Doctrine\Common\Collections\Collection
//     *
//     * @ORM\OneToMany(targetEntity="Note", mappedBy="household", cascade={"persist"})
//     */
//    protected $notes;

    /**
     * Constructor
     */
    public function __construct() {
        $this->members = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->addresses = new ArrayCollection();
        $this->phones = new ArrayCollection();
//        $this->referrals = new ArrayCollection();
//        $this->notes = new ArrayCollection();
//        $this->incomeHistories = new ArrayCollection();
        $this->appliances = new ArrayCollection();
        $this->reasons = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set hohId
     *
     * @param integer $hohId
     * @return Household
     */
    public function setHohId($hohId) {
        $this->hohId = $hohId;

        return $this;
    }

    /**
     * Get hohId
     *
     * @return integer 
     */
    public function getHohId() {
        return $this->hohId;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Household
     */
    public function setActive($active) {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive() {
        return $this->active;
    }

    /**
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     * @return Household
     */
    public function setDateAdded($dateAdded) {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get dateAdded
     *
     * @return \DateTime 
     */
    public function getDateAdded() {
        return $this->dateAdded;
    }

//    /**
//     * Set wic
//     *
//     * @param boolean $wic
//     * @return Household
//     */
//    public function setWic($wic) {
//        $this->wic = $wic;
//
//        return $this;
//    }
//
//    /**
//     * Get wic
//     *
//     * @return boolean 
//     */
//    public function getWic() {
//        return $this->wic;
//    }

    /**
     * Set foodStamps
     *
     * @param boolean $foodStamps
     * @return Household
     */
    public function setFoodStamps($foodStamps) {
        $this->foodStamps = $foodStamps;

        return $this;
    }

    /**
     * Get foodStamps
     *
     * @return boolean 
     */
    public function getFoodStamps() {
        return $this->foodStamps;
    }

    /**
     * Set arrivalmonth
     *
     * @param integer $arrivalmonth
     * @return Household
     */
    public function setArrivalmonth($arrivalmonth) {
        $this->arrivalmonth = $arrivalmonth;

        return $this;
    }

    /**
     * Get arrivalmonth
     *
     * @return integer 
     */
    public function getArrivalmonth() {
        return $this->arrivalmonth;
    }

    /**
     * Set arrivalyear
     *
     * @param integer $arrivalyear
     * @return Household
     */
    public function setArrivalyear($arrivalyear) {
        $this->arrivalyear = $arrivalyear;

        return $this;
    }

    /**
     * Get arrivalyear
     *
     * @return integer 
     */
    public function getArrivalyear() {
        return $this->arrivalyear;
    }

    /**
     * Add members
     *
     * @param \Mana\ClientBundle\Entity\Member $members
     * @return Household
     */
    public function addMember(Member $member) {
        $this->members[] = $member;
        $member->setHousehold($this);
        return $this;
    }

    /**
     * Remove members
     *
     * @param \Mana\ClientBundle\Entity\Member $members
     */
    public function removeMember(Member $member) {
        $this->members->removeElement($member);
    }

    /**
     * Get members
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMembers() {
        return $this->members;
    }

    /**
     * Add contacts
     *
     * @param \Mana\ClientBundle\Entity\Contact $contacts
     * @return Household
     */
    public function addContact(\Mana\ClientBundle\Entity\Contact $contact) {
        $this->contacts[] = $contact;
        $contact->setHousehold($this);
        return $this;
    }

    /**
     * Remove contacts
     *
     * @param \Mana\ClientBundle\Entity\Contact $contacts
     */
    public function removeContact(\Mana\ClientBundle\Entity\Contact $contact) {
        $this->contacts->removeElement($contact);
    }

    /**
     * Get contacts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContacts() {
        return $this->contacts;
    }

    /**
     * Add address
     *
     * @param \Mana\ClientBundle\Entity\Address $address
     * @return Household
     */
    public function addAddress(Address $address) {
        $this->addresses[] = $address;
        $address->setHousehold($this);
        return $this;
    }

    /**
     * Remove addresses
     *
     * @param \Mana\ClientBundle\Entity\Address $addresses
     */
    public function removeAddress(\Mana\ClientBundle\Entity\Address $address) {
        $this->addresses->removeElement($address);
    }

    /**
     * Get addresses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAddresses() {
        return $this->addresses;
    }

    /**
     * Add phone
     *
     * @param \Mana\ClientBundle\Entity\Phone $phone
     * @return Household
     */
    public function addPhone(Phone $phone) {
        $this->phones[] = $phone;
        $phone->setHousehold($this);
        return $this;
    }

    /**
     * Remove phones
     *
     * @param \Mana\ClientBundle\Entity\Phone $phones
     */
    public function removePhone(\Mana\ClientBundle\Entity\Phone $phone) {
        $this->phones->removeElement($phone);
    }

    /**
     * Get phones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPhones() {
        return $this->phones;
    }

//    /**
//     * Add referrals
//     *
//     * @param \Mana\ClientBundle\Entity\Referral $referrals
//     * @return Household
//     */
//    public function addReferral(\Mana\ClientBundle\Entity\Referral $referrals) {
//        $this->referrals[] = $referrals;
//
//        return $this;
//    }
//
//    /**
//     * Remove referrals
//     *
//     * @param \Mana\ClientBundle\Entity\Referral $referrals
//     */
//    public function removeReferral(\Mana\ClientBundle\Entity\Referral $referrals) {
//        $this->referrals->removeElement($referrals);
//    }
//
//    /**
//     * Get referrals
//     *
//     * @return \Doctrine\Common\Collections\Collection 
//     */
//    public function getReferrals() {
//        return $this->referrals;
//    }
//
//    /**
//     * Add notes
//     *
//     * @param \Mana\ClientBundle\Entity\Note $notes
//     * @return Household
//     */
//    public function addNote(\Mana\ClientBundle\Entity\Note $notes) {
//        $this->notes[] = $notes;
//
//        return $this;
//    }
//
//    /**
//     * Remove notes
//     *
//     * @param \Mana\ClientBundle\Entity\Note $notes
//     */
//    public function removeNote(\Mana\ClientBundle\Entity\Note $notes) {
//        $this->notes->removeElement($notes);
//    }
//
//    /**
//     * Get notes
//     *
//     * @return \Doctrine\Common\Collections\Collection 
//     */
//    public function getNotes() {
//        return $this->notes;
//    }

    public function setHead($member) {
        $this->head = $member;
        return $this;
    }

    public function getHead() {
        return $this->head;
    }


    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Appliance", inversedBy="households", cascade={"persist"})
     * @ORM\JoinTable(name="household_appliance",
     *      joinColumns={@ORM\JoinColumn(name="household_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="appliance_id", referencedColumnName="id")}
     *      ))
     */
    protected $appliances;

    public function addAppliance(Appliance $appliance) {
        $appliance->addHousehold($this); // synchronously updating inverse side
        $this->appliances[] = $appliance;
    }

    public function getAppliances() {
        return $this->appliances;
    }

    /**
     * @var \Mana\ClientBundle\Entity\IncomeSource
     *
     * @ORM\ManyToMany(targetEntity="IncomeSource", inversedBy="household")
     */
    protected $incomeSource;

    public function addIncomeSource(IncomeSource $incomeSource) {
        $incomeSource->addHousehold($this); // synchronously updating inverse side
        $this->incomeSource[] = $incomeSource;
    }


    /**
     * Remove incomesources
     *
     * @param \Mana\ClientBundle\Entity\IncomeSource $incomeSources
     */
    public function removeIncomeSource(IncomeSource $incomeSource) {
        $this->incomeSources->removeElement($incomeSource);
        $incomeSource->setHousehold(null);
    }

    public function getIncomeSource() {
        return $this->incomeSource;
    }
//
//    /**
//     * Set incomeSource
//     *
//     * @param \Mana\ClientBundle\Entity\IncomeSource $incomeSource
//     * @return Contact
//     */
//    public function setIncomeSource(IncomeSource $incomeSource = null) {
//        $this->incomeSource = $incomeSource;
//
//        return $this;
//    }
//
//    /**
//     * Get incomeSource
//     *
//     * @return \Mana\ClientBundle\Entity\IncomeSource 
//     */
//    public function getIncomeSource() {
//        return $this->incomeSource;
//    }

    /**
     * @var \Mana\ClientBundle\Entity\Housing
     *
     * @ORM\ManyToOne(targetEntity="Housing", inversedBy="households")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="housing_id", referencedColumnName="id")
     * })
     */
    protected $housing;

    /**
     * Set housing
     *
     * @param \Mana\ClientBundle\Entity\Housing $housing
     * @return Contact
     */
    public function setHousing(Housing $housing = null) {
        $this->housing = $housing;

        return $this;
    }

    /**
     * Get housing
     *
     * @return \Mana\ClientBundle\Entity\Housing 
     */
    public function getHousing() {
        return $this->housing;
    }

    /**
     * @var \Mana\ClientBundle\Entity\Notfoodstamp
     *
     * @ORM\ManyToOne(targetEntity="Notfoodstamp", inversedBy="households")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="notfoodstamp_id", referencedColumnName="id")
     * })
     */
    protected $notfoodstamp;

    /**
     * Set notfoodstamp
     *
     * @param \Mana\ClientBundle\Entity\Notfoodstamp $notfoodstamp
     * @return Contact
     */
    public function setNotfoodstamp(Notfoodstamp $notfoodstamp = null) {
        $this->notfoodstamp = $notfoodstamp;

        return $this;
    }

    /**
     * Get notfoodstamp
     *
     * @return \Mana\ClientBundle\Entity\Notfoodstamp 
     */
    public function getNotfoodstamp() {
        return $this->notfoodstamp;
    }

    /**
     * @var \Mana\ClientBundle\Entity\FsAmount
     *
     * @ORM\ManyToOne(targetEntity="Mana\ClientBundle\Entity\FsAmount", inversedBy="households")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fsamount_id", referencedColumnName="id")
     * })
     */
    protected $fsamount;

    /**
     * Set fsamount
     *
     * @param \Mana\ClientBundle\Entity\FsAmount $fsamount
     * @return Contact
     */
    public function setFsamount(FsAmount $fsamount = null) {
        $this->fsamount = $fsamount;

        return $this;
    }

    /**
     * Get fsamount
     *
     * @return \Mana\ClientBundle\Entity\Fsamount 
     */
    public function getFsamount() {
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

    public function addReason(Reason $reason) {
        $reason->addHousehold($this); // synchronously updating inverse side
        $this->reasons[] = $reason;
    }

    public function getReasons() {
        return $this->reasons;
    }
//
//    /**
//     * @var boolean
//     *
//     * @ORM\Column(name="pregnant", type="boolean", nullable=true)
//     */
//    protected $pregnant;
//
//    /**
//     * Set pregnant
//     *
//     * @param boolean $pregnant
//     * @return Household
//     */
//    public function setPregnant($pregnant) {
//        $this->pregnant = $pregnant;
//
//        return $this;
//    }
//
//    /**
//     * Get pregnant
//     *
//     * @return boolean 
//     */
//    public function getPregnant() {
//        return $this->pregnant;
//    }

//    /**
//     * @var \Mana\ClientBundle\Entity\Specialneed
//     *
//     * @ORM\ManyToOne(targetEntity="Specialneed", inversedBy="households")
//     * @ORM\JoinColumns({
//     *   @ORM\JoinColumn(name="specialneed_id", referencedColumnName="id")
//     * })
//     */
//    protected $specialneed;
//
//    /**
//     * Set specialneed
//     *
//     * @param \Mana\ClientBundle\Entity\Specialneed $specialneed
//     * @return Contact
//     */
//    public function setSpecialneed(Specialneed $specialneed = null)
//    {
//        $this->specialneed = $specialneed;
//
//        return $this;
//    }
//
//    /**
//     * Get specialneed
//     *
//     * @return \Mana\ClientBundle\Entity\Specialneed 
//     */
//    public function getSpecialneed()
//    {
//        return $this->specialneed;
//    }

    /**
     * @var boolean
     *
     * @ORM\Column(name="compliance", type="boolean", nullable=true)
     */
    protected $compliance;

    /**
     * Set compliance
     *
     * @param boolean $compliance
     * @return compliance
     */
    public function setCompliance($compliance) {
        $this->compliance = $compliance;

        return $this;
    }

    /**
     * Get compliance
     *
     * @return boolean 
     */
    public function getCompliance() {
        return $this->compliance;
    }

    /**
     * @var boolean
     *
     * @ORM\Column(name="compliance_date", type="date", nullable=true)
     * @Assert\Date(message="Compliance date invalid")
     * @Assert\NotBlank(message="Compliance date may not be empty")
     */
    protected $complianceDate;

    /**
     * Set complianceDate
     *
     * @param boolean $complianceDate
     * @return complianceDate
     */
    public function setComplianceDate($complianceDate) {
        $this->complianceDate = $complianceDate;

        return $this;
    }

    /**
     * Get complianceDate
     *
     * @return boolean 
     */
    public function getComplianceDate() {
        return $this->complianceDate;
    }

    /**
     * @var boolean
     *
     * @ORM\Column(name="shared", type="boolean", nullable=true)
     */
    protected $shared;

    /**
     * Set shared
     *
     * @param boolean $shared
     * @return shared
     */
    public function setShared($shared) {
        $this->shared = $shared;

        return $this;
    }

    /**
     * Get shared
     *
     * @return boolean 
     */
    public function getShared() {
        return $this->shared;
    }

    /**
     * @var boolean
     *
     * @ORM\Column(name="shared_date", type="date", nullable=true)
     * @Assert\Date(message="Shared date invalid")
     * @Assert\NotBlank(message="Shared date may not be empty")
     */
    protected $shareddate;

    /**
     * Set shareddate
     *
     * @param boolean $shareddate
     * @return shareddate
     */
    public function setShareddate($shareddate) {
        $this->shareddate = $shareddate;

        return $this;
    }

    /**
     * Get shareddate
     *
     * @return boolean 
     */
    public function getShareddate() {
        return $this->shareddate;
    }


    /**
     * @var \Mana\ClientBundle\Entity\Income
     *
     * @ORM\ManyToOne(targetEntity="Income", inversedBy="households")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="income_id", referencedColumnName="id")
     * })
     */
    protected $income;

    /**
     * Set income
     *
     * @param \Mana\ClientBundle\Entity\OneSideEntity $income
     * @return Contact
     */
    public function setIncome(Income $income = null)
    {
        $this->income = $income;

        return $this;
    }

    /**
     * Get income
     *
     * @return \Mana\ClientBundle\Entity\OneSideEntity 
     */
    public function getIncome()
    {
        return $this->income;
    }

    /**
     * @var \Mana\ClientBundle\Entity\OneSideEntity
     *
     * @ORM\ManyToOne(targetEntity="Center", inversedBy="households")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="center_id", referencedColumnName="id")
     * })
     * Assert\NotBlank(message="First site may not be empty")
     */
    protected $center;

    /**
     * Set center
     *
     * @param \Mana\ClientBundle\Entity\Center $center
     * @return Contact
     */
    public function setCenter(Center $center = null)
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
}
