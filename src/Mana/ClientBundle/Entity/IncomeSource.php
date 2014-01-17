<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * IncomeSource
 *
 * @ORM\Table(name="income_source")
 * @ORM\Entity
 */
class IncomeSource
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
     * @ORM\Column(name="income_source", type="string", length=45, nullable=true)
     */
    protected $incomeSource;

//    /**
//     * @var string
//     *
//     * @ORM\Column(name="income_abbr", type="string", length=45, nullable=true)
//     */
//    protected $incomeAbbreviation;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Household", mappedBy="incomeSource")
     */
    protected $household;

    public function addHousehold(Household $household) {
        $this->households[] = $household;
//        $household->addAppliance($this);
    }

    public function getHouseholds() {
        return $this->households;
    }    
    
    /**
     * @var integer 
     * @ORM\Column(name="enabled", type="integer", nullable=true)
     */
    protected $enabled;

//    /**
//     * Constructor
//     */
//    public function __construct()
//    {
//        $this->incomeHistories = new ArrayCollection();
//    }


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
     * Set incomeSource
     *
     * @param string $incomeSource
     * @return IncomeSource
     */
    public function setIncomeSource($incomeSource)
    {
        $this->incomeSource = $incomeSource;

        return $this;
    }

    /**
     * Get incomeSource
     *
     * @return string 
     */
    public function getIncomeSource()
    {
        return $this->incomeSource;
    }


    /**
     * Set enabled
     *
     * @param integer $enabled
     * @return enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }
    
    /**
     * Get enabled
     *
     * @return integer 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }
//
//    /**
//     * Set incomeAbbreviation
//     *
//     * @param string $incomeAbbreviation
//     * @return IncomeSource
//     */
//    public function setIncomeAbbreviation($incomeAbbreviation)
//    {
//        $this->incomeAbbreviation = $incomeAbbreviation;
//
//        return $this;
//    }
//
//    /**
//     * Get incomeAbbreviation
//     *
//     * @return string 
//     */
//    public function getIncomeAbbreviation()
//    {
//        return $this->incomeAbbreviation;
//    }

//    /**
//     * Add incomeHistories
//     *
//     * @param \Mana\ClientBundle\Entity\IncomeHistory $incomeHistories
//     * @return IncomeSource
//     */
//    public function addIncomeHistory(\Mana\ClientBundle\Entity\IncomeHistory $incomeHistories)
//    {
//        $this->incomeHistories[] = $incomeHistories;
//
//        return $this;
//    }
//
//    /**
//     * Remove incomeHistories
//     *
//     * @param \Mana\ClientBundle\Entity\IncomeHistory $incomeHistories
//     */
//    public function removeIncomeHistory(\Mana\ClientBundle\Entity\IncomeHistory $incomeHistories)
//    {
//        $this->incomeHistories->removeElement($incomeHistories);
//    }
//
//    /**
//     * Get incomeHistories
//     *
//     * @return \Doctrine\Common\Collections\Collection 
//     */
//    public function getIncomeHistories()
//    {
//        return $this->incomeHistories;
//    }
}
