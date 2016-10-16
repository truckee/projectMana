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
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Notfoodstamp.
 *
 * @ORM\Table(name="notfoodstamp")
 * @ORM\Entity(repositoryClass="Truckee\ProjectmanaBundle\Entity\NotfoodstampRepository")
 */
class Notfoodstamp
{
    public function __construct()
    {
        $this->households = ArrayCollection();
    }
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="notfoodstamp", type="string", nullable=false)
     * @Assert\NotBlank(message="Reason may not be blank")
     */
    protected $notfoodstamp;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    protected $enabled;

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
     * Set notfoodstamp.
     *
     * @param int $notfoodstamp
     *
     * @return notfoodstamp
     */
    public function setNotfoodstamp($notfoodstamp)
    {
        $this->notfoodstamp = $notfoodstamp;

        return $this;
    }

    /**
     * Get notfoodstamp.
     *
     * @return int
     */
    public function getNotfoodstamp()
    {
        return $this->notfoodstamp;
    }

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

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Household", mappedBy="notfoodstamp")
     */
    protected $households;

    public function addHousehold(Household $household)
    {
        $this->households[] = $household;
    }

    public function getHouseholds()
    {
        return $this->households;
    }
}
