<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ContactDesc
 *
 * @ORM\Table(name="contact_type")
 * @ORM\Entity
 */
class ContactDesc
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
     * @ORM\Column(name="contact_desc", type="string", length=45, nullable=true)
     */
    protected $contactDesc;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Mana\ClientBundle\Entity\Contact", mappedBy="contactDesc", cascade={"persist"})
     */
    protected $contacts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contacts = new ArrayCollection();
    }

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
     * Set contactDesc
     *
     * @param string $contactDesc
     * @return ContactDesc
     */
    public function setContactDesc($contactDesc)
    {
        $this->contactDesc = $contactDesc;

        return $this;
    }

    /**
     * Get contactDesc
     *
     * @return string
     */
    public function getContactDesc()
    {
        return $this->contactDesc;
    }

    /**
     * Add contacts
     *
     * @param \Mana\ClientBundle\Entity\Contact $contacts
     * @return ContactDesc
     */
    public function addContact(\Mana\ClientBundle\Entity\Contact $contacts)
    {
        $this->contacts[] = $contacts;

        return $this;
    }

    /**
     * Remove contacts
     *
     * @param \Mana\ClientBundle\Entity\Contact $contacts
     */
    public function removeContact(\Mana\ClientBundle\Entity\Contact $contacts)
    {
        $this->contacts->removeElement($contacts);
    }

    /**
     * Get contacts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * @var integer
     * @ORM\Column(name="enabled", type="boolean", nullable=true)
     */
    protected $enabled;

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
