<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Offence.
 *
 * @ORM\Table("offence")
 * @ORM\Entity
 */
class Offence
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="offence", type="string", length=45)
     * @Assert\NotBlank(message="Offense may not be blank")
     */
    private $offence;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

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
     * Set offence.
     *
     * @param string $offence
     *
     * @return Offence
     */
    public function setOffence($offence)
    {
        $this->offence = $offence;

        return $this;
    }

    /**
     * Get offence.
     *
     * @return string
     */
    public function getOffence()
    {
        return $this->offence;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return Offence
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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Member", mappedBy="offences")
     */
    protected $members;

    public function addMember(Member $member)
    {
        $this->members[] = $member;
    }

    public function getMembers()
    {
        return $this->members;
    }
}
