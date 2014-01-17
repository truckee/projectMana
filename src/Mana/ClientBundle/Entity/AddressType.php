<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mana\ClientBundle\Entity\Address;

/**
 * AddressType
 *
 * @ORM\Table(name="address_type")
 * @ORM\Entity
 */
class AddressType {

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
     * @ORM\Column(name="address_type", type="string", nullable=false)
     */
    protected $addresstype;

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
     * Set addresstype
     *
     * @param integer $addresstype
     * @return addresstype
     */
    public function setAddresstype($addresstype) {
        $this->addresstype = $addresstype;

        return $this;
    }

    /**
     * Get addresstype
     *
     * @return integer 
     */
    public function getAddresstype() {
        return $this->addresstype;
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
     * @ORM\OneToMany(targetEntity="Address", mappedBy="addresstype")
     */
    protected $addresses;

    public function addAddress(Address $address) {
        $this->addresses[] = $address;
//        $address->addAddresstype($this);
    }

    public function getAddresses() {
        return $this->addresses;
    }

}
