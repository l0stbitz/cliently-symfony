<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DealStage
 *
 * @ORM\Table(name="deal_stage", uniqueConstraints={@ORM\UniqueConstraint(name="_key", columns={"deal_id", "stage_id"})}, indexes={@ORM\Index(name="deal_stage_stage", columns={"stage_id"})})
 * @ORM\Entity
 */
class DealStage
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
     * @ORM\Column(name="deal_id", type="integer", nullable=false)
     */
    private $dealId;

    /**
     * @var integer
     *
     * @ORM\Column(name="stage_id", type="integer", nullable=false)
     */
    private $stageId;

    /**
     * @var integer
     *
     * @ORM\Column(name="duration_total", type="integer", nullable=false)
     */
    private $durationTotal;

    /**
     * @var integer
     *
     * @ORM\Column(name="duration_last", type="integer", nullable=false)
     */
    private $durationLast;

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
     * Set dealId
     *
     * @param integer $dealId
     *
     * @return DealStage
     */
    public function setDealId($dealId)
    {
        $this->dealId = $dealId;

        return $this;
    }

    /**
     * Get dealId
     *
     * @return integer
     */
    public function getDealId()
    {
        return $this->dealId;
    }

    /**
     * Set stageId
     *
     * @param integer $stageId
     *
     * @return DealStage
     */
    public function setStageId($stageId)
    {
        $this->stageId = $stageId;

        return $this;
    }

    /**
     * Get stageId
     *
     * @return integer
     */
    public function getStageId()
    {
        return $this->stageId;
    }

    /**
     * Set durationTotal
     *
     * @param integer $durationTotal
     *
     * @return DealStage
     */
    public function setDurationTotal($durationTotal)
    {
        $this->durationTotal = $durationTotal;

        return $this;
    }

    /**
     * Get durationTotal
     *
     * @return integer
     */
    public function getDurationTotal()
    {
        return $this->durationTotal;
    }

    /**
     * Set durationLast
     *
     * @param integer $durationLast
     *
     * @return DealStage
     */
    public function setDurationLast($durationLast)
    {
        $this->durationLast = $durationLast;

        return $this;
    }

    /**
     * Get durationLast
     *
     * @return integer
     */
    public function getDurationLast()
    {
        return $this->durationLast;
    }

    /**
     * Set createdAt
     *
     * @param integer $createdAt
     *
     * @return DealStage
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
     * @return DealStage
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
     * @return DealStage
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
