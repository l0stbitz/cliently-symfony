<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Source
 *
 * @ORM\Table(name="source", uniqueConstraints={@ORM\UniqueConstraint(name="_key", columns={"code", "type"})})
 * @ORM\Entity
 */
class Source
{

    const TYPE_TWITTER_USER = 1;
    const TYPE_TWITTER_MSG = 2;
    const TYPE_ZOOMINFO_PERSON = 3;
    const TYPE_ZOOMINFO_COMPANY = 4;
    const TYPE_ZOOMINFO_COMPANY_NOID = 5;
    const BY_ID = [
        1 => ['id' => 1, 'class' => 'twitter_user'],
        2 => ['id' => 2, 'class' => 'twitter_tweet'],
        3 => ['id' => 3, 'class' => 'dbperson_user'],
        4 => ['id' => 4, 'class' => 'dbperson_company'],
        5 => ['id' => 5, 'class' => 'dbperson_company'],
    ];
    const BY_CLASS = [
        'twitter_user' => ['id' => 1, 'class' => 'twitter_user'],
        'twitter_tweet' => ['id' => 2, 'class' => 'twitter_tweet'],
        'dbperson_user' => ['id' => 3, 'class' => 'dbperson_user'],
        'dbperson_company' => ['id' => 4, 'class' => 'dbperson_company'],
    ];

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
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="bigint", nullable=false)
     */
    private $code = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="extra", type="text", length=65535, nullable=false)
     */
    private $extra = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="is_enabled", type="integer", nullable=false)
     */
    private $isEnabled = 0;

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
     *
     * @ORM\OneToMany(targetEntity="Integration", mappedBy="source")
     */
    private $integrations;

    /**
     *
     * @ORM\OneToMany(targetEntity="ClientTwitter", mappedBy="source")
     */
    private $clientTwitters;

    /**
     *
     * @ORM\OneToMany(targetEntity="Client", mappedBy="source")
     */
    private $clients;

    /**
     *
     * @ORM\OneToMany(targetEntity="Company", mappedBy="source")
     */
    private $companies;

    /**
     *
     * @ORM\OneToMany(targetEntity="Deal", mappedBy="source")
     */
    private $deals;

    /**
     *
     * @ORM\OneToMany(targetEntity="Lead", mappedBy="source")
     */
    private $leads;

    /**
     *
     * @ORM\OneToMany(targetEntity="Msg", mappedBy="source")
     */
    private $msgs;

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
     * Set type
     *
     * @param integer $type
     *
     * @return Source
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
     * Set code
     *
     * @param integer $code
     *
     * @return Source
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set extra
     *
     * @param string $extra
     *
     * @return Source
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * Get extra
     *
     * @return string
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * Set isEnabled
     *
     * @param integer $isEnabled
     *
     * @return Source
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * Get isEnabled
     *
     * @return integer
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Set createdAt
     *
     * @param integer $createdAt
     *
     * @return Source
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
     * @return Source
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
     * @return array
     */
    public function toArray()
    {
        $arr = [];
        $arr['id'] = $this->getId();
        $arr['code'] = json_decode($this->getCode());
        $arr['type'] = $this->getType();
        $arr['extra'] = json_decode($this->getExtra());
        //$arr['avatar'] = $this->getAvatar();
        //$arr['handle'] = $this->getHandle();
        //$arr['user_id'] = $this->getUser()->getId();
        //$arr['source'] = $this->getSource()->toArray();
        //$arr['values'] = json_decode($this->getValues());
        //$arr['is_primary'] = $this->getIsPrimary();
        $arr['is_enabled'] = $this->getIsEnabled();
        $arr['created_at'] = $this->getCreatedAt();
        $arr['updated_at'] = $this->getUpdatedAt();
        return $arr;
    }
}
