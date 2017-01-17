<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pipeline
 *
 * @ORM\Table(name="pipeline")
 * @ORM\Entity
 */
class Pipeline
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
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="workspace_id", type="integer", nullable=false)
     */
    private $workspaceId;

    /**
     * @var integer
     *
     * @ORM\Column(name="owner_id", type="integer", nullable=false)
     */
    private $ownerId;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_enabled", type="integer", nullable=false)
     */
    private $isEnabled = false;

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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="clients")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $user;
    
    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Workspace", inversedBy="pipelines")
     * @ORM\JoinColumn(name="workspace_id", referencedColumnName="id")
     */
    private $workspace;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Stage", mappedBy="pipeline")
     */
    private $stages;  
    
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
     * @return Pipeline
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
     * Set position
     *
     * @param integer $position
     *
     * @return Pipeline
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
     * Set workspaceId
     *
     * @param integer $workspaceId
     *
     * @return Pipeline
     */
    public function setWorkspaceId($workspaceId)
    {
        $this->workspaceId = $workspaceId;

        return $this;
    }

    /**
     * Get workspaceId
     *
     * @return integer
     */
    public function getWorkspaceId()
    {
        return $this->workspaceId;
    }

    /**
     * Set ownerId
     *
     * @param integer $ownerId
     *
     * @return Pipeline
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
     * Set isEnabled
     *
     * @param integer $isEnabled
     *
     * @return Pipeline
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
     * @return Pipeline
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
     * @return Pipeline
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
     *
     * @return type
     */
    public function getStagesArray()
    {
        $stages = [];
        foreach ($this->getStages() as $stage) {
            $stages[] = $stage->toArray();
        }
        return $stages;
    }

    /**
     *
     * @return type
     */
    public function getStages()
    {
        return $this->stages;
    }

    /**
     *
     * @param type $stages
     * @return \AppBundle\Entity\Stage
     */
    public function setStages($stages)
    {
        $this->stages = $stages;
        return $this;
    }
    
    /**
     * 
     * @return array
     */
    public function toArray()
    {
        //'[{"id":1,"name":"Pipeline 1","position":1,"is_enabled":true,"created_at":1483748729,"updated_at":0,
        //"stages":[{"id":1,"name":"New Lead","position":1,"value":0,"pipeline_id":1},
        //{"id":2,"name":"Qualifying","position":2,"value":0,"pipeline_id":1},
        //{"id":3,"name":"Validation","position":3,"value":0,"pipeline_id":1},
        //{"id":4,"name":"Negotiation","position":4,"value":0,"pipeline_id":1},
        //{"id":5,"name":"Closed Won","position":5,"value":0,"pipeline_id":1}]}]'
        $arr = [];
        $arr['id'] = $this->getId();
        $arr['name'] = $this->getName();
        $arr['stop_on_respond'] = [];
        $arr['is_enabled'] = $this->getIsEnabled();
        $arr['position'] = $this->getPosition();
        $arr['owner_id'] = $this->getOwnerId();
        $arr['stages'] = $this->getStagesArray();
        $arr['created_at'] = $this->getCreatedAt();
        $arr['updated_at'] = $this->getUpdatedAt();
        return $arr;
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
    public function getWorkspace()
    {
        return $this->workspace;
    }

    /**
     *
     * @param type $workspace
     * @return \AppBundle\Entity\Workspace
     */
    public function setWorkspace($workspace)
    {
        $this->workspace = $workspace;
        return $this;
    }
}
