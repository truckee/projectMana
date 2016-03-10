<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Member
 *
 * @ORM\Table(name="relationship")
 * @ORM\Entity
 */
class Relationship
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
     * Set id
     *
     * @param boolean $id
     * @return id
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return boolean
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="relation", type="string", nullable=true)
     */
    protected $relation;

    /**
     * Set relation
     *
     * @param boolean $relation
     * @return Member
     */
    public function setRelation($relation)
    {
        $this->relation = $relation;

        return $this;
    }

    /**
     * Get relation
     *
     * @return boolean
     */
    public function getRelation()
    {
        return $this->relation;
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
     * @ORM\OneToMany(targetEntity="Member", mappedBy="relation", cascade={"persist"})
     */
    protected $members;

    /**
     * Add members
     *
     * @param \Mana\ClientBundle\Entity\Member $members
     * @return Member
     */
    public function addMember(\Mana\ClientBundle\Entity\Member $member)
    {
        $this->members[] = $member;
        $member->setRelationship($this);
        return $this;
    }

    /**
     * Remove members
     *
     * @param \Mana\ClientBundle\Entity\Member $members
     */
    public function removeMember(\Mana\ClientBundle\Entity\Member $member)
    {
        $this->members->removeElement($member);
    }

    /**
     * Get members
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMembers()
    {
        return $this->members;
    }

}
