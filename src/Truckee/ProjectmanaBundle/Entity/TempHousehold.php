<?php
/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Entity\TempHousehold.php

namespace Truckee\ProjectmanaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TempHousehold
 * @ORM\Entity
 * @ORM\Table(name="temp_household", indexes={@ORM\Index(name="cid", columns={"hoh_id"})})
 * (id, hoh_id, res, size, date_added)
 */
class TempHousehold
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
     * @ORM\Column(name="hoh_id", type="integer", nullable=true)
     */
    protected $head;

    /**
     * @var int
     *
     * @ORM\Column(name="res", type="string", nullable=true)
     */
    protected $res;

    /**
     * @var int
     *
     * @ORM\Column(name="size", type="integer", nullable=true)
     */
    protected $size;

    /**
     * @var int
     *
     * @ORM\Column(name="size_text", type="string", nullable=true)
     */
    protected $sizeText;

    /**
     * @var date
     *
     * @ORM\Column(name="date_added", type="date", nullable=true)
     */
    protected $dateAdded;

    public function getId()
    {
        return $this->id;
    }

    public function getHead()
    {
        return $this->head;
    }

    public function getRes()
    {
        return $this->res;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getSizeText()
    {
        return $this->size_text;
    }

    public function getDateAdded()
    {
        return $this->dateAdded;
    }
    
}