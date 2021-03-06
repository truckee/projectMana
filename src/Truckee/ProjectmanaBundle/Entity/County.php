<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Truckee\ProjectmanaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * County.
 *
 * @ORM\Table(name="county")
 * @ORM\Entity(repositoryClass="Truckee\ProjectmanaBundle\Entity\CountyRepository")
 */
class County
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
     * @ORM\Column(name="county", type="string", length=15, nullable=false)
     * @Assert\NotBlank(message="County may not be blank", groups={"Options"})
     */
    protected $county;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Truckee\ProjectmanaBundle\Entity\Center", mappedBy="county", cascade={"persist"})
     */
    protected $centers;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Truckee\ProjectmanaBundle\Entity\Contact", mappedBy="county", cascade={"persist"})
     */
    protected $contacts;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->centers = new ArrayCollection();
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
     * Set county.
     *
     * @param string $county
     *
     * @return County
     */
    public function setCounty($county)
    {
        $this->county = $county;

        return $this;
    }

    /**
     * Get county.
     *
     * @return string
     */
    public function getCounty()
    {
        return $this->county;
    }

    /**
     * Add centers.
     *
     * @param \Truckee\ProjectmanaBundle\Entity\Center $centers
     *
     * @return County
     */
    public function addCenter(\Truckee\ProjectmanaBundle\Entity\Center $centers)
    {
        $this->centers[] = $centers;

        return $this;
    }

    /**
     * Remove centers.
     *
     * @param \Truckee\ProjectmanaBundle\Entity\Center $centers
     */
    public function removeCenter(\Truckee\ProjectmanaBundle\Entity\Center $centers)
    {
        $this->centers->removeElement($centers);
    }

    /**
     * Get centers.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCenters()
    {
        return $this->centers;
    }

    /**
     * Add contacts.
     *
     * @param \Truckee\ProjectmanaBundle\Entity\Contact $contacts
     *
     * @return County
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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Address", mappedBy="county", cascade={"persist"})
     */
    protected $addresses;

    /**
     * Add addresses.
     *
     * @param \Truckee\ProjectmanaBundle\Entity\Address $addresses
     *
     * @return Household
     */
    public function addAddress(\Truckee\ProjectmanaBundle\Entity\Address $address)
    {
        $this->addresses[] = $address;
        $address->setCounty($this);

        return $this;
    }

    /**
     * Remove addresses.
     *
     * @param \Truckee\ProjectmanaBundle\Entity\Address $addresses
     */
    public function removeAddress(\Truckee\ProjectmanaBundle\Entity\Address $address)
    {
        $this->addresses->removeElement($address);
    }

    /**
     * Get addresses.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=true)
     */
    protected $enabled;

    /**
     * Set enabled.
     *
     * @param bool $enabled
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
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    public function __toString()
    {
        return $this->getCounty();
    }
}
