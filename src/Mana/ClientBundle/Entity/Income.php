<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Income.
 *
 * @ORM\Table(name="income")
 * @ORM\Entity(repositoryClass="Mana\ClientBundle\Entity\IncomeRepository")
 */
class Income
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
     * @ORM\Column(name="income", type="string", length=45, nullable=true)
     * @Assert\NotBlank(message="Bracket may not be blank")
     */
    protected $income;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Household", mappedBy="income")
     */
    protected $households;

    public function addHousehold(Household $household)
    {
        $this->households[] = $household;
    }

    public function getHouseholds()
    {
        return $this->households;
    }

    /**
     * @var int
     * @ORM\Column(name="enabled", type="boolean", nullable=true)
     */
    protected $enabled;

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
     * Set income.
     *
     * @param string $income
     *
     * @return Income
     */
    public function setIncome($income)
    {
        $this->income = $income;

        return $this;
    }

    /**
     * Get income.
     *
     * @return string
     */
    public function getIncome()
    {
        return $this->income;
    }

    /**
     * Set enabled.
     *
     * @param int $enabled
     *
     * @return enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled.
     *
     * @return int
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    public function bracket()
    {
        return $this->income;
    }
}
