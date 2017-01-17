<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientlySetting
 *
 * @ORM\Table(name="cliently_setting")
 * @ORM\Entity
 */
class ClientlySetting
{
    /**
     * @var integer
     *
     * @ORM\Column(name="setting_id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $settingId;

    /**
     * @var string
     *
     * @ORM\Column(name="setting_title", type="string", length=255, nullable=true)
     */
    private $settingTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="setting_content", type="text", nullable=true)
     */
    private $settingContent;

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
     * Get settingId
     *
     * @return integer
     */
    public function getSettingId()
    {
        return $this->settingId;
    }

    /**
     * Set settingTitle
     *
     * @param string $settingTitle
     *
     * @return ClientlySetting
     */
    public function setSettingTitle($settingTitle)
    {
        $this->settingTitle = $settingTitle;

        return $this;
    }

    /**
     * Get settingTitle
     *
     * @return string
     */
    public function getSettingTitle()
    {
        return $this->settingTitle;
    }

    /**
     * Set settingContent
     *
     * @param string $settingContent
     *
     * @return ClientlySetting
     */
    public function setSettingContent($settingContent)
    {
        $this->settingContent = $settingContent;

        return $this;
    }

    /**
     * Get settingContent
     *
     * @return string
     */
    public function getSettingContent()
    {
        return $this->settingContent;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return ClientlySetting
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
     * @return ClientlySetting
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
     * @return ClientlySetting
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
     * @return ClientlySetting
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
