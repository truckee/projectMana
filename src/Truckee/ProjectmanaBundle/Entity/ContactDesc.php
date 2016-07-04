<?php

namespace Truckee\ProjectmanaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ContactDesc.
 *
 * @ORM\Table(name="contact_type")
 * @ORM\Entity
 */
class ContactDesc
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
     * @ORM\Column(name="contact_desc", type="string", length=45, nullable=true)
     * @Assert\NotBlank(message="Type may not be blank")
     */
    protected $contactDesc;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Truckee\ProjectmanaBundle\Entity\Contact", mappedBy="contactDesc", cascade={"persist"})
     */
    protected $contacts;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->contacts = new ArrayCollection();
    }

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
     * Set contactDesc.
     *
     * @param string $contactDesc
     *
     * @return ContactDesc
     */
    public function setContactDesc($contactDesc)
    {
        $this->contactDesc = $contactDesc;

        return $this;
    }

    /**
     * Get contactDesc.
     *
     * @return string
     */
    public function getContactDesc()
    {
        return $this->contactDesc;
    }

    /**
     * Add contacts.
     *
     * @param \Truckee\ProjectmanaBundle\Entity\Contact $contacts
     *
     * @return ContactDesc
     */
    public function addContact(\Truckee\ProjectmanaBundle\Entity\Contact $contacts)
    {
        $this->contacts[] = $contacts;

        return $this;
    }

    /**
     * Remove contacts.
     *
     * @param \Truckee\ProjectmanaBundle\Entity\Contact $contacts
     */
    public function removeContact(\Truckee\ProjectmanaBundle\Entity\Contact $contacts)
    {
        $this->contacts->removeElement($contacts);
    }

    /**
     * Get contacts.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * @var int
     * @ORM\Column(name="enabled", type="boolean", nullable=true)
     */
    protected $enabled;

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
}
