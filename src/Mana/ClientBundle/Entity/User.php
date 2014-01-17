<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Mana\ClientBundle\Entity\Usertable
 *
 * @ORM\Table(name="usertable")
 * @ORM\Entity
 * @UniqueEntity(fields = "username", message = "This username name already exists")
 */
class User implements UserInterface, \Serializable
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
     * @var string $username
     *
     * @ORM\Column(name="username", type="string", length=12, nullable=true)
     * @Assert\NotBlank(message = "User name may not be empty")
     */
    protected $username;

    /**
     * @var string $userpass
     *
     * @ORM\Column(name="password", type="string", length=100, nullable=false)
     * @Assert\NotBlank(message="Password may not be empty")
     * @Assert\Length(
     *      min = "5",
     *      max = "12",
     *      minMessage = "Password must be at least 5 characters long",
     *      maxMessage = "Password cannot be longer than than 12 characters",
     *      groups = {"create"}
     * )
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $salt;

    /**
     * @var string $role
     *
     * @ORM\Column(name="role", type="string", nullable=false)
     */
    protected $role;

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
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=45, nullable=false)
     */
    protected $email;

    /**
     * @ORM\Column(name="is_active", type="integer")
     */
    protected $isActive;

    public function __construct()
    {
        $this->notes = new ArrayCollection;
        $this->referrals = new ArrayCollection;
        $this->incomeHistories = new ArrayCollection;
        $this->salt = md5(uniqid(null, true));
    }



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
     * Set username
     *
     * @param string $username
     * @return Usertable
     */
    public function setUsername($username)
    {
        $this->username = $username;
    
        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set userpass
     *
     * @param string $userpass
     * @return Usertable
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Get userpass
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return Usertable
     */
    public function setRole($role)
    {
        $this->role = $role;
    
        return $this;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
    }

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

    /**
     * Set email
     *
     * @param string $email
     * @return Usertable
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set isActive
     *
     * @param string $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get isActive
     *
     * @return string 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
    
//    /**
//    /**
//     * Set enabled
//     *
//     * @param string $enabled
//     * @return Usertable
//     */
//    public function setEnabled($enabled)
//    {
//        $this->enabled = $enabled;
//    
//        return $this;
//    }
//
//    /**
//     * Get enabled
//     *
//     * @return string 
//     */
//    public function getEnabled()
//    {
//        return $this->enabled;
//    }
//    
    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
        ) = unserialize($serialized);
    }
    
    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return array($this->getRole());
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }


//    /**
//     * @var \Doctrine\Common\Collections\Collection
//     *
//     * @ORM\OneToMany(targetEntity="Mana\ClientBundle\Entity\Note", mappedBy="user", cascade={"persist"})
//     */
//    protected $notes;
//
//    /**
//     * @var \Doctrine\Common\Collections\Collection
//     *
//     * @ORM\OneToMany(targetEntity="Mana\ClientBundle\Entity\Referral", mappedBy="user", cascade={"persist"})
//     */
//    protected $referrals;
//
//    /**
//     * @var \Doctrine\Common\Collections\Collection
//     *
//     * @ORM\OneToMany(targetEntity="Mana\ClientBundle\Entity\IncomeHistory", mappedBy="user", cascade={"persist"})
//     */
//    protected $incomeHistories;
//
//    /**
//     * Add notes
//     *
//     * @param \Mana\ClientBundle\Entity\Note $notes
//     * @return Usertable
//     */
//    public function addNote(\Mana\ClientBundle\Entity\Note $notes)
//    {
//        $this->notes[] = $notes;
//
//        return $this;
//    }
//
//    /**
//     * Remove notes
//     *
//     * @param \Mana\ClientBundle\Entity\Note $notes
//     */
//    public function removeNote(\Mana\ClientBundle\Entity\Note $notes)
//    {
//        $this->notes->removeElement($notes);
//    }
//
//    /**
//     * Get notes
//     *
//     * @return \Doctrine\Common\Collections\Collection 
//     */
//    public function getNotes()
//    {
//        return $this->notes;
//    }
//
//    /**
//     * Add referrals
//     *
//     * @param \Mana\ClientBundle\Entity\Referral $referrals
//     * @return Usertable
//     */
//    public function addReferral(\Mana\ClientBundle\Entity\Referral $referrals)
//    {
//        $this->referrals[] = $referrals;
//
//        return $this;
//    }
//
//    /**
//     * Remove referrals
//     *
//     * @param \Mana\ClientBundle\Entity\Referral $referrals
//     */
//    public function removeReferral(\Mana\ClientBundle\Entity\Referral $referrals)
//    {
//        $this->referrals->removeElement($referrals);
//    }
//
//    /**
//     * Get referrals
//     *
//     * @return \Doctrine\Common\Collections\Collection 
//     */
//    public function getReferrals()
//    {
//        return $this->referrals;
//    }
//    /**
//     * Add incomehistories
//     *
//     * @param \Mana\ClientBundle\Entity\IncomeHistory $incomehistories
//     * @return Usertable
//     */
//    public function addIncomeHistory(\Mana\ClientBundle\Entity\IncomeHistory $incomeHistory)
//    {
//        $this->incomeHistories[] = $incomeHistory;
//
//        return $this;
//    }
//
//    /**
//     * Remove incomehistories
//     *
//     * @param \Mana\ClientBundle\Entity\IncomeHistory $incomehistories
//     */
//    public function removeIncomeHistory(\Mana\ClientBundle\Entity\IncomeHistory $incomeHistory)
//    {
//        $this->incomeHistories->removeElement($incomeHistory);
//    }
//
//    /**
//     * Get incomehistories
//     *
//     * @return \Doctrine\Common\Collections\Collection 
//     */
//    public function getIncomeHistories()
//    {
//        return $this->incomeHistories;
//    }
}
?>
