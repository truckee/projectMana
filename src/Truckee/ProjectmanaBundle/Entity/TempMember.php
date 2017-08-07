<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Entity\TempMember.php

namespace Truckee\ProjectmanaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TempMember
 * @ORM\Entity
 * @ORM\Table(name="temp_member")
 * id, household_id, sex, age, ethnicity_id
 */
class TempMember
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
     * @ORM\Column(name="household_id", type="integer", nullable=true)
     */
    protected $household;

    /**
     * @var string
     *
     * @ORM\Column(name="sex", type="string", length=45, nullable=true)
     */
    protected $sex;

    /**
     * @var int
     *
     * @ORM\Column(name="age", type="integer", nullable=true)
     */
    protected $age;

    /**
     *   @ORM\Column(name="ethnicity_id", type="integer", nullable=true)
     */
    protected $ethnicity;

    public function getHousehold()
    {
        return $this->household;
    }

    public function getSex()
    {
        return $this->sex;
    }

    public function getAge()
    {
        return $this->age;
    }
}
