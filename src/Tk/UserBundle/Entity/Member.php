<?php

namespace Tk\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Member
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Tk\UserBundle\Entity\MemberRepository")
 */
class Member
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="invitationToken", type="string", length=255, nullable=true)
     */
    private $invitationToken;

    /**
     * @var string
     *
     * @ORM\Column(name="facebookId", type="string", length=255, nullable=true)
     */
    protected $facebookId;

    /**
     * @ORM\ManyToOne(targetEntity="Tk\GroupBundle\Entity\TGroup", inversedBy="members", cascade={"persist"})
     */
    protected $tgroup;

    /**
     * @ORM\ManyToOne(targetEntity="Tk\UserBundle\Entity\User", inversedBy="members", cascade={"persist"})
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="Tk\ExpenseBundle\Entity\Expense", mappedBy="owner", cascade={"persist"})
     */
    protected $myExpenses;
    
    /**
     * @ORM\ManyToMany(targetEntity="Tk\ExpenseBundle\Entity\Expense", mappedBy="users", cascade={"persist"})
     */
    protected $forMeExpenses;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active = true;

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
     * Set name
     *
     * @param string $name
     * @return Member
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
     * Set invitationToken
     *
     * @param string $invitationToken
     * @return Member
     */
    public function setInvitationToken($invitationToken)
    {
        $this->invitationToken = $invitationToken;

        return $this;
    }

    /**
     * Get invitationToken
     *
     * @return string 
     */
    public function getInvitationToken()
    {
        return $this->invitationToken;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->myExpenses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->forMeExpenses = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Generate invitationToken
     *
     * @return string 
     */
    public function generateInvitationToken()
    {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < 30; $i++) {
            $key .= $keys[array_rand($keys)];
        }

    return $key;
    }

    /**
     * Set tgroup
     *
     * @param \Tk\GroupBundle\Entity\TGroup $tgroup
     * @return Member
     */
    public function setTgroup(\Tk\GroupBundle\Entity\TGroup $tgroup = null)
    {
        $this->tgroup = $tgroup;

        return $this;
    }

    /**
     * Get tgroup
     *
     * @return \Tk\GroupBundle\Entity\TGroup 
     */
    public function getTgroup()
    {
        return $this->tgroup;
    }

    /**
     * Set user
     *
     * @param \Tk\UserBundle\Entity\User $user
     * @return Member
     */
    public function setUser(\Tk\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Tk\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add myExpenses
     *
     * @param \Tk\ExpenseBundle\Entity\Expense $myExpenses
     * @return Member
     */
    public function addMyExpense(\Tk\ExpenseBundle\Entity\Expense $myExpenses)
    {
        $this->myExpenses[] = $myExpenses;

        return $this;
    }

    /**
     * Remove myExpenses
     *
     * @param \Tk\ExpenseBundle\Entity\Expense $myExpenses
     */
    public function removeMyExpense(\Tk\ExpenseBundle\Entity\Expense $myExpenses)
    {
        $this->myExpenses->removeElement($myExpenses);
    }

    /**
     * Get myExpenses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMyExpenses()
    {
        $all_expenses = $this->myExpenses;
        $active_expenses = new \Doctrine\Common\Collections\ArrayCollection();
        if ($all_expenses) {
            foreach($all_expenses as $expense){
                if($expense->getActive() == 1){
                    $active_expenses->add($expense);
                }
            }
            return $active_expenses;
        } else {
            return null;
        }
    }

    /**
     * Get all myExpenses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAllMyExpenses()
    {
        return $this->myExpenses;
    }

    /**
     * Add forMeExpenses
     *
     * @param \Tk\ExpenseBundle\Entity\Expense $forMeExpenses
     * @return Member
     */
    public function addForMeExpense(\Tk\ExpenseBundle\Entity\Expense $forMeExpenses)
    {
        $this->forMeExpenses[] = $forMeExpenses;

        return $this;
    }

    /**
     * Remove forMeExpenses
     *
     * @param \Tk\ExpenseBundle\Entity\Expense $forMeExpenses
     */
    public function removeForMeExpense(\Tk\ExpenseBundle\Entity\Expense $forMeExpenses)
    {
        $this->forMeExpenses->removeElement($forMeExpenses);
    }

    /**
     * Get forMeExpenses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getForMeExpenses()
    {
        $all_expenses = $this->forMeExpenses;
        $active_expenses = new \Doctrine\Common\Collections\ArrayCollection();
        if ($all_expenses) {
            foreach($all_expenses as $expense){
                if($expense->getActive() == 1){
                    $active_expenses->add($expense);
                }
            }
            return $active_expenses;
        } else {
            return null;
        }

    }

    /**
     * Get all forMeExpenses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAllForMeExpenses()
    {
        return $this->forMeExpenses;
    }

    /**
     * Get total paid
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTotalPaid()
    {
        $all_expenses = $this->getMyExpenses();

        $sum = 0;
        foreach($all_expenses as $expense){
            $sum += $expense->getAmount();
        }
        return $sum;
    }    

    /**
     * Get Balance
     *
     * @return integer
     */
    public function getBalance()
    {   
        $supposed_paid = 0;
        foreach($this->getForMeExpenses() as $expense){
            $supposed_paid += $this->forMe($expense);
        }

        $paid = 0;
        foreach($this->getMyExpenses() as $expense){
            $paid += $expense->getAmount();
        }

        $balance = $paid - $supposed_paid;
        return round($balance, 2);
    }

    private function forMe($expense)
    {
        $members = $expense->getUsers()->toArray();
        if(in_array($this, $members)){
            return round(($expense->getAmount())/(sizeof($members)),2);
        }else{
            return 0;
        }
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Member
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
     * Set active
     *
     * @param boolean $active
     * @return Member
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
    *Get Picture Path
    *
    *@return string
    */
    public function getPicturePath()
    {
        $picture_path = 'local';
        $fbid = $this->getFacebookId();
        if($fbid){
            $picture_path = $fbid;
        } else {
            $member_user = $this->getUser();
            if($member_user){
                $picture_path = $member_user->getPicturePath();
            }
        }
        return $picture_path;
    }

    /**
     * Set facebookId
     *
     * @param string $facebookId
     * @return Member
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * Get facebookId
     *
     * @return string 
     */
    public function getFacebookId()
    {
        $fbid = $this->facebookId; 
        if (!$fbid) {
            $user = $this->getUser();
            if($user){
                $fbid = $user->getFacebookId();
            }
        }
        return $fbid;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Member
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        if ($this->getUser()){
            $this->getUser()->setPhone($phone);
        }
        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        if ($this->getUser()){
            if($this->getUser()->getPhone()){
                return $this->getUser()->getPhone();
            }
        }
        return $this->phone;
    }
}
