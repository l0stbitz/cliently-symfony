<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WorkspaceMember
 *
 * @ORM\Table(name="workspace_member",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="_key", columns={"user_id", "workspace_id"})})
 * @ORM\Entity
 */
class WorkspaceMember
{

    const ROLE_BY_ID = [
        1 => ['id' => 1, 'class' => 'owner'],
        2 => ['id' => 2, 'class' => 'admin'],
        3 => ['id' => 3, 'class' => 'user'],
    ];
    const ROLE_BY_CLASS = [
        'owner' => ['id' => 1, 'class' => 'owner'],
        'admin' => ['id' => 2, 'class' => 'admin'],
        'user' => ['id' => 3, 'class' => 'user'],
    ];
    const ROLE = [
        1 => 'owner',
        2 => 'admin',
        3 => 'user',
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
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="workspace_id", type="integer", nullable=false)
     */
    private $workspaceId;

    /**
     * @var integer
     *
     * @ORM\Column(name="role", type="integer", nullable=false)
     */
    private $role = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="credit_balance", type="bigint", nullable=false)
     */
    private $creditBalance = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="accepted_deal_count", type="integer", nullable=false)
     */
    private $acceptedDealCount = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="extra", type="text", length=65535, nullable=true)
     */
    private $extra;

    /**
     * @var integer
     *
     * @ORM\Column(name="owner_id", type="integer", nullable=true)
     */
    private $ownerId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_confirmed", type="boolean", nullable=false)
     */
    private $isConfirmed = false;

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
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position = 0;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Workspace", inversedBy="workspaceMembers")
     * @ORM\JoinColumn(name="workspace_id", referencedColumnName="id")
     */
    private $workspace;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="workspaceMembers")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
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
     * Set userId
     *
     * @param integer $userId
     *
     * @return WorkspaceMember
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
     * Set workspaceId
     *
     * @param integer $workspaceId
     *
     * @return WorkspaceMember
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
     * Set role
     *
     * @param integer $role
     *
     * @return WorkspaceMember
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return integer
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Get role
     *
     * @return integer
     */
    public function getRoleString()
    {
        if ($this->role > 0) {
            return self::ROLE_BY_ID[$this->role]['class'];
        }
        return 'user';
    }

    /**
     * Set creditBalance
     *
     * @param integer $creditBalance
     *
     * @return WorkspaceMember
     */
    public function setCreditBalance($creditBalance)
    {
        $this->creditBalance = $creditBalance;

        return $this;
    }

    /**
     * Get creditBalance
     *
     * @return integer
     */
    public function getCreditBalance()
    {
        return $this->creditBalance;
    }

    /**
     * Set acceptedDealCount
     *
     * @param integer $acceptedDealCount
     *
     * @return WorkspaceMember
     */
    public function setAcceptedDealCount($acceptedDealCount)
    {
        $this->acceptedDealCount = $acceptedDealCount;

        return $this;
    }

    /**
     * Get acceptedDealCount
     *
     * @return integer
     */
    public function getAcceptedDealCount()
    {
        return $this->acceptedDealCount;
    }

    /**
     * Set extra
     *
     * @param string $extra
     *
     * @return WorkspaceMember
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
     * Set ownerId
     *
     * @param integer $ownerId
     *
     * @return WorkspaceMember
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
     * Set isConfirmed
     *
     * @param boolean $isConfirmed
     *
     * @return WorkspaceMember
     */
    public function setIsConfirmed($isConfirmed)
    {
        $this->isConfirmed = $isConfirmed;

        return $this;
    }

    /**
     * Get isConfirmed
     *
     * @return boolean
     */
    public function getIsConfirmed()
    {
        return $this->isConfirmed;
    }

    /**
     * Set isEnabled
     *
     * @param integer $isEnabled
     *
     * @return WorkspaceMember
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
     * @return WorkspaceMember
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
     * @return WorkspaceMember
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
     * Set workspace
     *
     * @param Workspace $workspace
     *
     * @return Workspace
     */
    public function setWorkspace($workspace = null)
    {
        $this->workspace = $workspace;

        return $this;
    }

    /**
     * Get workspace
     *
     * @return workspace
     */
    public function getWorkspace()
    {
        return $this->workspace;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return User
     */
    public function setUser($user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * 
     * @return array
     */
    public function toArray()
    {
        //json_decode('[{"id":1,"user_id":1,"workspace_id":1,"role":"owner","credit_balance":6,"accepted_deal_count":0,"extra":{},
        //"owner_id":1,"is_confirmed":true,"is_enabled":true,"created_at":1483748729,"updated_at":0,
        //"user":{"id":1,"first_name":"sdfgsdfgsdfg","last_name":"sdfgsdfgsdfg","avatar":null,"email":"bob@test.com","integrations":[]}},
        //{"id":2,"user_id":0,"workspace_id":1,"role":"admin","credit_balance":0,"accepted_deal_count":0,"extra":{"name":"asdfasdfa","email":"asdfasdf@asdfasdf.com"},"owner_id":1,"is_confirmed":false,"is_enabled":true,"created_at":1483756100,"updated_at":0,"user":null}]');
        $arr = [];
        $arr['id'] = $this->getId();
        $arr['role'] = $this->getRoleString();
        $arr['user'] = $this->getUser() ? $this->getUser()->toArray() : null;
        $arr['user_id'] = $this->getUserId() ? $this->getUserId() : 0;
        $arr['owner_id'] = $this->getOwnerId();
        $arr['credit_balance'] = $this->getCreditBalance();
        $arr['accepted_deal_count'] = $this->getAcceptedDealCount();
        $arr['extra'] = json_decode($this->getExtra());
        $arr['is_enabled'] = $this->getIsEnabled();
        $arr['is_confirmed'] = $this->getIsConfirmed();
        $arr['created_at'] = $this->getCreatedAt();
        $arr['updated_at'] = $this->getUpdatedAt();
        return $arr;
    }
}
