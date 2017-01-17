<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Auth
 *
 * @ORM\Table(name="auth")
 * @ORM\Entity
 */
class Auth
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
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="key", type="string", length=255, nullable=false)
     */
    private $key;

    /**
     * @var string
     *
     * @ORM\Column(name="key_prev", type="string", length=255, nullable=false)
     */
    private $keyPrev;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=45, nullable=false)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="ua", type="string", length=500, nullable=false)
     */
    private $ua;

    /**
     * @var integer
     *
     * @ORM\Column(name="ua_hash", type="integer", nullable=false)
     */
    private $uaHash;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return Auth
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set key
     *
     * @param string $key
     *
     * @return Auth
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set keyPrev
     *
     * @param string $keyPrev
     *
     * @return Auth
     */
    public function setKeyPrev($keyPrev)
    {
        $this->keyPrev = $keyPrev;

        return $this;
    }

    /**
     * Get keyPrev
     *
     * @return string
     */
    public function getKeyPrev()
    {
        return $this->keyPrev;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return Auth
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set ua
     *
     * @param string $ua
     *
     * @return Auth
     */
    public function setUa($ua)
    {
        $this->ua = $ua;

        return $this;
    }

    /**
     * Get ua
     *
     * @return string
     */
    public function getUa()
    {
        return $this->ua;
    }

    /**
     * Set uaHash
     *
     * @param integer $uaHash
     *
     * @return Auth
     */
    public function setUaHash($uaHash)
    {
        $this->uaHash = $uaHash;

        return $this;
    }

    /**
     * Get uaHash
     *
     * @return integer
     */
    public function getUaHash()
    {
        return $this->uaHash;
    }

    /**
     * Set createdAt
     *
     * @param integer $createdAt
     *
     * @return Auth
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
     * @return Auth
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
}
