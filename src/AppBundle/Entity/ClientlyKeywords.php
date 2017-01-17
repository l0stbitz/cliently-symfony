<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientlyKeywords
 *
 * @ORM\Table(name="cliently_keywords")
 * @ORM\Entity
 */
class ClientlyKeywords
{
    /**
     * @var integer
     *
     * @ORM\Column(name="keywords_id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $keywordsId;

    /**
     * @var integer
     *
     * @ORM\Column(name="keywords_industryId", type="bigint", nullable=true)
     */
    private $keywordsIndustryid;

    /**
     * @var integer
     *
     * @ORM\Column(name="keywords_subIndustryId", type="bigint", nullable=true)
     */
    private $keywordsSubindustryid;

    /**
     * @var string
     *
     * @ORM\Column(name="keywords_name", type="string", length=255, nullable=true)
     */
    private $keywordsName;

    /**
     * @var string
     *
     * @ORM\Column(name="keywords_short_description", type="text", length=65535, nullable=true)
     */
    private $keywordsShortDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="keywords_description", type="text", length=65535, nullable=true)
     */
    private $keywordsDescription;

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
     * Get keywordsId
     *
     * @return integer
     */
    public function getKeywordsId()
    {
        return $this->keywordsId;
    }

    /**
     * Set keywordsIndustryid
     *
     * @param integer $keywordsIndustryid
     *
     * @return ClientlyKeywords
     */
    public function setKeywordsIndustryid($keywordsIndustryid)
    {
        $this->keywordsIndustryid = $keywordsIndustryid;

        return $this;
    }

    /**
     * Get keywordsIndustryid
     *
     * @return integer
     */
    public function getKeywordsIndustryid()
    {
        return $this->keywordsIndustryid;
    }

    /**
     * Set keywordsSubindustryid
     *
     * @param integer $keywordsSubindustryid
     *
     * @return ClientlyKeywords
     */
    public function setKeywordsSubindustryid($keywordsSubindustryid)
    {
        $this->keywordsSubindustryid = $keywordsSubindustryid;

        return $this;
    }

    /**
     * Get keywordsSubindustryid
     *
     * @return integer
     */
    public function getKeywordsSubindustryid()
    {
        return $this->keywordsSubindustryid;
    }

    /**
     * Set keywordsName
     *
     * @param string $keywordsName
     *
     * @return ClientlyKeywords
     */
    public function setKeywordsName($keywordsName)
    {
        $this->keywordsName = $keywordsName;

        return $this;
    }

    /**
     * Get keywordsName
     *
     * @return string
     */
    public function getKeywordsName()
    {
        return $this->keywordsName;
    }

    /**
     * Set keywordsShortDescription
     *
     * @param string $keywordsShortDescription
     *
     * @return ClientlyKeywords
     */
    public function setKeywordsShortDescription($keywordsShortDescription)
    {
        $this->keywordsShortDescription = $keywordsShortDescription;

        return $this;
    }

    /**
     * Get keywordsShortDescription
     *
     * @return string
     */
    public function getKeywordsShortDescription()
    {
        return $this->keywordsShortDescription;
    }

    /**
     * Set keywordsDescription
     *
     * @param string $keywordsDescription
     *
     * @return ClientlyKeywords
     */
    public function setKeywordsDescription($keywordsDescription)
    {
        $this->keywordsDescription = $keywordsDescription;

        return $this;
    }

    /**
     * Get keywordsDescription
     *
     * @return string
     */
    public function getKeywordsDescription()
    {
        return $this->keywordsDescription;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return ClientlyKeywords
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
     * @return ClientlyKeywords
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
     * @return ClientlyKeywords
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
     * @return ClientlyKeywords
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
