<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientlySubIndustries
 *
 * @ORM\Table(name="cliently_sub_industries")
 * @ORM\Entity
 */
class ClientlySubIndustries
{
    /**
     * @var integer
     *
     * @ORM\Column(name="sub_industry_id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $subIndustryId;

    /**
     * @var integer
     *
     * @ORM\Column(name="sub_industry_industryId", type="bigint", nullable=true)
     */
    private $subIndustryIndustryid;

    /**
     * @var string
     *
     * @ORM\Column(name="sub_industry_name", type="string", length=255, nullable=true)
     */
    private $subIndustryName;

    /**
     * @var string
     *
     * @ORM\Column(name="sub_industry_short_description", type="text", length=65535, nullable=true)
     */
    private $subIndustryShortDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="sub_industry_description", type="text", length=65535, nullable=true)
     */
    private $subIndustryDescription;

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
     * Get subIndustryId
     *
     * @return integer
     */
    public function getSubIndustryId()
    {
        return $this->subIndustryId;
    }

    /**
     * Set subIndustryIndustryid
     *
     * @param integer $subIndustryIndustryid
     *
     * @return ClientlySubIndustries
     */
    public function setSubIndustryIndustryid($subIndustryIndustryid)
    {
        $this->subIndustryIndustryid = $subIndustryIndustryid;

        return $this;
    }

    /**
     * Get subIndustryIndustryid
     *
     * @return integer
     */
    public function getSubIndustryIndustryid()
    {
        return $this->subIndustryIndustryid;
    }

    /**
     * Set subIndustryName
     *
     * @param string $subIndustryName
     *
     * @return ClientlySubIndustries
     */
    public function setSubIndustryName($subIndustryName)
    {
        $this->subIndustryName = $subIndustryName;

        return $this;
    }

    /**
     * Get subIndustryName
     *
     * @return string
     */
    public function getSubIndustryName()
    {
        return $this->subIndustryName;
    }

    /**
     * Set subIndustryShortDescription
     *
     * @param string $subIndustryShortDescription
     *
     * @return ClientlySubIndustries
     */
    public function setSubIndustryShortDescription($subIndustryShortDescription)
    {
        $this->subIndustryShortDescription = $subIndustryShortDescription;

        return $this;
    }

    /**
     * Get subIndustryShortDescription
     *
     * @return string
     */
    public function getSubIndustryShortDescription()
    {
        return $this->subIndustryShortDescription;
    }

    /**
     * Set subIndustryDescription
     *
     * @param string $subIndustryDescription
     *
     * @return ClientlySubIndustries
     */
    public function setSubIndustryDescription($subIndustryDescription)
    {
        $this->subIndustryDescription = $subIndustryDescription;

        return $this;
    }

    /**
     * Get subIndustryDescription
     *
     * @return string
     */
    public function getSubIndustryDescription()
    {
        return $this->subIndustryDescription;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return ClientlySubIndustries
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
     * @return ClientlySubIndustries
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
     * @return ClientlySubIndustries
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
     * @return ClientlySubIndustries
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
