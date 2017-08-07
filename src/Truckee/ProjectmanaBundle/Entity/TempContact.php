<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Entity\TempContact.php

namespace Truckee\ProjectmanaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TempContact
 * @ORM\Entity
 * @ORM\Table(name="temp_contact", indexes={@ORM\Index(name="idx_cid", columns={"household_id"}), @ORM\Index(name="idx_vid", columns={"contact_type_id"}), @ORM\Index(name="idx_date", columns={"contact_date"})})
 */
class TempContact
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
     * @var int
     *
     * @ORM\Column(name="contact_type_id", type="integer", nullable=true)
     */
    protected $contactType;

    /**
     * @var int
     *
     * @ORM\Column(name="household_id", type="integer", nullable=true)
     */
    protected $household;

    /**
     * @var date
     *
     * @ORM\Column(name="contact_date", type="date", nullable=true)
     */
    protected $contactDate;

    /**
     * @var bool
     *
     * @ORM\Column(name="first", type="boolean", nullable=true)
     */
    protected $first;

    /**
     * @var int
     *
     * @ORM\Column(name="center_id", type="integer", nullable=true)
     */
    protected $center;

    /**
     * @var int
     *
     * @ORM\Column(name="county_id", type="integer", nullable=true)
     */
    protected $county;

    public function getId()
    {
        return $this->id;
    }

    public function getContactType()
    {
        return $this->contactType;
    }

    public function getHousehold()
    {
        return $this->household;
    }

    public function getContactDate()
    {
        return $this->contactDate;
    }

    public function getFirst()
    {
        return $this->first;
    }

    public function getCenter()
    {
        return $this->center;
    }

    public function getCounty()
    {
        return $this->county;
    }
}
