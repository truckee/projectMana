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
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Truckee\ProjectmanaBundle\Entity\Usertable.
 *
 * @ORM\Table(name="usertable")
 * @ORM\Entity
 */
class User extends BaseUser implements EncoderAwareInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="fname", type="string", length=25, nullable=false)
     * @Assert\NotBlank(message = "First name may not be empty")
     */
    protected $fname;

    /**
     * @var string
     *
     * @ORM\Column(name="sname", type="string", length=45, nullable=false)
     * @Assert\NotBlank(message = "Last name may not be empty")
     */
    protected $sname;

    /**
     * Set fname.
     *
     * @param string $fname
     *
     * @return Usertable
     */
    public function setFname($fname)
    {
        $this->fname = $fname;

        return $this;
    }

    /**
     * Get fname.
     *
     * @return string
     */
    public function getFname()
    {
        return $this->fname;
    }

    /**
     * Set sname.
     *
     * @param string $sname
     *
     * @return Usertable
     */
    public function setSname($sname)
    {
        $this->sname = $sname;

        return $this;
    }

    /**
     * Get sname.
     *
     * @return string
     */
    public function getSname()
    {
        return $this->sname;
    }

    public function hasRoleAdmin()
    {
        return ($this->hasRole('ROLE_ADMIN')) ? 'Yes' : 'No';
    }

    public function setHasRoleAdmin($isAdmin)
    {
        if ('Yes' === $isAdmin && 'No' === $this->hasRole('ROLE_ADMIN')) {
            $this->addRole('ROLE_ADMIN');
        }
        if ('No' === $isAdmin && 'Yes' == $this->hasRole('ROLE_ADMIN')) {
            $this->removeRole('ROLE_ADMIN');
        }
        $this->isAdmin = $isAdmin;
    }
    
    /**
     * @ORM\Column(name="encoder_name", type="string")
     */
    private $encoderName = 'new';
    
    public function getEncoderName() {
        return $this->encoderName;
    }
    
    public function setEncoderName($name) {
        $this->encoderName = $name;
        
        return $this;
    }
}
