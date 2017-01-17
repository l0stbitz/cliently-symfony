<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Deal
 *
 * @ORM\Table(name="deal", 
 *      indexes={@ORM\Index(name="deal_client", columns={"initial_client_id"}),
 *               @ORM\Index(name="deal_owner", columns={"owner_id"})})
 * @ORM\Entity
 */
class Deal
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
    private $name = '';

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=false)
     */
    private $description = '';

    /**
     * @var string
     *
     * @ORM\Column(name="source_description", type="text", length=65535, nullable=false)
     */
    private $sourceDescription = '';

    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float", precision=10, scale=0, nullable=false)
     */
    private $value = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_enabled", type="integer", nullable=false)
     */
    private $isEnabled = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="initial_client_id", type="integer", nullable=false)
     */
    private $initialClientId = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="workflow_id", type="integer", nullable=false)
     */
    private $workflowId = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="stage_id", type="integer", nullable=false)
     */
    private $stageId = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="task_id", type="integer", nullable=false)
     */
    private $taskId = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="action_id", type="integer", nullable=false)
     */
    private $actionId = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="action_values", type="text", length=65535, nullable=false)
     */
    private $actionValues = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="owner_id", type="integer", nullable=false)
     */
    private $ownerId = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="source_id", type="integer", nullable=false)
     */
    private $sourceId = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_at", type="integer", nullable=false)
     */
    private $createdAt = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="updated_at", type="integer", nullable=false)
     */
    private $updatedAt = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="accessed_at", type="integer", nullable=false)
     */
    private $accessedAt = 0;

    /**
     *
     * @ORM\OneToMany(targetEntity="Task", mappedBy="deal")
     */
    private $tasks = [];

    /**
     *
     * @ORM\OneToMany(targetEntity="Note", mappedBy="deal")
     */
    private $notes = [];

    /**
     *
     * @ORM\OneToMany(targetEntity="Msg", mappedBy="deal")
     */
    private $mails = [];

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Stage", inversedBy="deals")
     * @ORM\JoinColumn(name="stage_id", referencedColumnName="id")
     */
    private $stage;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="deals")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Source", inversedBy="deals")
     * @ORM\JoinColumn(name="source_id", referencedColumnName="id")
     */
    private $source;

    /**
     * __construct
     * Insert description here
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function __construct()
    {
        $this->setCreatedAt(time());
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
     * Set name
     *
     * @param string $name
     *
     * @return Deal
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
     * @return Deal
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
     * Set sourceDescription
     *
     * @param string $sourceDescription
     *
     * @return Deal
     */
    public function setSourceDescription($sourceDescription)
    {
        $this->sourceDescription = $sourceDescription;

        return $this;
    }

    /**
     * Get sourceDescription
     *
     * @return string
     */
    public function getSourceDescription()
    {
        return $this->sourceDescription;
    }

    /**
     * Set value
     *
     * @param float $value
     *
     * @return Deal
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set isEnabled
     *
     * @param integer $isEnabled
     *
     * @return Deal
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
     * Set initialClientId
     *
     * @param integer $initialClientId
     *
     * @return Deal
     */
    public function setInitialClientId($initialClientId)
    {
        $this->initialClientId = $initialClientId;

        return $this;
    }

    /**
     * Get initialClientId
     *
     * @return integer
     */
    public function getInitialClientId()
    {
        return $this->initialClientId;
    }

    /**
     * Set workflowId
     *
     * @param integer $workflowId
     *
     * @return Deal
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
     * Set stageId
     *
     * @param integer $stageId
     *
     * @return Deal
     */
    public function setStageId($stageId)
    {
        $this->stageId = $stageId;

        return $this;
    }

    /**
     * Get stageId
     *
     * @return integer
     */
    public function getStageId()
    {
        return $this->stageId;
    }

    /**
     * Set taskId
     *
     * @param integer $taskId
     *
     * @return Deal
     */
    public function setTaskId($taskId)
    {
        $this->taskId = $taskId;

        return $this;
    }

    /**
     * Get taskId
     *
     * @return integer
     */
    public function getTaskId()
    {
        return $this->taskId;
    }

    /**
     * Set actionId
     *
     * @param integer $actionId
     *
     * @return Deal
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
     * Set actionValues
     *
     * @param string $actionValues
     *
     * @return Deal
     */
    public function setActionValues($actionValues)
    {
        $this->actionValues = $actionValues;

        return $this;
    }

    /**
     * Get actionValues
     *
     * @return string
     */
    public function getActionValues()
    {
        return $this->actionValues;
    }

    /**
     * Set ownerId
     *
     * @param integer $ownerId
     *
     * @return Deal
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
     * Set sourceId
     *
     * @param integer $sourceId
     *
     * @return Deal
     */
    public function setSourceId($sourceId)
    {
        $this->sourceId = $sourceId;

        return $this;
    }

    /**
     * Get sourceId
     *
     * @return integer
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }

    /**
     * Set createdAt
     *
     * @param integer $createdAt
     *
     * @return Deal
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
     * @return Deal
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
     * Set accessedAt
     *
     * @param integer $accessedAt
     *
     * @return Deal
     */
    public function setAccessedAt($accessedAt)
    {
        $this->accessedAt = $accessedAt;

        return $this;
    }

    /**
     * Get accessedAt
     *
     * @return integer
     */
    public function getAccessedAt()
    {
        return $this->accessedAt;
    }

    /**
     *
     * @return type
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     *
     * @param type $stage
     * @return \AppBundle\Entity\Stage
     */
    public function setStage($stage)
    {
        $this->stage = $stage;
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
        $arr['value'] = $this->getValue();
        $arr['owner_id'] = $this->getOwnerId();
        $arr['initial_id'] = $this->getInitialClientId();
        $arr['workflow_id'] = 0;
        $arr['stage_id'] = $this->getStageId();
        $arr['stage'] = $this->getStage()->toArray();
        $arr['company'] = null;
        $arr['source'] = null;
        // $arr['client_source_type'] = null;
        $arr['task_due_at'] = 0;
        $arr['new_events_count'] = 0;
        $arr['source_description'] = $this->getSourceDescription();
        $arr['clients'] = [];
        $arr['action_values'] = [];
        $arr['tasks'] = $this->getTasksArray();
        $arr['notes'] = $this->getNotesArray();
        $arr['mails'] = $this->getMailsArray();
        $arr['twitter'] = [];
        $arr['created_at'] = $this->getCreatedAt();
        $arr['updated_at'] = $this->getUpdatedAt();
        $arr['accessed_at'] = $this->getAccessedAt();
        return $arr;
    }

    /**
     *
     * @return type
     */
    public function getTasksArray()
    {
        $tasks = [];
        foreach ($this->getTasks() as $task) {
            $tasks[] = $task->toArray();
        }
        return $tasks;
    }

    /**
     *
     * @return type
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     *
     * @param type $tasks
     * @return \AppBundle\Entity\Task
     */
    public function setTasks($tasks)
    {
        $this->tasks = $tasks;
        return $this;
    }

    /**
     *
     * @return type
     */
    public function getNotesArray()
    {
        $notes = [];
        foreach ($this->getNotes() as $note) {
            $notes[] = $note->toArray();
        }
        return $notes;
    }

    /**
     *
     * @return type
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     *
     * @param type $notes
     * @return \AppBundle\Entity\Note
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     *
     * @return type
     */
    public function getMailsArray()
    {
        $mails = [];
        foreach ($this->getMails() as $mail) {
            $mails[] = $mail->toArray();
        }
        return $mails;
    }

    /**
     *
     * @return type
     */
    public function getMails()
    {
        return $this->mails;
    }

    /**
     *
     * @param type $mails
     * @return \AppBundle\Entity\Msg
     */
    public function setMails($mails)
    {
        $this->mails = $mails;
        return $this;
    }

    /**
     *
     * @return type
     */
    public function getOwner()
    {
        return $this->user;
    }

    /**
     *
     * @param type $user
     * @return \AppBundle\Entity\User
     */
    public function setOwner($user)
    {
        $this->user = $user;
        return $this;
    }
        
    /**
     *
     * @return type
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     *
     * @param type $source
     * @return \AppBundle\Entity\Source
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }
}
