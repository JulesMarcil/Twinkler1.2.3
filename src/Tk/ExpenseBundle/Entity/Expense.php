<?php

namespace Tk\ExpenseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Expense
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Tk\ExpenseBundle\Entity\ExpenseRepository")
 */
class Expense
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
     * @var decimal
     *
     * @ORM\Column(name="amount", type="decimal", scale=2, precision=7)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="addedDate", type="datetime")
     */
    private $addedDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active = true;

    /**
     * @ORM\ManyToOne(targetEntity="Tk\UserBundle\Entity\Member", cascade={"persist"})
     */
    protected $author;

    /**
     * @ORM\ManyToOne(targetEntity="Tk\UserBundle\Entity\Member", inversedBy="myExpenses", cascade={"persist"})
     */
    protected $owner;

    /**
     * @ORM\ManyToMany(targetEntity="Tk\UserBundle\Entity\Member", inversedBy="ForMeExpenses", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $users;

    /**
     * @ORM\ManyToOne(targetEntity="Tk\GroupBundle\Entity\TGroup", inversedBy="expenses", cascade={"persist"})
     */
    protected $group;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set amount
     *
     * @param float $amount
     * @return Expense
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Expense
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set addedDate
     *
     * @param \DateTime $addedDate
     * @return Expense
     */
    public function setAddedDate($addedDate)
    {
        $this->addedDate = $addedDate;

        return $this;
    }

    /**
     * Get addedDate
     *
     * @return \DateTime 
     */
    public function getAddedDate()
    {
        return $this->addedDate;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Expense
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Expense
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set author
     *
     * @param \Tk\UserBundle\Entity\Member $author
     * @return Expense
     */
    public function setAuthor(\Tk\UserBundle\Entity\Member $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \Tk\UserBundle\Entity\Member 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set owner
     *
     * @param \Tk\UserBundle\Entity\Member $owner
     * @return Expense
     */
    public function setOwner(\Tk\UserBundle\Entity\Member $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Tk\UserBundle\Entity\Member 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Add users
     *
     * @param \Tk\UserBundle\Entity\Member $users
     * @return Expense
     */
    public function addUser(\Tk\UserBundle\Entity\Member $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Tk\UserBundle\Entity\Member $users
     */
    public function removeUser(\Tk\UserBundle\Entity\Member $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set group
     *
     * @param \Tk\GroupBundle\Entity\TGroup $group
     * @return Expense
     */
    public function setGroup(\Tk\GroupBundle\Entity\TGroup $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \Tk\GroupBundle\Entity\TGroup 
     */
    public function getGroup()
    {
        return $this->group;
    }
}
