<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Action
 *
 * @ORM\Table(name="action", 
 *  uniqueConstraints={@ORM\UniqueConstraint(name="_key", columns={"workflow_id", "position", "sort"})})
 * @ORM\Entity
 */
class Action
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
     * @var integer
     *
     * @ORM\Column(name="action_type_id", type="integer", nullable=false)
     */
    private $actionTypeId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=false)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="workflow_id", type="integer", nullable=false)
     */
    private $workflowId;

    /**
     * @var integer
     *
     * @ORM\Column(name="owner_id", type="integer", nullable=false)
     */
    private $ownerId;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position;

    /**
     * @var string
     *
     * @ORM\Column(name="values", type="text", length=65535, nullable=false)
     */
    private $values;

    /**
     * @var string
     *
     * @ORM\Column(name="extra", type="text", length=65535, nullable=false)
     */
    private $extra;

    /**
     * @var integer
     *
     * @ORM\Column(name="sort", type="integer", nullable=false)
     */
    private $sort;

    /**
     * @var integer
     *
     * @ORM\Column(name="default_id", type="integer", nullable=false)
     */
    private $defaultId;

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
     * @var integer
     *
     * @ORM\Column(name="executed_at", type="integer", nullable=false)
     */
    private $executedAt;
    
    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Workflow", inversedBy="actions")
     * @ORM\JoinColumn(name="workflow_id", referencedColumnName="id")
     */
    private $workflow;
    
    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ActionType", inversedBy="actions")
     * @ORM\JoinColumn(name="action_type_id", referencedColumnName="id")
     */
    private $actionType;

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
     * Set actionTypeId
     *
     * @param integer $actionTypeId
     *
     * @return Action
     */
    public function setActionTypeId($actionTypeId)
    {
        $this->actionTypeId = $actionTypeId;

        return $this;
    }

    /**
     * Get actionTypeId
     *
     * @return integer
     */
    public function getActionTypeId()
    {
        return $this->actionTypeId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Action
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
     * Set description
     *
     * @param string $description
     *
     * @return Action
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set workflowId
     *
     * @param integer $workflowId
     *
     * @return Action
     */
    public function setWorkflowId($workflowId)
    {
        $this->workflowId = $workflowId;

        return $this;
    }

    /**
     * Get workflowId
     *
     * @return integer
     */
    public function getWorkflowId()
    {
        return $this->workflowId;
    }

    /**
     * Set ownerId
     *
     * @param integer $ownerId
     *
     * @return Action
     */
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;

        return $this;
    }

    /**
     * Get ownerId
     *
     * @return integer
     */
    public function getOwnerId()
    {
        return $this->ownerId;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Action
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set values
     *
     * @param string $values
     *
     * @return Action
     */
    public function setValues($values)
    {
        $this->values = $values;

        return $this;
    }

    /**
     * Get values
     *
     * @return string
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Set extra
     *
     * @param string $extra
     *
     * @return Action
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * Get extra
     *
     * @return string
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * Set sort
     *
     * @param integer $sort
     *
     * @return Action
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort
     *
     * @return integer
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set defaultId
     *
     * @param integer $defaultId
     *
     * @return Action
     */
    public function setDefaultId($defaultId)
    {
        $this->defaultId = $defaultId;

        return $this;
    }

    /**
     * Get defaultId
     *
     * @return integer
     */
    public function getDefaultId()
    {
        return $this->defaultId;
    }

    /**
     * Set isEnabled
     *
     * @param integer $isEnabled
     *
     * @return Action
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
     * @return Action
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
     * @return Action
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

    /**
     * Set executedAt
     *
     * @param integer $executedAt
     *
     * @return Action
     */
    public function setExecutedAt($executedAt)
    {
        $this->executedAt = $executedAt;

        return $this;
    }

    /**
     * Get executedAt
     *
     * @return integer
     */
    public function getExecutedAt()
    {
        return $this->executedAt;
    }
    
    /**
     *
     * @return type
     */
    public function getActionType()
    {
        return $this->actionType;
    }

    /**
     *
     * @param type $actionType
     * @return \AppBundle\Entity\ActionType
     */
    public function setActionType($actionType)
    {
        $this->actionType = $actionType;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function toArray()
    {
        $arr = [];
        $arr['id'] = $this->getId();
        $arr['class'] = $this->getActionType()->getClass();
        $arr['module_class'] = $this->getActionType()->getModule()->getClass();
        $arr['name'] = $this->getName();
        $arr['description'] = $this->getDescription();
        $arr['extra'] = json_decode($this->getExtra());
        $arr['values'] = json_decode($this->getValues());
        $arr['sort'] = $this->getSort();
        $arr['position'] = $this->getPosition();
        $arr['action_type_id'] = $this->getActionTypeId();
        $arr['default_id'] = $this->getDefaultId();
        $arr['is_enabled'] = $this->getIsEnabled();
        $arr['created_at'] = $this->getCreatedAt();
        $arr['updated_at'] = $this->getUpdatedAt();
        $arr['executed_at'] = $this->getExecutedAt();
        return $arr;
    }
}
