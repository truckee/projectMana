<?php
namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
//use Doctrine\Common\Collections\ArrayCollection;

/**
 * Household
 *
 * @ORM\Table(name="specialneed")
 * @ORM\Entity
 */
class Specialneed {
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Set id
     *
     * @param boolean $id
     * @return id
     */
    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return boolean 
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="need", type="string", nullable=true)
     */
    protected $need;

    /**
     * Set need
     *
     * @param boolean $need
     * @return Household
     */
    public function setNeed($need) {
        $this->need = $need;

        return $this;
    }

    /**
     * Get need
     *
     * @return boolean 
     */
    public function getNeed() {
        return $this->need;
    }


    /**
     * @var boolean
     *
     * @ORM\Column(name="order", type="integer", nullable=true)
     */
    protected $order;

    /**
     * Set order
     *
     * @param boolean $order
     * @return Household
     */
    public function setOrder($order) {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return boolean 
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Household", mappedBy="specialneed", cascade={"persist"})
     */
    protected $households;


    /**
     * Add households
     *
     * @param \Mana\ClientBundle\Entity\Household $households
     * @return Household
     */
    public function addHousehold(\Mana\ClientBundle\Entity\Household $household) {
        $this->households[] = $household;
        $household->setSpecialneed($this);
        return $this;
    }

    /**
     * Remove households
     *
     * @param \Mana\ClientBundle\Entity\Household $households
     */
    public function removeHousehold(\Mana\ClientBundle\Entity\Household $household) {
        $this->households->removeElement($household);
    }

    /**
     * Get households
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHouseholds() {
        return $this->households;
    }

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=true)
     */
    protected $enabled;

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return enabled
     */
    public function setEnabled($enabled) {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled() {
        return $this->enabled;
    }
}