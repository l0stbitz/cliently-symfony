<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stage
 *
 * @ORM\Table(name="stage", uniqueConstraints={@ORM\UniqueConstraint(name="_key", columns={"position", "pipeline_id"})}, 
 *   indexes={@ORM\Index(name="stage_status", columns={"is_enabled"})})
 * @ORM\Entity
 */
class Stage
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
     * @var float
     *
     * @ORM\Column(name="value", type="float", precision=10, scale=0, nullable=false)
     */
    private $value = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="pipeline_id", type="integer", nullable=false)
     */
    private $pipelineId;

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
     * @ORM\ManyToOne(targetEntity="Pipeline", inversedBy="stages")
     * @ORM\JoinColumn(name="pipeline_id", referencedColumnName="id")
     */
    private $pipeline;

    /**
     *
     * @ORM\OneToMany(targetEntity="Deal", mappedBy="stage")
     */
    private $deals;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="stages")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $user;

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
     * @return Stage
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
     * @return Stage
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
     * Set value
     *
     * @param float $value
     *
     * @return Stage
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
     * Set pipelineId
     *
     * @param integer $pipelineId
     *
     * @return Stage
     */
    public function setPipelineId($pipelineId)
    {
        $this->pipelineId = $pipelineId;

        return $this;
    }

    /**
     * Get pipelineId
     *
     * @return integer
     */
    public function getPipelineId()
    {
        return $this->pipelineId;
    }

    /**
     * Set ownerId
     *
     * @param integer $ownerId
     *
     * @return Stage
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
     * @return Stage
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
     * @return Stage
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
     * @return Stage
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
     * @return array
     */
    public function toArray()
    {
        $arr = [];
        $arr['id'] = $this->getId();
        $arr['pipeline_id'] = $this->getPipelineId();
        $arr['name'] = $this->getName();
        $arr['value'] = $this->getValue();
        $arr['position'] = $this->getPosition();
        $arr['is_enabled'] = $this->getIsEnabled();
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
    public function getPipeline()
    {
        return $this->pipeline;
    }

    /**
     *
     * @param type $pipeline
     * @return \AppBundle\Entity\Pipeline
     */
    public function setPipeline($pipeline)
    {
        $this->pipeline = $pipeline;
        return $this;
    }
}
