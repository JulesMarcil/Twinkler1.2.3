<?php

namespace Tk\ListBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Item
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Tk\ListBundle\Entity\ItemRepository")
 * @ExclusionPolicy("all")
 */
class Item
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Expose
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     * @Expose
     */
    protected $status;

    /**
     * @ORM\ManyToOne(targetEntity="Tk\ListBundle\Entity\Lists", inversedBy="items", cascade={"persist"})
     */
    protected $list;

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
     * @return Item
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
     * Set status
     *
     * @param string $status
     * @return Item
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set list
     *
     * @param \Tk\ListBundle\Entity\Lists $list
     * @return Item
     */
    public function setList(\Tk\ListBundle\Entity\Lists $list = null)
    {
        $this->list = $list;

        return $this;
    }

    /**
     * Get list
     *
     * @return \Tk\ListBundle\Entity\Lists 
     */
    public function getList()
    {
        return $this->list;
    }
}
