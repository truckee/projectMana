<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Member.
 *
 * @ORM\Table(name="relationship")
 * @ORM\Entity
 */
class Relationship
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
     * Set id.
     *
     * @param bool $id
     *
     * @return id
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id.
     *
     * @return bool
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="relation", type="string", nullable=true)
     * @Assert\NotBlank(message="Relationship may not be blank")
     */
    protected $relation;

    /**
     * Set relation.
     *
     * @param bool $relation
     *
     * @return Member
     */
    public function setRelation($relation)
    {
        $this->relation = $relation;

        return $this;
    }

    /**
     * Get relation.
     *
     * @return bool
     */
    public function getRelation()
    {
        return $this->relation;
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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Member", mappedBy="relation", cascade={"persist"})
     */
    protected $members;

    /**
     * Add members.
     *
     * @param \Mana\ClientBundle\Entity\Member $members
     *
     * @return Member
     */
    public function addMember(\Mana\ClientBundle\Entity\Member $member)
    {
        $this->members[] = $member;
        $member->setRelationship($this);

        return $this;
    }

    /**
     * Remove members.
     *
     * @param \Mana\ClientBundle\Entity\Member $members
     */
    public function removeMember(\Mana\ClientBundle\Entity\Member $member)
    {
        $this->members->removeElement($member);
    }

    /**
     * Get members.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMembers()
    {
        return $this->members;
    }
}
