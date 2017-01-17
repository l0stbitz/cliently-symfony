<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Workspace
 *
 * @ORM\Table(name="workspace")
 * @ORM\Entity
 */
class Workspace
{

    const TYPE_BY_ID = [
        1 => ['id' => 1, 'class' => 'standard'],
    ];
    const TYPE_BY_CLASS = [
        'standard' => ['id' => 1, 'class' => 'standard'],
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
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="account_id", type="integer", nullable=false)
     */
    private $accountId;

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
     * @ORM\Column(name="owner_id", type="integer", nullable=false)
     */
    private $ownerId = 0;

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
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="workspaces")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     */
    private $account;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position = 0;

    /**
     *
     * @ORM\OneToMany(targetEntity="WorkspaceMember", mappedBy="workspace")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $workspaceMembers;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="workspaces")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $user;

    /**
     *
     * @ORM\OneToMany(targetEntity="Pipeline", mappedBy="workspace")
     */
    private $pipelines;

    /**
     *
     * @ORM\OneToMany(targetEntity="Client", mappedBy="workspace")
     */
    private $clients;

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
     * @return Workspace
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
     * @return Workspace
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
     * Set accountId
     *
     * @param integer $accountId
     *
     * @return Workspace
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;

        return $this;
    }

    /**
     * Get accountId
     *
     * @return integer
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * Set memberCount
     *
     * @param integer $memberCount
     *
     * @return Workspace
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
     * @return Workspace
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
     * @return Workspace
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
     * Set pipelineCount
     *
     * @param integer $pipelineCount
     *
     * @return Workspace
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
     * @return Workspace
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
     * @return Workspace
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
     * @return Workspace
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
     * Set enabledPipelineCount
     *
     * @param integer $enabledPipelineCount
     *
     * @return Workspace
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
     * @return Workspace
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
     * @return Workspace
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
     * @return Workspace
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
     * Set ownerId
     *
     * @param integer $ownerId
     *
     * @return Workspace
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
     * @return Workspace
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
     * @return Workspace
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
     * @return Workspace
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
     * Set account
     *
     * @param Account $account
     *
     * @return AccountCategories
     */
    public function setAccount($account = null)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return account
     */
    public function getAccount()
    {
        return $this->account;
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
        $arr['account_id'] = $this->getAccount()->getId();
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
        $arr['is_enabled'] = $this->getIsEnabled();
        $arr['membership'] = json_decode('{"role":"owner","credit_balance":6,"accepted_deal_count":0,"is_enabled":true}');
        $arr['workspace_members'] = $this->getWorkspaceMembersArray();
        return $arr;
    }

    /**
     *
     * @return type
     */
    public function getWorkspaceMembersArray()
    {
        $workspaceMembers = [];
        foreach ($this->getWorkspaceMembers() as $workspace) {
            $workspaceMembers[] = $workspace->toArray();
        }
        return $workspaceMembers;
    }

    /**
     *
     * @return type
     */
    public function getWorkspaceMembers()
    {
        return $this->workspaceMembers;
    }

    /**
     *
     * @param type $workspaceMembers
     * @return \AppBundle\Entity\WorkspaceMember
     */
    public function setWorkspaceMembers($workspaceMembers)
    {
        $this->workspaceMembers = $workspaceMembers;
        return $this;
    }

    /**
     *
     * @return type
     */
    public function getPipelinesArray()
    {
        $pipelines = [];
        foreach ($this->getPipelines() as $pipeline) {
            $pipelines[] = $pipeline->toArray();
        }
        return $pipelines;
    }

    /**
     *
     * @return type
     */
    public function getPipelines()
    {
        return $this->pipelines;
    }

    /**
     *
     * @param type $pipelines
     * @return \AppBundle\Entity\Pipeline
     */
    public function setPipelines($pipelines)
    {
        $this->pipelines = $pipelines;
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
}
