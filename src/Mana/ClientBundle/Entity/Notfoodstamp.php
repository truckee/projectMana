<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mana\ClientBundle\Entity\Household;

/**
 * Notfoodstamp
 *
 * @ORM\Table(name="notfoodstamp")
 * @ORM\Entity
 */
class Notfoodstamp {

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
     * @ORM\Column(name="notfoodstamp", type="string", nullable=false)
     */
    protected $notfoodstamp;

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
     * Set notfoodstamp
     *
     * @param integer $notfoodstamp
     * @return notfoodstamp
     */
    public function setNotfoodstamp($notfoodstamp) {
        $this->notfoodstamp = $notfoodstamp;

        return $this;
    }

    /**
     * Get notfoodstamp
     *
     * @return integer 
     */
    public function getNotfoodstamp() {
        return $this->notfoodstamp;
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
     * @ORM\OneToMany(targetEntity="Household", mappedBy="notfoodstamp")
     */
    protected $households;

    public function addHousehold(Household $household) {
        $this->households[] = $household;
//        $household->addNotfoodstamp($this);
    }

    public function getHouseholds() {
        return $this->households;
    }

}
