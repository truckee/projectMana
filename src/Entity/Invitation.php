<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="invitation")
 * @ORM\Entity
 * @UniqueEntity(fields="username", message="Username already exists")
 */
class Invitation {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="Email address is required")
     * @Assert\Email(message="A valid email address is required")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="First name is required")
     */
    private $fname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Last name is required")
     */
    private $sname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Username is required")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    public function getId(): ?int {
        return $this->id;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(?string $email): self {
        $this->email = $email;

        return $this;
    }

    /**
     * Get sname.
     *
     * @return string
     */
    public function getSname() {
        return $this->sname;
    }

    public function setSname(?string $sname): self {
        $this->sname = $sname;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    public function setUsername(?string $username): self {
        $this->username = $username;

        return $this;
    }

    /**
     * Get fname.
     *
     * @return string
     */
    public function getFname() {
        return $this->fname;
    }

    public function setFname(?string $fname): self {
        $this->fname = $fname;

        return $this;
    }

    /**
     * Get token.
     *
     * @return string
     */
    public function getToken() {
        return $this->token;
    }

    public function setToken(?string $token): self {
        $this->token = $token;

        return $this;
    }
}
