<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Industry
 *
 * @ORM\Table(name="industry")
 * @ORM\Entity
 */
class Industry
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
     * @ORM\Column(name="parent_id", type="integer", nullable=false)
     */
    private $parentId;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_last", type="integer", nullable=false)
     */
    private $isLast;

    /**
     * @var string
     *
     * @ORM\Column(name="workflows", type="text", length=65535, nullable=false)
     */
    private $workflows;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_enabled", type="integer", nullable=false)
     */
    private $isEnabled;

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
     * @return Industry
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
     * Set parentId
     *
     * @param integer $parentId
     *
     * @return Industry
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId
     *
     * @return integer
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set isLast
     *
     * @param integer $isLast
     *
     * @return Industry
     */
    public function setIsLast($isLast)
    {
        $this->isLast = $isLast;

        return $this;
    }

    /**
     * Get isLast
     *
     * @return integer
     */
    public function getIsLast()
    {
        return $this->isLast;
    }

    /**
     * Set workflows
     *
     * @param string $workflows
     *
     * @return Industry
     */
    public function setWorkflows($workflows)
    {
        $this->workflows = $workflows;

        return $this;
    }

    /**
     * Get workflows
     *
     * @return string
     */
    public function getWorkflows()
    {
        return $this->workflows;
    }

    /**
     * Set isEnabled
     *
     * @param integer $isEnabled
     *
     * @return Industry
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * Get isEnabled
     *
     * @return integer
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * 
     * @return array
     */
    public function toArray()
    {
        $arr = [];
        $arr['id'] = $this->getId();
        $arr['name'] = $this->getName();
        $arr['parent_id'] = $this->getParentId();
        return $arr;
    }
}
