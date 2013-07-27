<?php

namespace Tk\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User
 *
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="Tk\UserBundle\Entity\UserRepository")
 * @UniqueEntity(fields="username", message="This username is already taken")
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="facebookId", type="string", length=255, nullable=true)
     */
    protected $facebookId;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     */
    protected $firstname;
    
    /**
     * @var string
     *
     * @ORM\Column(name="lasttname", type="string", length=255, nullable=true)
     */
    protected $lastname;

    /**
     * @ORM\ManyToOne(targetEntity="Tk\UserBundle\Entity\ProfilePicture", cascade={"all"})
     */
    protected $picture;

    /**
     * @ORM\OneToMany(targetEntity="Tk\UserBundle\Entity\Member", mappedBy="user", cascade={"persist"})
     */
    protected $members;

    /**
     * @ORM\OneToOne(targetEntity="Tk\UserBundle\Entity\Member", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    protected $currentMember;

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
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->myExpenses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ForMeExpenses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->roles = array('ROLE_USER');
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

     /**
     * Get fullname
     *
     * @return string 
     */
    public function getFullname()
    {
        if($this->lastname or $this->lastname){
            return $this->firstname.' '.$this->lastname;
        }elseif($this->email){
            return $this->email;
        }else{
            return 'no information';
        }
    }

    /**
     * Add members
     *
     * @param \Tk\UserBundle\Entity\Member $members
     * @return User
     */
    public function addMember(\Tk\UserBundle\Entity\Member $members)
    {
        $this->members[] = $members;

        return $this;
    }

    /**
     * Remove members
     *
     * @param \Tk\UserBundle\Entity\Member $members
     */
    public function removeMember(\Tk\UserBundle\Entity\Member $members)
    {
        $this->members->removeElement($members);
    }

    /**
     * Get members
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMembers()
    {
        $all_members = $this->members;
        $active_members = new \Doctrine\Common\Collections\ArrayCollection();
        foreach($all_members as $member){
            if($member->getActive() == true){
                $active_members->add($member);
            }
        }
        return $active_members;
    }

    /**
     * Set currentMember
     *
     * @param \Tk\UserBundle\Entity\Member $currentMember
     * @return User
     */
    public function setCurrentMember(\Tk\UserBundle\Entity\Member $currentMember = null)
    {
        $this->currentMember = $currentMember;

        return $this;
    }

    /**
     * Get currentMember
     *
     * @return \Tk\UserBundle\Entity\Member 
     */
    public function getCurrentMember()
    {
        return $this->currentMember;
    }

    /**
     * Get friends
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFriends()
    {
        $my_members = $this->members;
        $groups = new \Doctrine\Common\Collections\ArrayCollection();
        foreach($my_members as $my_member){
            $groups->add($my_member->getTGroup());
        } 
        $friends = array();
        foreach($groups as $group){
            foreach($group->getMembers() as $member){
                $u = $member->getUser();
                if($u and $u != $this and !in_array($u, $friends)){
                    $friends[] = $u;
                }
            }
        }
        return $friends;
    }

    /**
     * @param string $facebookId
     * @return void
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
        $this->setUsername($facebookId);
        $this->salt = '';
    }
 
    /**
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }
 
    /**
     * @param Array
     */
    public function setFBData($fbdata) // C'est dans cette méthode que vous ajouterez vos informations
    {
        if (isset($fbdata['id'])) {
            $this->setFacebookId($fbdata['id']);
            $this->addRole('ROLE_FACEBOOK');
        }
        if (isset($fbdata['first_name']) or isset($fbdata['last_name'])) {
            $this->setUsername($fbdata['first_name'].' '.$fbdata['last_name']);
        }
        if (isset($fbdata['first_name'])) {
            $this->setFirstname($fbdata['first_name']);
        }
        if (isset($fbdata['last_name'])) {
            $this->setLastname($fbdata['last_name']);
        }
        if (isset($fbdata['email'])) {
            $this->setEmail($fbdata['email']);
        }
    }

    /**
     * Set picture
     *
     * @param \Tk\UserBundle\Entity\ProfilePicture $picture
     * @return User
     */
    public function setPicture(\Tk\UserBundle\Entity\ProfilePicture $picture = null)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return \Tk\UserBundle\Entity\ProfilePicture 
     */
    public function getPicture()
    {
        return $this->picture;
    }
}
