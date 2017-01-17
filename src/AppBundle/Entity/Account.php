<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Account
 *
 * @ORM\Table(name="account", uniqueConstraints={@ORM\UniqueConstraint(name="_key", columns={"owner_id"})})
 * @ORM\Entity
 */
class Account
{

    const TYPE_BY_ID = [
        1 => ['id' => 1, 'class' => 'main'],
    ];
    const TYPE_BY_CLASS = [
        'main' => ['id' => 1, 'class' => 'main'],
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
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="plan_id", type="integer", nullable=false)
     */
    private $planId = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="next_plan_id", type="integer", nullable=false)
     */
    private $nextPlanId = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="member_count", type="integer", nullable=false)
     */
    private $memberCount = 0;

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
     * @var integer
     *
     * @ORM\Column(name="workspace_count", type="integer", nullable=false)
     */
    private $workspaceCount = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="pipeline_count", type="integer", nullable=false)
     */
    private $pipelineCount = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="workflow_count", type="integer", nullable=false)
     */
    private $workflowCount = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="source_count", type="integer", nullable=false)
     */
    private $sourceCount = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="enabled_member_count", type="integer", nullable=false)
     */
    private $enabledMemberCount = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="enabled_workspace_count", type="integer", nullable=false)
     */
    private $enabledWorkspaceCount = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="enabled_pipeline_count", type="integer", nullable=false)
     */
    private $enabledPipelineCount = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="enabled_workflow_count", type="integer", nullable=false)
     */
    private $enabledWorkflowCount = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="enabled_source_count", type="integer", nullable=false)
     */
    private $enabledSourceCount = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="daily_leads_scanned", type="integer", nullable=false)
     */
    private $dailyLeadsScanned = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="plan_started_at", type="integer", nullable=false)
     */
    private $planStartedAt = 0;

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
    private $isEnabled = 0;

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
     *
     * @ORM\OneToMany(targetEntity="Workspace", mappedBy="account")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $workspaces;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="accounts")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $user;

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
     * @return Account
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
     * Set type
     *
     * @param integer $type
     *
     * @return Account
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
     * Set planId
     *
     * @param integer $planId
     *
     * @return Account
     */
    public function setPlanId($planId)
    {
        $this->planId = $planId;

        return $this;
    }

    /**
     * Get planId
     *
     * @return integer
     */
    public function getPlanId()
    {
        return $this->planId;
    }

    /**
     * Get planClass
     *
     * @return string
     */
    public function getPlanClass()
    {
        switch ($this->getPlanId()) {
        case 0:
            return 'free';
        default:
            return 'free';
        }
    }

    /**
     * Set nextPlanId
     *
     * @param integer $nextPlanId
     *
     * @return Account
     */
    public function setNextPlanId($nextPlanId)
    {
        $this->nextPlanId = $nextPlanId;

        return $this;
    }

    /**
     * Get nextPlanId
     *
     * @return integer
     */
    public function getNextPlanId()
    {
        return $this->nextPlanId;
    }

    /**
     * Get nextPlanClass
     *
     * @return string
     */
    public function getNextPlanClass()
    {
        switch ($this->getNextPlanId()) {
        case 0:
            return '';
        default:
            return '';
        }
    }

    /**
     * Set memberCount
     *
     * @param integer $memberCount
     *
     * @return Account
     */
    public function setMemberCount($memberCount)
    {
        $this->memberCount = $memberCount;

        return $this;
    }

    /**
     * Get memberCount
     *
     * @return integer
     */
    public function getMemberCount()
    {
        return $this->memberCount;
    }

    /**
     * Set creditBalance
     *
     * @param integer $creditBalance
     *
     * @return Account
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
     * @return Account
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
     * Set workspaceCount
     *
     * @param integer $workspaceCount
     *
     * @return Account
     */
    public function setWorkspaceCount($workspaceCount)
    {
        $this->workspaceCount = $workspaceCount;

        return $this;
    }

    /**
     * Get workspaceCount
     *
     * @return integer
     */
    public function getWorkspaceCount()
    {
        return $this->workspaceCount;
    }

    /**
     * Set pipelineCount
     *
     * @param integer $pipelineCount
     *
     * @return Account
     */
    public function setPipelineCount($pipelineCount)
    {
        $this->pipelineCount = $pipelineCount;

        return $this;
    }

    /**
     * Get pipelineCount
     *
     * @return integer
     */
    public function getPipelineCount()
    {
        return $this->pipelineCount;
    }

    /**
     * Set workflowCount
     *
     * @param integer $workflowCount
     *
     * @return Account
     */
    public function setWorkflowCount($workflowCount)
    {
        $this->workflowCount = $workflowCount;

        return $this;
    }

    /**
     * Get workflowCount
     *
     * @return integer
     */
    public function getWorkflowCount()
    {
        return $this->workflowCount;
    }

    /**
     * Set sourceCount
     *
     * @param integer $sourceCount
     *
     * @return Account
     */
    public function setSourceCount($sourceCount)
    {
        $this->sourceCount = $sourceCount;

        return $this;
    }

    /**
     * Get sourceCount
     *
     * @return integer
     */
    public function getSourceCount()
    {
        return $this->sourceCount;
    }

    /**
     * Set enabledMemberCount
     *
     * @param integer $enabledMemberCount
     *
     * @return Account
     */
    public function setEnabledMemberCount($enabledMemberCount)
    {
        $this->enabledMemberCount = $enabledMemberCount;

        return $this;
    }

    /**
     * Get enabledMemberCount
     *
     * @return integer
     */
    public function getEnabledMemberCount()
    {
        return $this->enabledMemberCount;
    }

    /**
     * Set enabledWorkspaceCount
     *
     * @param integer $enabledWorkspaceCount
     *
     * @return Account
     */
    public function setEnabledWorkspaceCount($enabledWorkspaceCount)
    {
        $this->enabledWorkspaceCount = $enabledWorkspaceCount;

        return $this;
    }

    /**
     * Get enabledWorkspaceCount
     *
     * @return integer
     */
    public function getEnabledWorkspaceCount()
    {
        return $this->enabledWorkspaceCount;
    }

    /**
     * Set enabledPipelineCount
     *
     * @param integer $enabledPipelineCount
     *
     * @return Account
     */
    public function setEnabledPipelineCount($enabledPipelineCount)
    {
        $this->enabledPipelineCount = $enabledPipelineCount;

        return $this;
    }

    /**
     * Get enabledPipelineCount
     *
     * @return integer
     */
    public function getEnabledPipelineCount()
    {
        return $this->enabledPipelineCount;
    }

    /**
     * Set enabledWorkflowCount
     *
     * @param integer $enabledWorkflowCount
     *
     * @return Account
     */
    public function setEnabledWorkflowCount($enabledWorkflowCount)
    {
        $this->enabledWorkflowCount = $enabledWorkflowCount;

        return $this;
    }

    /**
     * Get enabledWorkflowCount
     *
     * @return integer
     */
    public function getEnabledWorkflowCount()
    {
        return $this->enabledWorkflowCount;
    }

    /**
     * Set enabledSourceCount
     *
     * @param integer $enabledSourceCount
     *
     * @return Account
     */
    public function setEnabledSourceCount($enabledSourceCount)
    {
        $this->enabledSourceCount = $enabledSourceCount;

        return $this;
    }

    /**
     * Get enabledSourceCount
     *
     * @return integer
     */
    public function getEnabledSourceCount()
    {
        return $this->enabledSourceCount;
    }

    /**
     * Set dailyLeadsScanned
     *
     * @param integer $dailyLeadsScanned
     *
     * @return Account
     */
    public function setDailyLeadsScanned($dailyLeadsScanned)
    {
        $this->dailyLeadsScanned = $dailyLeadsScanned;

        return $this;
    }

    /**
     * Get dailyLeadsScanned
     *
     * @return integer
     */
    public function getDailyLeadsScanned()
    {
        return $this->dailyLeadsScanned;
    }

    /**
     * Set planStartedAt
     *
     * @param integer $planStartedAt
     *
     * @return Account
     */
    public function setPlanStartedAt($planStartedAt)
    {
        $this->planStartedAt = $planStartedAt;

        return $this;
    }

    /**
     * Get planStartedAt
     *
     * @return integer
     */
    public function getPlanStartedAt()
    {
        return $this->planStartedAt;
    }

    /**
     * Set ownerId
     *
     * @param integer $ownerId
     *
     * @return Account
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
     * @return Account
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
     * @return Account
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
     * @return Account
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
    public function getWorkspacesArray()
    {
        $workspaces = [];
        foreach ($this->getWorkspaces() as $workspace) {
            $workspaces[] = $workspace->toArray();
        }
        return $workspaces;
    }

    /**
     *
     * @return type
     */
    public function getWorkspaces()
    {
        return $this->workspaces;
    }

    /**
     *
     * @param type $workspaces
     * @return \AppBundle\Entity\Workspace
     */
    public function setWorkspaces($workspaces)
    {
        $this->workspaces = $workspaces;
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
     * @return array
     */
    public function toArray()
    {
        $arr = [];
        $arr['id'] = $this->getId();
        $arr['name'] = $this->getName();
        $arr['type'] = $this->getType();
        $arr['plan_id'] = $this->getPlanId();
        $arr['plan_class'] = $this->getPlanClass();
        $arr['next_plan_class'] = $this->getNextPlanClass();
        $arr['member_count'] = $this->getMemberCount();
        $arr['credit_balance'] = $this->getCreditBalance();
        $arr['accepted_deal_count'] = $this->getAcceptedDealCount();
        $arr['pipeline_count'] = $this->getPipelineCount();
        $arr['workflow_count'] = $this->getWorkflowCount();
        $arr['source_count'] = $this->getSourceCount();
        $arr['enabled_member_count'] = $this->getEnabledMemberCount();
        $arr['enabled_pipeline_count'] = $this->getEnabledPipelineCount();
        $arr['enabled_workflow_count'] = $this->getEnabledWorkflowCount();
        $arr['enabled_source_count'] = $this->getEnabledSourceCount();
        $arr['daily_leads_scanned'] = $this->getDailyLeadsScanned();
        $arr['membership'] = json_decode('{"role":"owner","is_enabled":true}');
        $arr['is_enabled'] = $this->getIsEnabled();
        $arr['created_at'] = $this->getCreatedAt();
        $arr['updated_at'] = $this->getUpdatedAt();
        return $arr;
    }
}
