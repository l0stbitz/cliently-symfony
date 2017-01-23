<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Integration
 *
 * @ORM\Table(name="integration", uniqueConstraints={@ORM\UniqueConstraint(name="_key", columns={"code", "type"})}, indexes={@ORM\Index(name="integration_account", columns={"user_id"}), @ORM\Index(name="integration_type", columns={"type"}), @ORM\Index(name="integration_status", columns={"status"}), @ORM\Index(name="integration_handle", columns={"handle"})})
 * @ORM\Entity
 */
class Integration
{

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DEFAULT = 2;
    const TYPE_BY_ID = [
        1 => ['id' => 1, 'class' => 'mail'],
        2 => ['id' => 2, 'class' => 'twitter'],
        3 => ['id' => 3, 'class' => 'google'],
        4 => ['id' => 4, 'class' => 'slack'],
    ];
    const TYPE_BY_CLASS = [
        'mail' => ['id' => 1, 'class' => 'mail'],
        'twitter' => ['id' => 2, 'class' => 'twitter'],
        'google' => ['id' => 3, 'class' => 'google'],
        'slack' => ['id' => 4, 'class' => 'slack'],
    ];
    const VALUES_MAIL = [
        'fullname' => '',
        'email' => '',
        'password' => '',
        'imap_server' => '',
        'imap_port' => 0,
        'smtp_server' => '',
        'smtp_port' => 0,
    ];

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
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="source_id", type="integer", nullable=false)
     */
    private $sourceId;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=50, nullable=false)
     */
    private $code = '';
    //Renamed field
    /**
     * @var string
     *
     * @ORM\Column(name="vals", type="text", length=65535, nullable=false)
     */
    private $values = '';

    /**
     * @var string
     *
     * @ORM\Column(name="avatar", type="string", length=100, nullable=false)
     */
    private $avatar = '';

    /**
     * @var string
     *
     * @ORM\Column(name="handle", type="string", length=100, nullable=false)
     */
    private $handle = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="is_primary", type="integer", nullable=false)
     */
    private $isPrimary = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_suspended", type="boolean", nullable=false)
     */
    private $isSuspended = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = 0;

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
    private $updatedAt = 0;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="integrations")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Source", inversedBy="integrations")
     * @ORM\JoinColumn(name="source_id", referencedColumnName="id")
     */
    private $source;

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
     * @return Integration
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
     * Set userId
     *
     * @param integer $userId
     *
     * @return Integration
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Integration
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
     * Get type
     *
     * @return integer
     */
    public function getTypeString()
    {
        return self::TYPE_BY_ID[$this->getType()]['class'];
    }

    /**
     * Set sourceId
     *
     * @param integer $sourceId
     *
     * @return Integration
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
     * Set code
     *
     * @param string $code
     *
     * @return Integration
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set values
     *
     * @param string $values
     *
     * @return Integration
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
     * Set avatar
     *
     * @param string $avatar
     *
     * @return Integration
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set handle
     *
     * @param string $handle
     *
     * @return Integration
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;

        return $this;
    }

    /**
     * Get handle
     *
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * Set isPrimary
     *
     * @param integer $isPrimary
     *
     * @return Integration
     */
    public function setIsPrimary($isPrimary)
    {
        $this->isPrimary = $isPrimary;

        return $this;
    }

    /**
     * Get isPrimary
     *
     * @return integer
     */
    public function getIsPrimary()
    {
        return $this->isPrimary;
    }

    /**
     * Set isSuspended
     *
     * @param boolean $isSuspended
     *
     * @return Integration
     */
    public function setIsSuspended($isSuspended)
    {
        $this->isSuspended = $isSuspended;

        return $this;
    }

    /**
     * Get isSuspended
     *
     * @return boolean
     */
    public function getIsSuspended()
    {
        return $this->isSuspended;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Integration
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdAt
     *
     * @param integer $createdAt
     *
     * @return Integration
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
     * @return Integration
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

    /**
     *
     * @return type
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     *
     * @param type $user
     * @return \AppBundle\Entity\User
     */
    public function setUser($user)
    {
        $this->user = $user;
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
        $arr['name'] = $this->getName();
        $arr['avatar'] = $this->getAvatar();
        $arr['type'] = $this->getTypeString();
        $arr['status'] = $this->getStatus();
        $arr['handle'] = $this->getHandle();
        $arr['code'] = $this->getCode();
        $arr['user_id'] = $this->getUser()->getId();
        $arr['source'] = $this->getSource()->toArray();
        $arr['values'] = json_decode($this->getValues());
        $arr['is_primary'] = $this->getIsPrimary();
        $arr['is_suspended'] = $this->getIsSuspended();
        $arr['created_at'] = $this->getCreatedAt();
        $arr['updated_at'] = $this->getUpdatedAt();
        return $arr;
    }
}
