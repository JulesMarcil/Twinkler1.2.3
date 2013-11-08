<?php

namespace Smoney\ActionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Smoney\ActionBundle\Entity\PaymentRepository")
 */
class Payment
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
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var boolean
     *
     * @ORM\Column(name="confirmed", type="boolean")
     */
    private $confirmed;

    /**
     * @var boolean
     *
     * @ORM\Column(name="paid", type="boolean")
     */
    private $paid;

    /**
     * @var boolean
     *
     * @ORM\Column(name="received", type="boolean")
     */
    private $received;

    /**
     * @var boolean
     *
     * @ORM\Column(name="registered", type="boolean")
     */
    private $registered;

    /**
     * @ORM\ManyToOne(targetEntity="Tk\UserBundle\Entity\Member", cascade={"persist"})
     */
    protected $payer;

    /**
     * @ORM\OneToMany(targetEntity="Tk\ExpenseBundle\Entity\Expense", mappedBy="payment", cascade={"persist"})
     */
    protected $paybacks;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->confirmed = false;
        $this->paid = false;
        $this->received = false;
        $this->registered = false;
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
     * @return Payment
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
     * Set date
     *
     * @param \DateTime $date
     * @return Payment
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
     * Set confirmed
     *
     * @param boolean $confirmed
     * @return Payment
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    /**
     * Get confirmed
     *
     * @return boolean 
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * Set paid
     *
     * @param boolean $paid
     * @return Payment
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;

        return $this;
    }

    /**
     * Get paid
     *
     * @return boolean 
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * Set received
     *
     * @param boolean $received
     * @return Payment
     */
    public function setReceived($received)
    {
        $this->received = $received;

        return $this;
    }

    /**
     * Get received
     *
     * @return boolean 
     */
    public function getReceived()
    {
        return $this->received;
    }

    /**
     * Set registered
     *
     * @param boolean $registered
     * @return Payment
     */
    public function setRegistered($registered)
    {
        $this->registered = $registered;

        return $this;
    }

    /**
     * Get registered
     *
     * @return boolean 
     */
    public function getRegistered()
    {
        return $this->registered;
    }

    /**
     * Set payer
     *
     * @param \Tk\UserBundle\Entity\Member $payer
     * @return Payment
     */
    public function setPayer(\Tk\UserBundle\Entity\Member $payer = null)
    {
        $this->payer = $payer;

        return $this;
    }

    /**
     * Get payer
     *
     * @return \Tk\UserBundle\Entity\Member 
     */
    public function getPayer()
    {
        return $this->payer;
    }

    /**
     * Set receiver
     *
     * @param \Tk\UserBundle\Entity\Member $receiver
     * @return Payment
     */
    public function setReceiver(\Tk\UserBundle\Entity\Member $receiver = null)
    {
        $this->receiver = $receiver;

        return $this;
    }

    /**
     * Get receiver
     *
     * @return \Tk\UserBundle\Entity\Member 
     */
    public function getReceiver()
    {
        return $this->receiver;
    }
}
