<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ModuleCat
 *
 * @ORM\Table(name="module_cat", indexes={@ORM\Index(name="module_cat_is_event", columns={"is_event"})})
 * @ORM\Entity
 */
class ModuleCat
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_event", type="integer", nullable=false)
     */
    private $isEvent;



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
     *
     * @return ModuleCat
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
     * Set isEvent
     *
     * @param integer $isEvent
     *
     * @return ModuleCat
     */
    public function setIsEvent($isEvent)
    {
        $this->isEvent = $isEvent;

        return $this;
    }

    /**
     * Get isEvent
     *
     * @return integer
     */
    public function getIsEvent()
    {
        return $this->isEvent;
    }
}
