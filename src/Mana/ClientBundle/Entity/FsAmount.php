<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mana\ClientBundle\Entity\Household;

/**
 * FsAmount
 *
 * @ORM\Table(name="fs_amount")
 * @ORM\Entity
 */
class FsAmount {

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
     * @ORM\Column(name="amount", type="string", nullable=false)
     */
    protected $amount;

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
     * Set amount
     *
     * @param integer $amount
     * @return amount
     */
    public function setAmount($amount) {
        $this->fsamount = $amount;

        return $this;
    }

    /**
     * Get fsamount
     *
     * @return integer 
     */
    public function getAmount() {
        return $this->amount;
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
     * @ORM\OneToMany(targetEntity="Household", mappedBy="fsamount")
     */
    protected $households;

    public function addHousehold(Household $household) {
        $this->households[] = $household;
//        $household->addFsAmount($this);
    }

    public function getHouseholds() {
        return $this->households;
    }

}
