<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Module
 *
 * @ORM\Table(name="module", indexes={@ORM\Index(name="module_cat", columns={"module_cat_id"}), @ORM\Index(name="module_is_event", columns={"is_event"}), @ORM\Index(name="module_status", columns={"is_enabled"})})
 * @ORM\Entity
 */
class Module
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
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=100, nullable=false)
     */
    private $class;

    /**
     * @var integer
     *
     * @ORM\Column(name="module_cat_id", type="integer", nullable=false)
     */
    private $moduleCatId;

    /**
     * @var integer
     *
     * @ORM\Column(name="module_process_id", type="integer", nullable=false)
     */
    private $moduleProcessId;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_event", type="integer", nullable=false)
     */
    private $isEvent;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_enabled", type="integer", nullable=false)
     */
    private $isEnabled;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_at", type="integer", nullable=false)
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="updated_at", type="integer", nullable=false)
     */
    private $updatedAt;

    /**
     *
     * @ORM\OneToMany(targetEntity="ActionType", mappedBy="module")
     */
    private $actionTypes;

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
     * @return Module
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
     * Set class
     *
     * @param string $class
     *
     * @return Module
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set moduleCatId
     *
     * @param integer $moduleCatId
     *
     * @return Module
     */
    public function setModuleCatId($moduleCatId)
    {
        $this->moduleCatId = $moduleCatId;

        return $this;
    }

    /**
     * Get moduleCatId
     *
     * @return integer
     */
    public function getModuleCatId()
    {
        return $this->moduleCatId;
    }

    /**
     * Set moduleProcessId
     *
     * @param integer $moduleProcessId
     *
     * @return Module
     */
    public function setModuleProcessId($moduleProcessId)
    {
        $this->moduleProcessId = $moduleProcessId;

        return $this;
    }

    /**
     * Get moduleProcessId
     *
     * @return integer
     */
    public function getModuleProcessId()
    {
        return $this->moduleProcessId;
    }

    /**
     * Set isEvent
     *
     * @param integer $isEvent
     *
     * @return Module
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

    /**
     * Set isEnabled
     *
     * @param integer $isEnabled
     *
     * @return Module
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
     * Set createdAt
     *
     * @param integer $createdAt
     *
     * @return Module
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return integer
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param integer $updatedAt
     *
     * @return Module
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return integer
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
