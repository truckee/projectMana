<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * FsAmount.
 *
 * @ORM\Table(name="fs_amount")
 * @ORM\Entity(repositoryClass="Mana\ClientBundle\Entity\FsAmountRepository")
 */
class FsAmount
{
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
     * @ORM\Column(name="amount", type="string", nullable=false)
     * @Assert\NotBlank(message="Bracket may not be blank")
     */
    protected $amount;

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
     * Set amount.
     *
     * @param int $amount
     *
     * @return amount
     */
    public function setAmount($amount)
    {
        $this->fsamount = $amount;

        return $this;
    }

    /**
     * Get fsamount.
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
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
     * @ORM\OneToMany(targetEntity="Household", mappedBy="fsamount")
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

    public function bracket()
    {
        return $this->amount;
    }
}
