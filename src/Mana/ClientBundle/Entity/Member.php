<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Mana\ClientBundle\Validator\Constraints as ManaAssert;

/**
 * Client.
 *
 * @ORM\Table(name="member", indexes={
 *      @ORM\Index(name="idx_client_household_idx", columns={"household_id"}),
 *      @ORM\Index(name="idx_client_ethnicity_idx", columns={"ethnicity_id"}),
 *      @ORM\Index(columns={"fname", "sname"}, flags={"fulltext"})})
 * @ORM\Entity(repositoryClass="Mana\ClientBundle\Entity\MemberRepository")
 */
class Member
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
     * @ORM\Column(name="fname", type="string", length=45, nullable=true)
     * @Assert\NotBlank(message = "First name may not be blank")
     */
    protected $fname;

    /**
     * @var string
     *
     * @ORM\Column(name="sname", type="string", length=45, nullable=true)
     * @Assert\NotBlank(message = "Last name may not be blank")
     */
    protected $sname;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dob", type="date", nullable=true)
     * @Assert\NotBlank(message = "DOB must be valid date or age")
     * @ManaAssert\NotFutureDate(message="Future date not allowed")
     */
    protected $dob;

    /**
     * @var string
     *
     * @ORM\Column(name="include", type="string", length=5, nullable=true)
     */
    protected $include;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="exclude_date", type="date", nullable=true)
     */
    protected $excludeDate;

    /**
     * @var string
     *
     * @ORM\Column(name="sex", type="string", length=45, nullable=true)
     * @Assert\NotBlank(message = "Gender may not be blank")
     */
    protected $sex;

    /**
     * @var \Mana\ClientBundle\Entity\Ethnicity
     *
     * @ORM\ManyToOne(targetEntity="Mana\ClientBundle\Entity\Ethnicity", inversedBy="members", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ethnicity_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank(message = "Ethnicity may not be blank")
     */
    protected $ethnicity;

    /**
     * @var \Mana\ClientBundle\Entity\Household
     *
     * @ORM\ManyToOne(targetEntity="Mana\ClientBundle\Entity\Household", inversedBy="members", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="household_id", referencedColumnName="id")
     * })
     */
    protected $household;

    /**
     * Set fname.
     *
     * @param string $fname
     *
     * @return Member
     */
    public function setFname($fname)
    {
        $this->fname = $fname;

        return $this;
    }

    /**
     * Get fname.
     *
     * @return string
     */
    public function getFname()
    {
        return $this->fname;
    }

    /**
     * Set sname.
     *
     * @param string $sname
     *
     * @return Member
     */
    public function setSname($sname)
    {
        $this->sname = $sname;

        return $this;
    }

    /**
     * Get sname.
     *
     * @return string
     */
    public function getSname()
    {
        return $this->sname;
    }

    /**
     * Set dob.
     *
     * @param \DateTime $dob
     *
     * @return Member
     */
    public function setDob($dob)
    {
        $this->dob = $dob;

        return $this;
    }

    /**
     * Get dob.
     *
     * @return \DateTime
     */
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * Set include.
     *
     * @param string $include
     *
     * @return Member
     */
    public function setInclude($include)
    {
        $this->include = $include;

        return $this;
    }

    /**
     * Get include.
     *
     * @return string
     */
    public function getInclude()
    {
        return $this->include;
    }

    /**
     * Set excludeDate.
     *
     * @param \DateTime $excludeDate
     *
     * @return Member
     */
    public function setExcludeDate($excludeDate)
    {
        $this->excludeDate = $excludeDate;

        return $this;
    }

    /**
     * Get excludeDate.
     *
     * @return \DateTime
     */
    public function getExcludeDate()
    {
        return $this->excludeDate;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return Member
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set sex.
     *
     * @param string $sex
     *
     * @return Member
     */
    public function setSex($sex)
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * Get sex.
     *
     * @return string
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Set ethnicity.
     *
     * @param \Mana\ClientBundle\Entity\Ethnicity $ethnicity
     *
     * @return Member
     */
    public function setEthnicity(\Mana\ClientBundle\Entity\Ethnicity $ethnicity = null)
    {
        $this->ethnicity = $ethnicity;

        return $this;
    }

    /**
     * Get ethnicity.
     *
     * @return \Mana\ClientBundle\Entity\Ethnicity
     */
    public function getEthnicity()
    {
        return $this->ethnicity;
    }

    /**
     * Set household.
     *
     * @param \Mana\ClientBundle\Entity\Household $household
     *
     * @return Member
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

    /**
     * @var \Mana\ClientBundle\Entity\Relationship
     *
     * @ORM\ManyToOne(targetEntity="Relationship", inversedBy="members")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="relationship_id", referencedColumnName="id")
     * })
     */
    protected $relation;

    /**
     * Set relation.
     *
     * @param \Mana\ClientBundle\Entity\Relationship $relation
     *
     * @return Contact
     */
    public function setRelation(Relationship $relation = null)
    {
        $this->relation = $relation;

        return $this;
    }

    /**
     * Get relation.
     *
     * @return \Mana\ClientBundle\Entity\Relationship
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Offence", inversedBy="members", cascade={"persist"})
     * @ORM\JoinTable(name="member_offence",
     *      joinColumns={@ORM\JoinColumn(name="member_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="offence_id", referencedColumnName="id")}
     *      ))
     */
    protected $offences;

    public function addOffence(Offence $offence)
    {
        $offence->addMember($this); // synchronously updating inverse side
        $this->offences[] = $offence;
    }

    /**
     * Remove offences.
     *
     * @param \Mana\ClientBundle\Entity\Offence $offences
     */
    public function removeOffence(Offence $offence)
    {
        $this->offences->removeElement($offence);
        $offence->setMember(null);
    }

    public function getOffences()
    {
        return $this->offences;
    }

    /**
     * @var \Mana\ClientBundle\Entity\Work
     *
     * @ORM\ManyToOne(targetEntity="Work", inversedBy="members")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="work_id", referencedColumnName="id")
     * })
     */
    protected $work;

    /**
     * Set work.
     *
     * @param \Mana\ClientBundle\Entity\Work $work
     *
     * @return Contact
     */
    public function setWork(Work $work = null)
    {
        $this->work = $work;

        return $this;
    }

    /**
     * Get work.
     *
     * @return \Mana\ClientBundle\Entity\Work
     */
    public function getWork()
    {
        return $this->work;
    }
}
