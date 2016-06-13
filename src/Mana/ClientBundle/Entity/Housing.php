<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mana\ClientBundle\Entity\Household;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Housing
 *
 * @ORM\Table(name="housing")
 * @ORM\Entity
 */
class Housing {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="housing", type="string", nullable=false)
     * @Assert\NotBlank(message="Housing may not be blank")
     */
    protected $housing;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    protected $enabled;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set housing
     *
     * @param integer $housing
     * @return housing
     */
    public function setHousing($housing) {
        $this->housing = $housing;

        return $this;
    }

    /**
     * Get housing
     *
     * @return integer 
     */
    public function getHousing() {
        return $this->housing;
    }

    /**
     * Set enabled
     *
     * @param integer $enabled
     * @return enabled
     */
    public function setEnabled($enabled) {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return integer 
     */
    public function getEnabled() {
        return $this->enabled;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Household", mappedBy="housing")
     */
    protected $households;

    public function addHousehold(Household $household) {
        $this->households[] = $household;
//        $household->addHousing($this);
    }

    public function getHouseholds() {
        return $this->households;
    }

}
