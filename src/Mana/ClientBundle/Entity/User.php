<?php

namespace Mana\ClientBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Mana\ClientBundle\Entity\Usertable
 *
 * @ORM\Table(name="usertable")
 * @ORM\Entity
 */
class User extends BaseUser
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string $fname
     *
     * @ORM\Column(name="fname", type="string", length=25, nullable=false)
     * @Assert\NotBlank(message = "First name may not be empty")
     */
    protected $fname;

    /**
     * @var string $sname
     *
     * @ORM\Column(name="sname", type="string", length=45, nullable=false)
     * @Assert\NotBlank(message = "Last name may not be empty")
     */
    protected $sname;

    /**
     * Set fname
     *
     * @param string $fname
     * @return Usertable
     */
    public function setFname($fname)
    {
        $this->fname = $fname;
    
        return $this;
    }

    /**
     * Get fname
     *
     * @return string 
     */
    public function getFname()
    {
        return $this->fname;
    }

    /**
     * Set sname
     *
     * @param string $sname
     * @return Usertable
     */
    public function setSname($sname)
    {
        $this->sname = $sname;
    
        return $this;
    }

    /**
     * Get sname
     *
     * @return string 
     */
    public function getSname()
    {
        return $this->sname;
    }
}
