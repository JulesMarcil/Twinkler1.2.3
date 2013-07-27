<?php

namespace Tk\ListBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Lists
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Tk\ListBundle\Entity\ListsRepository")
 */
class Lists
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="Tk\GroupBundle\Entity\TGroup", inversedBy="lists", cascade={"persist"})
     */
    protected $group;

    /**
     * @ORM\OneToMany(targetEntity="Tk\ListBundle\Entity\Item", mappedBy="list", cascade={"persist"})
     */
    protected $items;

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
     * @return Lists
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
     * Constructor
     */
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set group
     *
     * @param \Tk\GroupBundle\Entity\TGroup $group
     * @return Lists
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

    /**
     * Add items
     *
     * @param \Tk\ListBundle\Entity\Item $items
     * @return Lists
     */
    public function addItem(\Tk\ListBundle\Entity\Item $items)
    {
        $this->items[] = $items;

        return $this;
    }

    /**
     * Remove items
     *
     * @param \Tk\ListBundle\Entity\Item $items
     */
    public function removeItem(\Tk\ListBundle\Entity\Item $items)
    {
        $this->items->removeElement($items);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getItems()
    {
        $all_items = $this->items;
        $items = new \Doctrine\Common\Collections\ArrayCollection(); 
        foreach($all_items as $item){
            if($item->getStatus() == 'complete' or $item->getStatus() == 'incomplete'){
                $items->add($item);
            }
        }
        return $items;
    }
}
