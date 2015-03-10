<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Offence
 *
 * @ORM\Table("offence")
 * @ORM\Entity
 */
class Offence
{
    /**
     * @var integer
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
     */
    private $offence;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;


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
     * Set offence
     *
     * @param string $offence
     * @return Offence
     */
    public function setOffence($offence)
    {
        $this->offence = $offence;
    
        return $this;
    }

    /**
     * Get offence
     *
     * @return string 
     */
    public function getOffence()
    {
        return $this->offence;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Offence
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
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

    public function addMember(Member $member) {
        $this->members[] = $member;
    }

    public function getMembers() {
        return $this->members;
    }
}
