<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Center.
 *
 * @ORM\Table(name="center")
 * @ORM\Entity(repositoryClass="Mana\ClientBundle\Entity\CenterRepository")
 */
class Center
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
     * @ORM\Column(name="center", type="string", length=20, nullable=true)
     * @Assert\NotBlank(message="Site may not be blank", groups={"Options"})
     */
    protected $center;

    /**
     * @var \Mana\ClientBundle\Entity\County
     *
     * @ORM\ManyToOne(targetEntity="Mana\ClientBundle\Entity\County", inversedBy="centers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="county_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank(message="County may not be blank", groups={"Options"})
     */
    protected $county;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Mana\ClientBundle\Entity\Contact", mappedBy="center", cascade={"persist"})
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
     * Set center.
     *
     * @param string $center
     *
     * @return Center
     */
    public function setCenter($center)
    {
        $this->center = $center;

        return $this;
    }

    /**
     * Get center.
     *
     * @return string
     */
    public function getCenter()
    {
        return $this->center;
    }

    /**
     * Set county.
     *
     * @param string $county
     *
     * @return Center
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
     * Add contacts.
     *
     * @param \Mana\ClientBundle\Entity\Contact $contacts
     *
     * @return Center
     */
    public function addContact(\Mana\ClientBundle\Entity\Contact $contacts)
    {
        $this->contacts[] = $contacts;

        return $this;
    }

    /**
     * Remove contacts.
     *
     * @param \Mana\ClientBundle\Entity\Contact $contacts
     */
    public function removeContact(\Mana\ClientBundle\Entity\Contact $contacts)
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

    /**
     * @codeCoverageIgnore
     *
     * @ORM\OneToMany(targetEntity="Household", mappedBy="center", cascade={"persist"})
     */
    protected $households;

    /**
     * Add households.
     *
     * @codeCoverageIgnore
     *
     * @param \Mana\ClientBundle\Entity\Household $households
     *
     * @return Household
     */
    public function addHousehold(\Mana\ClientBundle\Entity\Household $household)
    {
        $this->households[] = $household;
        $household->setManySideSingular($this);

        return $this;
    }

    /**
     * Remove households.
     *
     * @codeCoverageIgnore
     *
     * @param \Mana\ClientBundle\Entity\Household $households
     */
    public function removeHousehold(\Mana\ClientBundle\Entity\Household $household)
    {
        $this->households->removeElement($household);
    }

    /**
     * Get households.
     *
     * @codeCoverageIgnore
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHouseholds()
    {
        return $this->households;
    }
}
