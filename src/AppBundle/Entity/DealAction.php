<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DealAction
 *
 * @ORM\Table(name="deal_action", indexes={@ORM\Index(name="action_owner", columns={"owner_id"}), @ORM\Index(name="action_pos", columns={"position"}), @ORM\Index(name="action_status", columns={"is_enabled"})})
 * @ORM\Entity
 */
class DealAction
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
     * @ORM\Column(name="action_id", type="integer", nullable=false)
     */
    private $actionId;

    /**
     * @var integer
     *
     * @ORM\Column(name="deal_workflow_id", type="integer", nullable=false)
     */
    private $dealWorkflowId;

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
     * @var boolean
     *
     * @ORM\Column(name="is_enabled", type="boolean", nullable=false)
     */
    private $isEnabled;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_failed", type="integer", nullable=false)
     */
    private $isFailed;

    /**
     * @var integer
     *
     * @ORM\Column(name="run_time", type="integer", nullable=false)
     */
    private $runTime;

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
     * @ORM\Column(name="due_at", type="integer", nullable=false)
     */
    private $dueAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="started_at", type="integer", nullable=false)
     */
    private $startedAt;



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
     * @return DealAction
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
     * @return DealAction
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
     * @return DealAction
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
     * Set actionId
     *
     * @param integer $actionId
     *
     * @return DealAction
     */
    public function setActionId($actionId)
    {
        $this->actionId = $actionId;

        return $this;
    }

    /**
     * Get actionId
     *
     * @return integer
     */
    public function getActionId()
    {
        return $this->actionId;
    }

    /**
     * Set dealWorkflowId
     *
     * @param integer $dealWorkflowId
     *
     * @return DealAction
     */
    public function setDealWorkflowId($dealWorkflowId)
    {
        $this->dealWorkflowId = $dealWorkflowId;

        return $this;
    }

    /**
     * Get dealWorkflowId
     *
     * @return integer
     */
    public function getDealWorkflowId()
    {
        return $this->dealWorkflowId;
    }

    /**
     * Set ownerId
     *
     * @param integer $ownerId
     *
     * @return DealAction
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
     * @return DealAction
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
     * @return DealAction
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
     * @return DealAction
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
     * Set isEnabled
     *
     * @param boolean $isEnabled
     *
     * @return DealAction
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * Get isEnabled
     *
     * @return boolean
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Set isFailed
     *
     * @param integer $isFailed
     *
     * @return DealAction
     */
    public function setIsFailed($isFailed)
    {
        $this->isFailed = $isFailed;

        return $this;
    }

    /**
     * Get isFailed
     *
     * @return integer
     */
    public function getIsFailed()
    {
        return $this->isFailed;
    }

    /**
     * Set runTime
     *
     * @param integer $runTime
     *
     * @return DealAction
     */
    public function setRunTime($runTime)
    {
        $this->runTime = $runTime;

        return $this;
    }

    /**
     * Get runTime
     *
     * @return integer
     */
    public function getRunTime()
    {
        return $this->runTime;
    }

    /**
     * Set createdAt
     *
     * @param integer $createdAt
     *
     * @return DealAction
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
     * @return DealAction
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
     * @return DealAction
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
     * Set dueAt
     *
     * @param integer $dueAt
     *
     * @return DealAction
     */
    public function setDueAt($dueAt)
    {
        $this->dueAt = $dueAt;

        return $this;
    }

    /**
     * Get dueAt
     *
     * @return integer
     */
    public function getDueAt()
    {
        return $this->dueAt;
    }

    /**
     * Set startedAt
     *
     * @param integer $startedAt
     *
     * @return DealAction
     */
    public function setStartedAt($startedAt)
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * Get startedAt
     *
     * @return integer
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }
}
