<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientlyIndustryLeads
 *
 * @ORM\Table(name="cliently_industry_leads")
 * @ORM\Entity
 */
class ClientlyIndustryLeads
{
    /**
     * @var integer
     *
     * @ORM\Column(name="industry_leads_id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $industryLeadsId;

    /**
     * @var string
     *
     * @ORM\Column(name="industry_leads_job_title", type="string", length=255, nullable=true)
     */
    private $industryLeadsJobTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="industry_leads_role", type="string", length=255, nullable=true)
     */
    private $industryLeadsRole;

    /**
     * @var string
     *
     * @ORM\Column(name="industry_leads_seniority", type="string", length=255, nullable=true)
     */
    private $industryLeadsSeniority;

    /**
     * @var integer
     *
     * @ORM\Column(name="industry_leads_industry_id", type="bigint", nullable=true)
     */
    private $industryLeadsIndustryId;

    /**
     * @var string
     *
     * @ORM\Column(name="industry_leads_size", type="string", length=255, nullable=true)
     */
    private $industryLeadsSize;

    /**
     * @var string
     *
     * @ORM\Column(name="industry_leads_revenue", type="string", length=255, nullable=true)
     */
    private $industryLeadsRevenue;

    /**
     * @var integer
     *
     * @ORM\Column(name="industry_leads_city_id", type="bigint", nullable=true)
     */
    private $industryLeadsCityId;

    /**
     * @var integer
     *
     * @ORM\Column(name="industry_leads_state_id", type="bigint", nullable=true)
     */
    private $industryLeadsStateId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive = '1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_delete", type="boolean", nullable=true)
     */
    private $isDelete = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime", nullable=true)
     */
    private $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified_date", type="datetime", nullable=true)
     */
    private $modifiedDate;



    /**
     * Get industryLeadsId
     *
     * @return integer
     */
    public function getIndustryLeadsId()
    {
        return $this->industryLeadsId;
    }

    /**
     * Set industryLeadsJobTitle
     *
     * @param string $industryLeadsJobTitle
     *
     * @return ClientlyIndustryLeads
     */
    public function setIndustryLeadsJobTitle($industryLeadsJobTitle)
    {
        $this->industryLeadsJobTitle = $industryLeadsJobTitle;

        return $this;
    }

    /**
     * Get industryLeadsJobTitle
     *
     * @return string
     */
    public function getIndustryLeadsJobTitle()
    {
        return $this->industryLeadsJobTitle;
    }

    /**
     * Set industryLeadsRole
     *
     * @param string $industryLeadsRole
     *
     * @return ClientlyIndustryLeads
     */
    public function setIndustryLeadsRole($industryLeadsRole)
    {
        $this->industryLeadsRole = $industryLeadsRole;

        return $this;
    }

    /**
     * Get industryLeadsRole
     *
     * @return string
     */
    public function getIndustryLeadsRole()
    {
        return $this->industryLeadsRole;
    }

    /**
     * Set industryLeadsSeniority
     *
     * @param string $industryLeadsSeniority
     *
     * @return ClientlyIndustryLeads
     */
    public function setIndustryLeadsSeniority($industryLeadsSeniority)
    {
        $this->industryLeadsSeniority = $industryLeadsSeniority;

        return $this;
    }

    /**
     * Get industryLeadsSeniority
     *
     * @return string
     */
    public function getIndustryLeadsSeniority()
    {
        return $this->industryLeadsSeniority;
    }

    /**
     * Set industryLeadsIndustryId
     *
     * @param integer $industryLeadsIndustryId
     *
     * @return ClientlyIndustryLeads
     */
    public function setIndustryLeadsIndustryId($industryLeadsIndustryId)
    {
        $this->industryLeadsIndustryId = $industryLeadsIndustryId;

        return $this;
    }

    /**
     * Get industryLeadsIndustryId
     *
     * @return integer
     */
    public function getIndustryLeadsIndustryId()
    {
        return $this->industryLeadsIndustryId;
    }

    /**
     * Set industryLeadsSize
     *
     * @param string $industryLeadsSize
     *
     * @return ClientlyIndustryLeads
     */
    public function setIndustryLeadsSize($industryLeadsSize)
    {
        $this->industryLeadsSize = $industryLeadsSize;

        return $this;
    }

    /**
     * Get industryLeadsSize
     *
     * @return string
     */
    public function getIndustryLeadsSize()
    {
        return $this->industryLeadsSize;
    }

    /**
     * Set industryLeadsRevenue
     *
     * @param string $industryLeadsRevenue
     *
     * @return ClientlyIndustryLeads
     */
    public function setIndustryLeadsRevenue($industryLeadsRevenue)
    {
        $this->industryLeadsRevenue = $industryLeadsRevenue;

        return $this;
    }

    /**
     * Get industryLeadsRevenue
     *
     * @return string
     */
    public function getIndustryLeadsRevenue()
    {
        return $this->industryLeadsRevenue;
    }

    /**
     * Set industryLeadsCityId
     *
     * @param integer $industryLeadsCityId
     *
     * @return ClientlyIndustryLeads
     */
    public function setIndustryLeadsCityId($industryLeadsCityId)
    {
        $this->industryLeadsCityId = $industryLeadsCityId;

        return $this;
    }

    /**
     * Get industryLeadsCityId
     *
     * @return integer
     */
    public function getIndustryLeadsCityId()
    {
        return $this->industryLeadsCityId;
    }

    /**
     * Set industryLeadsStateId
     *
     * @param integer $industryLeadsStateId
     *
     * @return ClientlyIndustryLeads
     */
    public function setIndustryLeadsStateId($industryLeadsStateId)
    {
        $this->industryLeadsStateId = $industryLeadsStateId;

        return $this;
    }

    /**
     * Get industryLeadsStateId
     *
     * @return integer
     */
    public function getIndustryLeadsStateId()
    {
        return $this->industryLeadsStateId;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return ClientlyIndustryLeads
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set isDelete
     *
     * @param boolean $isDelete
     *
     * @return ClientlyIndustryLeads
     */
    public function setIsDelete($isDelete)
    {
        $this->isDelete = $isDelete;

        return $this;
    }

    /**
     * Get isDelete
     *
     * @return boolean
     */
    public function getIsDelete()
    {
        return $this->isDelete;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return ClientlyIndustryLeads
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set modifiedDate
     *
     * @param \DateTime $modifiedDate
     *
     * @return ClientlyIndustryLeads
     */
    public function setModifiedDate($modifiedDate)
    {
        $this->modifiedDate = $modifiedDate;

        return $this;
    }

    /**
     * Get modifiedDate
     *
     * @return \DateTime
     */
    public function getModifiedDate()
    {
        return $this->modifiedDate;
    }
}
