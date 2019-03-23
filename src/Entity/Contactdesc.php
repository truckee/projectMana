<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Contactdesc.
 *
 * @ORM\Table(name="contactdesc")
 * @ORM\Entity
 */
class Contactdesc
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
     * @ORM\Column(name="contactdesc", type="string", length=45, nullable=true)
     * @Assert\NotBlank(message="Type may not be blank")
     */
    protected $contactdesc;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Contact", mappedBy="contactdesc", cascade={"persist"})
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
     * Set contactdesc.
     *
     * @param string $contactdesc
     *
     * @return Contactdesc
     */
    public function setContactdesc($contactdesc)
    {
        $this->contactdesc = $contactdesc;

        return $this;
    }

    /**
     * Get contactdesc.
     *
     * @return string
     */
    public function getContactdesc()
    {
        return $this->contactdesc;
    }

    /**
     * Add contacts.
     *
     * @param \App\Entity\Contact $contacts
     *
     * @return Contactdesc
     */
    public function addContact(\App\Entity\Contact $contacts)
    {
        $this->contacts[] = $contacts;

        return $this;
    }

    /**
     * Remove contacts.
     *
     * @param \App\Entity\Contact $contacts
     */
    public function removeContact(\App\Entity\Contact $contacts)
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
