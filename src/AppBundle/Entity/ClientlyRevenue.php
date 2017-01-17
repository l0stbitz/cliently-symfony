<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientlyRevenue
 *
 * @ORM\Table(name="cliently_revenue")
 * @ORM\Entity
 */
class ClientlyRevenue
{
    /**
     * @var integer
     *
     * @ORM\Column(name="revenue_id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $revenueId;

    /**
     * @var string
     *
     * @ORM\Column(name="revenue_name", type="string", length=255, nullable=true)
     */
    private $revenueName;

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
     * Get revenueId
     *
     * @return integer
     */
    public function getRevenueId()
    {
        return $this->revenueId;
    }

    /**
     * Set revenueName
     *
     * @param string $revenueName
     *
     * @return ClientlyRevenue
     */
    public function setRevenueName($revenueName)
    {
        $this->revenueName = $revenueName;

        return $this;
    }

    /**
     * Get revenueName
     *
     * @return string
     */
    public function getRevenueName()
    {
        return $this->revenueName;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return ClientlyRevenue
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
     * @return ClientlyRevenue
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
     * @return ClientlyRevenue
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
     * @return ClientlyRevenue
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
