<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Income
 *
 * @ORM\Table(name="income")
 * @ORM\Entity(repositoryClass="Mana\ClientBundle\Entity\IncomeRepository")
 */
class Income
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
     * @ORM\Column(name="income", type="string", length=45, nullable=true)
     */
    protected $income;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Household", mappedBy="income")
     */
    protected $households;

    public function addHousehold(Household $household) {
        $this->households[] = $household;
    }

    public function getHouseholds() {
        return $this->households;
    }

    /**
     * @var integer 
     * @ORM\Column(name="enabled", type="integer", nullable=true)
     */
    protected $enabled;

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
     * Set income
     *
     * @param string $income
     * @return Income
     */
    public function setIncome($income)
    {
        $this->income = $income;

        return $this;
    }

    /**
     * Get income
     *
     * @return string 
     */
    public function getIncome()
    {
        return $this->income;
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
}
