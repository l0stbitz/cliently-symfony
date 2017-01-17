<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Lead
 *
 * @ORM\Table(name="lead", indexes={@ORM\Index(name="deal_source_id", columns={"source_id"}), @ORM\Index(name="client_source_id", columns={"client_source_id"}), @ORM\Index(name="company_source_id", columns={"company_source_id"}), @ORM\Index(name="owner_id", columns={"owner_id"}), @ORM\Index(name="is_enabled", columns={"is_enabled"}), @ORM\Index(name="pipeline_id", columns={"workspace_id"})})
 * @ORM\Entity
 */
class Lead
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
     * @ORM\Column(name="source_id", type="integer", nullable=false)
     */
    private $sourceId;

    /**
     * @var integer
     *
     * @ORM\Column(name="client_source_id", type="integer", nullable=false)
     */
    private $clientSourceId;

    /**
     * @var integer
     *
     * @ORM\Column(name="company_source_id", type="integer", nullable=false)
     */
    private $companySourceId;

    /**
     * @var integer
     *
     * @ORM\Column(name="workflow_id", type="integer", nullable=false)
     */
    private $workflowId;

    /**
     * @var integer
     *
     * @ORM\Column(name="action_id", type="integer", nullable=false)
     */
    private $actionId;

    /**
     * @var string
     *
     * @ORM\Column(name="action_values", type="text", length=65535, nullable=false)
     */
    private $actionValues;

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
     * @var boolean
     *
     * @ORM\Column(name="is_enabled", type="boolean", nullable=false)
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
     * @var integer
     *
     * @ORM\Column(name="accessed_at", type="integer", nullable=false)
     */
    private $accessedAt;



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
     * Set sourceId
     *
     * @param integer $sourceId
     *
     * @return Lead
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
     * Set clientSourceId
     *
     * @param integer $clientSourceId
     *
     * @return Lead
     */
    public function setClientSourceId($clientSourceId)
    {
        $this->clientSourceId = $clientSourceId;

        return $this;
    }

    /**
     * Get clientSourceId
     *
     * @return integer
     */
    public function getClientSourceId()
    {
        return $this->clientSourceId;
    }

    /**
     * Set companySourceId
     *
     * @param integer $companySourceId
     *
     * @return Lead
     */
    public function setCompanySourceId($companySourceId)
    {
        $this->companySourceId = $companySourceId;

        return $this;
    }

    /**
     * Get companySourceId
     *
     * @return integer
     */
    public function getCompanySourceId()
    {
        return $this->companySourceId;
    }

    /**
     * Set workflowId
     *
     * @param integer $workflowId
     *
     * @return Lead
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
     * Set actionId
     *
     * @param integer $actionId
     *
     * @return Lead
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
     * @return Lead
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
     * Set workspaceId
     *
     * @param integer $workspaceId
     *
     * @return Lead
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
     * @return Lead
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
     * @param boolean $isEnabled
     *
     * @return Lead
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
     * Set createdAt
     *
     * @param integer $createdAt
     *
     * @return Lead
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
     * @return Lead
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
     * @return Lead
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
}
