<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Household", mappedBy="incomeSource")
     */
    protected $household;

    public function addHousehold(Household $household)
    {
        $this->households[] = $household;
//        $household->addAppliance($this);
    }

    public function getHouseholds()
    {
        return $this->households;
    }

    /**
     * @var integer
     * @ORM\Column(name="enabled", type="boolean", nullable=true)
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

}
