<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Housing.
 *
 * @ORM\Table(name="housing")
 * @ORM\Entity
 */
class Housing
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="housing", type="string", nullable=false)
     * @Assert\NotBlank(message="Housing may not be blank")
     */
    protected $housing;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
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
     * Set housing.
     *
     * @param int $housing
     *
     * @return housing
     */
    public function setHousing($housing)
    {
        $this->housing = $housing;

        return $this;
    }

    /**
     * Get housing.
     *
     * @return int
     */
    public function getHousing()
    {
        return $this->housing;
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

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Household", mappedBy="housing")
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
}
