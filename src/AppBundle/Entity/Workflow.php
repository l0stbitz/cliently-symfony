<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Workflow
 *
 * @ORM\Table(name="workflow", uniqueConstraints={@ORM\UniqueConstraint(name="_key", columns={"workspace_id", "position"})}, indexes={@ORM\Index(name="workflow_status", columns={"is_enabled"})})
 * @ORM\Entity
 */
class Workflow
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
     * @ORM\Column(name="owner_id", type="integer", nullable=false)
     */
    private $ownerId;

    /**
     * @var integer
     *
     * @ORM\Column(name="workspace_id", type="integer", nullable=false)
     */
    private $workspaceId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="stop_on_respond", type="text", length=65535, nullable=false)
     */
    private $stopOnRespond;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position;

    /**
     * @var integer
     *
     * @ORM\Column(name="origin_id", type="integer", nullable=false)
     */
    private $originId;

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
     * @ORM\OneToMany(targetEntity="Action", mappedBy="workflow")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $actions;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="workflows")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $user;

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
     * Set ownerId
     *
     * @param integer $ownerId
     *
     * @return Workflow
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
     * Set workspaceId
     *
     * @param integer $workspaceId
     *
     * @return Workflow
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
     * Set name
     *
     * @param string $name
     *
     * @return Workflow
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
     * Set stopOnRespond
     *
     * @param string $stopOnRespond
     *
     * @return Workflow
     */
    public function setStopOnRespond($stopOnRespond)
    {
        $this->stopOnRespond = $stopOnRespond;

        return $this;
    }

    /**
     * Get stopOnRespond
     *
     * @return string
     */
    public function getStopOnRespond()
    {
        return $this->stopOnRespond;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Workflow
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
     * Set originId
     *
     * @param integer $originId
     *
     * @return Workflow
     */
    public function setOriginId($originId)
    {
        $this->originId = $originId;

        return $this;
    }

    /**
     * Get originId
     *
     * @return integer
     */
    public function getOriginId()
    {
        return $this->originId;
    }

    /**
     * Set isEnabled
     *
     * @param integer $isEnabled
     *
     * @return Workflow
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
     * @return Workflow
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
     * @return Workflow
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
    public function getActionsArray()
    {
        $actions = [];
        foreach ($this->getActions() as $action) {
            $actions[] = $action->toArray();
        }
        return $actions;
    }

    /**
     *
     * @return type
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     *
     * @param type $actions
     * @return \AppBundle\Entity\Action
     */
    public function setActions($actions)
    {
        $this->actions = $actions;
        return $this;
    }

    /**
     *
     * @return type
     */
    public function getSourcesArray()
    {
        $sources = [];
        foreach ($this->getActions() as $action) {
            $sources[] = $action->toArray();
        }
        return $sources;
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
        $arr['stop_on_respond'] = [];
        $arr['is_enabled'] = $this->getIsEnabled();
        $arr['position'] = $this->getPosition();
        $arr['sources'] = [];
        $arr['actions'] = [];
        //$arr['created_at'] = $this->getCreatedAt();
        //$arr['updated_at'] = $this->getUpdatedAt();
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
}
