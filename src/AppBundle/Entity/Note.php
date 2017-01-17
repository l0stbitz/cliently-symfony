<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Note
 *
 * @ORM\Table(name="note", indexes={@ORM\Index(name="note_owner", columns={"owner_id"}), @ORM\Index(name="note_lead", columns={"deal_id"}), @ORM\Index(name="note_status", columns={"status"}), @ORM\Index(name="client_id", columns={"client_id"})})
 * @ORM\Entity
 */
class Note
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
     * @ORM\Column(name="owner_id", type="integer", nullable=false)
     */
    private $ownerId;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    private $description = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="client_id", type="integer", nullable=false)
     */
    private $clientId;

    /**
     * @var integer
     *
     * @ORM\Column(name="deal_id", type="integer", nullable=false)
     */
    private $dealId;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_at", type="integer", nullable=false)
     */
    private $createdAt = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="updated_at", type="integer", nullable=false)
     */
    private $updatedAt = 0;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Deal", inversedBy="notes")
     * @ORM\JoinColumn(name="deal_id", referencedColumnName="id")
     */
    private $deal;
    
    /**
     * __construct
     * Insert description here
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function __construct()
    {
        $this->setCreatedAt(time());
    }

    
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
     * Set ownerId
     *
     * @param integer $ownerId
     *
     * @return Note
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
     * Set description
     *
     * @param string $description
     *
     * @return Note
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set clientId
     *
     * @param integer $clientId
     *
     * @return Note
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
     * Set dealId
     *
     * @param integer $dealId
     *
     * @return Note
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
     * Set status
     *
     * @param integer $status
     *
     * @return Note
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
     * @return Note
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
     * @return Note
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
     *
     * @return type
     */
    public function getDeal()
    {
        return $this->deal;
    }

    /**
     *
     * @param type $deal
     * @return \AppBundle\Entity\Deal
     */
    public function setDeal($deal)
    {
        $this->deal = $deal;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function toArray()
    {
        $arr = [];
        $arr['id'] = $this->getId();
        $arr['client_id'] = $this->getClientId();
        $arr['deal_id'] = $this->getDealId();
        $arr['owner_id'] = $this->getOwnerId();
        $arr['description'] = $this->getDescription();
        $arr['status'] = $this->getStatus();
        $arr['created_at'] = $this->getCreatedAt();
        $arr['updated_at'] = $this->getUpdatedAt();

        return $arr;
    }
}
