<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientlyIndustries
 *
 * @ORM\Table(name="cliently_industries")
 * @ORM\Entity
 */
class ClientlyIndustries
{
    /**
     * @var integer
     *
     * @ORM\Column(name="industry_id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $industryId;

    /**
     * @var string
     *
     * @ORM\Column(name="industry_name", type="string", length=255, nullable=true)
     */
    private $industryName;

    /**
     * @var string
     *
     * @ORM\Column(name="industry_image", type="string", length=255, nullable=true)
     */
    private $industryImage;

    /**
     * @var string
     *
     * @ORM\Column(name="industry_short_description", type="text", length=65535, nullable=true)
     */
    private $industryShortDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="industry_description", type="text", length=65535, nullable=true)
     */
    private $industryDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="industry_home_text", type="text", length=65535, nullable=true)
     */
    private $industryHomeText;

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
     * Get industryId
     *
     * @return integer
     */
    public function getIndustryId()
    {
        return $this->industryId;
    }

    /**
     * Set industryName
     *
     * @param string $industryName
     *
     * @return ClientlyIndustries
     */
    public function setIndustryName($industryName)
    {
        $this->industryName = $industryName;

        return $this;
    }

    /**
     * Get industryName
     *
     * @return string
     */
    public function getIndustryName()
    {
        return $this->industryName;
    }

    /**
     * Set industryImage
     *
     * @param string $industryImage
     *
     * @return ClientlyIndustries
     */
    public function setIndustryImage($industryImage)
    {
        $this->industryImage = $industryImage;

        return $this;
    }

    /**
     * Get industryImage
     *
     * @return string
     */
    public function getIndustryImage()
    {
        return $this->industryImage;
    }

    /**
     * Set industryShortDescription
     *
     * @param string $industryShortDescription
     *
     * @return ClientlyIndustries
     */
    public function setIndustryShortDescription($industryShortDescription)
    {
        $this->industryShortDescription = $industryShortDescription;

        return $this;
    }

    /**
     * Get industryShortDescription
     *
     * @return string
     */
    public function getIndustryShortDescription()
    {
        return $this->industryShortDescription;
    }

    /**
     * Set industryDescription
     *
     * @param string $industryDescription
     *
     * @return ClientlyIndustries
     */
    public function setIndustryDescription($industryDescription)
    {
        $this->industryDescription = $industryDescription;

        return $this;
    }

    /**
     * Get industryDescription
     *
     * @return string
     */
    public function getIndustryDescription()
    {
        return $this->industryDescription;
    }

    /**
     * Set industryHomeText
     *
     * @param string $industryHomeText
     *
     * @return ClientlyIndustries
     */
    public function setIndustryHomeText($industryHomeText)
    {
        $this->industryHomeText = $industryHomeText;

        return $this;
    }

    /**
     * Get industryHomeText
     *
     * @return string
     */
    public function getIndustryHomeText()
    {
        return $this->industryHomeText;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return ClientlyIndustries
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
     * @return ClientlyIndustries
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
     * @return ClientlyIndustries
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
     * @return ClientlyIndustries
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
