<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientTwitter
 *
 * @ORM\Table(name="client_twitter", uniqueConstraints={@ORM\UniqueConstraint(name="_key", columns={"client_id", "source_id"})}, indexes={@ORM\Index(name="owner_id", columns={"owner_id"}), @ORM\Index(name="status", columns={"status"})})
 * @ORM\Entity
 */
class ClientTwitter
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
     * @ORM\Column(name="client_id", type="integer", nullable=false)
     */
    private $clientId;

    /**
     * @var integer
     *
     * @ORM\Column(name="source_id", type="integer", nullable=false)
     */
    private $sourceId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_follower", type="boolean", nullable=false)
     */
    private $isFollower;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_followed", type="boolean", nullable=false)
     */
    private $isFollowed;

    /**
     * @var integer
     *
     * @ORM\Column(name="new_events_count", type="integer", nullable=false)
     */
    private $newEventsCount;

    /**
     * @var integer
     *
     * @ORM\Column(name="owner_id", type="integer", nullable=false)
     */
    private $ownerId;

    /**
     * @var integer
     *
     * @ORM\Column(name="workspace_id", type="integer", nullable=false)
     */
    private $workspaceId;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

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
     * Set clientId
     *
     * @param integer $clientId
     *
     * @return ClientTwitter
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * Get clientId
     *
     * @return integer
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Set sourceId
     *
     * @param integer $sourceId
     *
     * @return ClientTwitter
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
     * Set isFollower
     *
     * @param boolean $isFollower
     *
     * @return ClientTwitter
     */
    public function setIsFollower($isFollower)
    {
        $this->isFollower = $isFollower;

        return $this;
    }

    /**
     * Get isFollower
     *
     * @return boolean
     */
    public function getIsFollower()
    {
        return $this->isFollower;
    }

    /**
     * Set isFollowed
     *
     * @param boolean $isFollowed
     *
     * @return ClientTwitter
     */
    public function setIsFollowed($isFollowed)
    {
        $this->isFollowed = $isFollowed;

        return $this;
    }

    /**
     * Get isFollowed
     *
     * @return boolean
     */
    public function getIsFollowed()
    {
        return $this->isFollowed;
    }

    /**
     * Set newEventsCount
     *
     * @param integer $newEventsCount
     *
     * @return ClientTwitter
     */
    public function setNewEventsCount($newEventsCount)
    {
        $this->newEventsCount = $newEventsCount;

        return $this;
    }

    /**
     * Get newEventsCount
     *
     * @return integer
     */
    public function getNewEventsCount()
    {
        return $this->newEventsCount;
    }

    /**
     * Set ownerId
     *
     * @param integer $ownerId
     *
     * @return ClientTwitter
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
     * Set workspaceId
     *
     * @param integer $workspaceId
     *
     * @return ClientTwitter
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
     * Set status
     *
     * @param integer $status
     *
     * @return ClientTwitter
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdAt
     *
     * @param integer $createdAt
     *
     * @return ClientTwitter
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
     * @return ClientTwitter
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
     * @return ClientTwitter
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
