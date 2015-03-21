<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mana\ClientBundle\Entity\Household;

/**
 * Fs_status
 *
 * @ORM\Table(name="fs_status")
 * @ORM\Entity(repositoryClass="Mana\ClientBundle\Entity\FsStatusRepository")
 */
class FsStatus
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
     * @ORM\Column(name="status", type="string", length=10)
     */
    private $status;


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
     * to String
     */
//    public function __toString()
//    {
//        return $this->status;
//    }
    
    /**
     * Set status
     *
     * @param string $status
     * @return Fs_status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Household", mappedBy="foodstamp")
     */
    protected $households;

    public function addHousehold(Household $household) {
        $this->households[] = $household;
//        $household->addFsAmount($this);
    }

    public function getHouseholds() {
        return $this->households;
    }
}
