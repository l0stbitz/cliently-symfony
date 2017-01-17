<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientlySeniority
 *
 * @ORM\Table(name="cliently_seniority")
 * @ORM\Entity
 */
class ClientlySeniority
{
    /**
     * @var integer
     *
     * @ORM\Column(name="seniority_id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $seniorityId;

    /**
     * @var string
     *
     * @ORM\Column(name="seniority_name", type="string", length=255, nullable=true)
     */
    private $seniorityName;

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
     * Get seniorityId
     *
     * @return integer
     */
    public function getSeniorityId()
    {
        return $this->seniorityId;
    }

    /**
     * Set seniorityName
     *
     * @param string $seniorityName
     *
     * @return ClientlySeniority
     */
    public function setSeniorityName($seniorityName)
    {
        $this->seniorityName = $seniorityName;

        return $this;
    }

    /**
     * Get seniorityName
     *
     * @return string
     */
    public function getSeniorityName()
    {
        return $this->seniorityName;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return ClientlySeniority
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
     * @return ClientlySeniority
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
     * @return ClientlySeniority
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
     * @return ClientlySeniority
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
