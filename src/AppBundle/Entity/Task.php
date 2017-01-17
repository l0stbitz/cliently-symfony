<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Task
 *
 * @ORM\Table(name="task", indexes={@ORM\Index(name="task_owner", columns={"owner_id"}), @ORM\Index(name="task_lead", columns={"deal_id"}), @ORM\Index(name="task_status", columns={"is_completed"}), @ORM\Index(name="client_id", columns={"client_id"})})
 * @ORM\Entity
 */
class Task
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
     * @var integer
     *
     * @ORM\Column(name="owner_id", type="integer", nullable=false)
     */
    private $ownerId;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    private $description = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="client_id", type="integer", nullable=false)
     */
    private $clientId;

    /**
     * @var integer
     *
     * @ORM\Column(name="deal_id", type="integer", nullable=false)
     */
    private $dealId;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_completed", type="integer", nullable=false)
     */
    private $isCompleted = false;

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
     * @ORM\Column(name="due_at", type="integer", nullable=false)
     */
    private $dueAt = 0;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Deal", inversedBy="tasks")
     * @ORM\JoinColumn(name="deal_id", referencedColumnName="id")
     */
    private $deal;
    
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
     * @return Task
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
     * Set ownerId
     *
     * @param integer $ownerId
     *
     * @return Task
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
     * Set description
     *
     * @param string $description
     *
     * @return Task
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
     * Set clientId
     *
     * @param integer $clientId
     *
     * @return Task
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * Get clientId
     *
     * @return integer
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Set dealId
     *
     * @param integer $dealId
     *
     * @return Task
     */
    public function setDealId($dealId)
    {
        $this->dealId = $dealId;

        return $this;
    }

    /**
     * Get dealId
     *
     * @return integer
     */
    public function getDealId()
    {
        return $this->dealId;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Task
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set isCompleted
     *
     * @param integer $isCompleted
     *
     * @return Task
     */
    public function setIsCompleted($isCompleted)
    {
        $this->isCompleted = $isCompleted;

        return $this;
    }

    /**
     * Get isCompleted
     *
     * @return integer
     */
    public function getIsCompleted()
    {
        return $this->isCompleted;
    }

    /**
     * Set createdAt
     *
     * @param integer $createdAt
     *
     * @return Task
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
     * @return Task
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
     * Set dueAt
     *
     * @param integer $dueAt
     *
     * @return Task
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
     *
     * @return type
     */
    public function getDeal()
    {
        return $this->deal;
    }

    /**
     *
     * @param type $deal
     * @return \AppBundle\Entity\Deal
     */
    public function setDeal($deal)
    {
        $this->deal = $deal;
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
        $arr['client_id'] = $this->getClientId();
        $arr['deal_id'] = $this->getDealId();
        $arr['owner_id'] = $this->getOwnerId();
        $arr['name'] = $this->getName();
        $arr['description'] = $this->getDescription();
        $arr['due_at'] = $this->getDueAt();
        $arr['type'] = $this->getType();
        $arr['is_completed'] = $this->getIsCompleted();   
        $arr['created_at'] = $this->getCreatedAt();
        $arr['updated_at'] = $this->getUpdatedAt();

        return $arr;
    }
}
