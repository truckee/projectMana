<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Work
 *
 * @ORM\Table(name="work")
 * @ORM\Entity(repositoryClass="Mana\ClientBundle\Entity\WorkRepository")
 */
class Work {

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
     * @ORM\Column(name="work", type="string", nullable=false)
     */
    protected $work;

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
     * Set work
     *
     * @param integer $work
     * @return work
     */
    public function setWork($work) {
        $this->work = $work;

        return $this;
    }

    /**
     * Get work
     *
     * @return integer 
     */
    public function getWork() {
        return $this->work;
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
     * @ORM\OneToMany(targetEntity="Member", mappedBy="work")
     */
    protected $members;

    public function addMember(Member $member) {
        $this->members[] = $member;
    }

    public function getMembers() {
        return $this->members;
    }

}
