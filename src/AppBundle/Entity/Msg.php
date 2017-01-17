<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Msg
 *
 * @ORM\Table(name="msg",        uniqueConstraints={@ORM\UniqueConstraint(name="_key", 
 *      columns={"code", "owner_id", "integration_type"})}, 
 *      indexes={@ORM\Index(name="msg_owner", columns={"owner_id"}), 
 *               @ORM\Index(name="msg_lead",   columns={"deal_id"}), 
 *               @ORM\Index(name="msg_type",   columns={"type"}), 
 *               @ORM\Index(name="msg_own",    columns={"is_own"}), 
 *               @ORM\Index(name="msg_status", columns={"status"})})
 * @ORM\Entity
 */
class Msg
{

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const TYPE_EMAIL = 1;
    const TYPE_TWITTER_TWEET = 100;
    const TYPE_TWITTER_RETWEET = 101;
    const TYPE_TWITTER_QUOTE = 102;
    const TYPE_TWITTER_FOLLOW = 103;
    const TYPE_TWITTER_UNFOLLOW = 104;
    const TYPE_TWITTER_FAVORITE = 105;
    const TYPE_TWITTER_DIRECT = 107;

    public static $types = array(
        'twitter_tweet' => 100,
        'twitter_retweet' => 101,
        'twitter_quote' => 102,
        'twitter_follow' => 103,
        'twitter_unfollow' => 104,
        'twitter_favorite' => 105,
        'twitter_direct' => 107
    );
    public static $type_codes = array(
        100 => 'twitter_tweet',
        101 => 'twitter_retweet',
        102 => 'twitter_quote',
        103 => 'twitter_follow',
        104 => 'twitter_unfollow',
        105 => 'twitter_favorite',
        107 => 'twitter_direct'
    );

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name = '';

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
     * @var string
     *
     * @ORM\Column(name="attachments", type="text", length=65535, nullable=false)
     */
    private $attachments = '';

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
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="handle", type="string", length=100, nullable=false)
     */
    private $handle = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="integration_type", type="integer", nullable=false)
     */
    private $integrationType = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="sender_source_id", type="integer", nullable=false)
     */
    private $senderSourceId = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="recipient_source_id", type="integer", nullable=false)
     */
    private $recipientSourceId = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="cc", type="text", length=65535, nullable=false)
     */
    private $cc = '';

    /**
     * @var string
     *
     * @ORM\Column(name="bcc", type="text", length=65535, nullable=false)
     */
    private $bcc = '';

    /**
     * @var string
     *
     * @ORM\Column(name="uid", type="text", length=65535, nullable=false)
     */
    private $uid = '';
    //TODO: Explain why this field was renamed
    /**
     * @var string
     * 
     * @ORM\Column(name="refs", type="text", length=65535, nullable=false)
     */
    private $references = '';

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=50, nullable=false)
     */
    private $code = '';

    /**
     * @var string
     *
     * @ORM\Column(name="thread_code", type="string", length=50, nullable=false)
     */
    private $threadCode = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_own", type="integer", nullable=false)
     */
    private $isOwn = 0;

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
     * @ORM\ManyToOne(targetEntity="Deal", inversedBy="mails")
     * @ORM\JoinColumn(name="deal_id", referencedColumnName="id")
     */
    private $deal;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Source", inversedBy="msgs")
     * @ORM\JoinColumn(name="source_id", referencedColumnName="id")
     */
    private $source;

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
     * Set name
     *
     * @param string $name
     *
     * @return Msg
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set ownerId
     *
     * @param integer $ownerId
     *
     * @return Msg
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
     * @return Msg
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
     * Set attachments
     *
     * @param string $attachments
     *
     * @return Msg
     */
    public function setAttachments($attachments)
    {
        $this->attachments = $attachments;

        return $this;
    }

    /**
     * Get attachments
     *
     * @return string
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * Set clientId
     *
     * @param integer $clientId
     *
     * @return Msg
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
     * @return Msg
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
     * Set email
     *
     * @param string $email
     *
     * @return Msg
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set handle
     *
     * @param string $handle
     *
     * @return Msg
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;

        return $this;
    }

    /**
     * Get handle
     *
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * Set integrationType
     *
     * @param integer $integrationType
     *
     * @return Msg
     */
    public function setIntegrationType($integrationType)
    {
        $this->integrationType = $integrationType;

        return $this;
    }

    /**
     * Get integrationType
     *
     * @return integer
     */
    public function getIntegrationType()
    {
        return $this->integrationType;
    }

    /**
     * Set senderSourceId
     *
     * @param integer $senderSourceId
     *
     * @return Msg
     */
    public function setSenderSourceId($senderSourceId)
    {
        $this->senderSourceId = $senderSourceId;

        return $this;
    }

    /**
     * Get senderSourceId
     *
     * @return integer
     */
    public function getSenderSourceId()
    {
        return $this->senderSourceId;
    }

    /**
     * Set recipientSourceId
     *
     * @param integer $recipientSourceId
     *
     * @return Msg
     */
    public function setRecipientSourceId($recipientSourceId)
    {
        $this->recipientSourceId = $recipientSourceId;

        return $this;
    }

    /**
     * Get recipientSourceId
     *
     * @return integer
     */
    public function getRecipientSourceId()
    {
        return $this->recipientSourceId;
    }

    /**
     * Set cc
     *
     * @param string $cc
     *
     * @return Msg
     */
    public function setCc($cc)
    {
        $this->cc = $cc;

        return $this;
    }

    /**
     * Get cc
     *
     * @return string
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * Set bcc
     *
     * @param string $bcc
     *
     * @return Msg
     */
    public function setBcc($bcc)
    {
        $this->bcc = $bcc;

        return $this;
    }

    /**
     * Get bcc
     *
     * @return string
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * Set uid
     *
     * @param string $uid
     *
     * @return Msg
     */
    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * Get uid
     *
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set references
     *
     * @param string $references
     *
     * @return Msg
     */
    public function setReferences($references)
    {
        $this->references = $references;

        return $this;
    }

    /**
     * Get references
     *
     * @return string
     */
    public function getReferences()
    {
        return $this->references;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Msg
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set threadCode
     *
     * @param string $threadCode
     *
     * @return Msg
     */
    public function setThreadCode($threadCode)
    {
        $this->threadCode = $threadCode;

        return $this;
    }

    /**
     * Get threadCode
     *
     * @return string
     */
    public function getThreadCode()
    {
        return $this->threadCode;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Msg
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set isOwn
     *
     * @param integer $isOwn
     *
     * @return Msg
     */
    public function setIsOwn($isOwn)
    {
        $this->isOwn = $isOwn;

        return $this;
    }

    /**
     * Get isOwn
     *
     * @return integer
     */
    public function getIsOwn()
    {
        return $this->isOwn;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Msg
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
     * @return Msg
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
     * @return Msg
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
        $arr['name'] = $this->getName();
        $arr['email'] = $this->getEmail();
        $arr['cc'] = $this->getCc();
        $arr['bcc'] = $this->getBcc();
        $arr['client_id'] = $this->getClientId();
        $arr['deal_id'] = $this->getDealId();
        $arr['owner_id'] = $this->getOwnerId();
        $arr['description'] = $this->getDescription();
        $arr['status'] = $this->getStatus();
        $arr['created_at'] = $this->getCreatedAt();
        $arr['updated_at'] = $this->getUpdatedAt();

        return $arr;
    }
        
    /**
     *
     * @return type
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     *
     * @param type $source
     * @return \AppBundle\Entity\Source
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }
}
