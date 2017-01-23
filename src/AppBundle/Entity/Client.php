<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Client
 *
 * @ORM\Table(name="client", 
 *      indexes={@ORM\Index(name="client_owner", columns={"owner_id"}), 
 *               @ORM\Index(name="client_status", columns={"is_enabled"})})
 * @ORM\Entity
 */
class Client
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name = '';

    /**
     * @var string
     *
     * @ORM\Column(name="avatar", type="string", length=20, nullable=false)
     */
    private $avatar = '';

    /**
     * @var string
     *
     * @ORM\Column(name="occupation", type="string", length=100, nullable=false)
     */
    private $occupation = '';

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=false)
     */
    private $description = '';

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=false)
     */
    private $email = '';

    /**
     * @var string
     *
     * @ORM\Column(name="address_line1", type="string", length=100, nullable=false)
     */
    private $addressLine1 = '';

    /**
     * @var string
     *
     * @ORM\Column(name="address_line2", type="string", length=50, nullable=false)
     */
    private $addressLine2 = '';

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=50, nullable=false)
     */
    private $city = '';

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=50, nullable=false)
     */
    private $state = '';

    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=50, nullable=false)
     */
    private $zip = '';

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=50, nullable=false)
     */
    private $country = '';

    /**
     * @var string
     *
     * @ORM\Column(name="coords", type="string", length=100, nullable=false)
     */
    private $coords = '';

    /**
     * @var string
     *
     * @ORM\Column(name="google_location", type="text", length=65535, nullable=false)
     */
    private $googleLocation = '';

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=100, nullable=false)
     */
    private $phone = '';

    /**
     * @var string
     *
     * @ORM\Column(name="social", type="text", length=65535, nullable=false)
     */
    private $social = '{}';

    /**
     * @var string
     *
     * @ORM\Column(name="contacts", type="text", length=65535, nullable=false)
     */
    private $contacts = '';

    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float", precision=10, scale=0, nullable=false)
     */
    private $value = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="new_events_count", type="integer", nullable=false)
     */
    private $newEventsCount = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="company_id", type="integer", nullable=false)
     */
    private $companyId = 0;

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
     * @ORM\Column(name="source_id", type="integer", nullable=true)
     */
    private $sourceId = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_verified", type="boolean", nullable=false)
     */
    private $isVerified = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_enabled", type="integer", nullable=false)
     */
    private $isEnabled = false;

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
     * @ORM\OneToMany(targetEntity="Msg", mappedBy="deal")
     */
    private $mails = [];

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Workspace", inversedBy="clients")
     * @ORM\JoinColumn(name="workspace_id", referencedColumnName="id")
     */
    private $workspace;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="clients")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Source", inversedBy="clients")
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
     * @return Client
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
     * Set avatar
     *
     * @param string $avatar
     *
     * @return Client
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set occupation
     *
     * @param string $occupation
     *
     * @return Client
     */
    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;

        return $this;
    }

    /**
     * Get occupation
     *
     * @return string
     */
    public function getOccupation()
    {
        return $this->occupation;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Client
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
     * Set email
     *
     * @param string $email
     *
     * @return Client
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
     * Set addressLine1
     *
     * @param string $addressLine1
     *
     * @return Client
     */
    public function setAddressLine1($addressLine1)
    {
        $this->addressLine1 = $addressLine1;

        return $this;
    }

    /**
     * Get addressLine1
     *
     * @return string
     */
    public function getAddressLine1()
    {
        return $this->addressLine1;
    }

    /**
     * Set addressLine2
     *
     * @param string $addressLine2
     *
     * @return Client
     */
    public function setAddressLine2($addressLine2)
    {
        $this->addressLine2 = $addressLine2;

        return $this;
    }

    /**
     * Get addressLine2
     *
     * @return string
     */
    public function getAddressLine2()
    {
        return $this->addressLine2;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Client
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return Client
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set zip
     *
     * @param string $zip
     *
     * @return Client
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Client
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set coords
     *
     * @param string $coords
     *
     * @return Client
     */
    public function setCoords($coords)
    {
        $this->coords = $coords;

        return $this;
    }

    /**
     * Get coords
     *
     * @return string
     */
    public function getCoords()
    {
        return $this->coords;
    }

    /**
     * Set googleLocation
     *
     * @param string $googleLocation
     *
     * @return Client
     */
    public function setGoogleLocation($googleLocation)
    {
        $this->googleLocation = $googleLocation;

        return $this;
    }

    /**
     * Get googleLocation
     *
     * @return string
     */
    public function getGoogleLocation()
    {
        return $this->googleLocation;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Client
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set social
     *
     * @param string $social
     *
     * @return Client
     */
    public function setSocial($social)
    {
        $this->social = $social;

        return $this;
    }

    /**
     * Get social
     *
     * @return string
     */
    public function getSocial()
    {
        return $this->social;
    }

    /**
     * Set contacts
     *
     * @param string $contacts
     *
     * @return Client
     */
    public function setContacts($contacts)
    {
        $this->contacts = $contacts;

        return $this;
    }

    /**
     * Get contacts
     *
     * @return string
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Set value
     *
     * @param float $value
     *
     * @return Client
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set newEventsCount
     *
     * @param integer $newEventsCount
     *
     * @return Client
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
     * Set companyId
     *
     * @param integer $companyId
     *
     * @return Client
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;

        return $this;
    }

    /**
     * Get companyId
     *
     * @return integer
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * Set ownerId
     *
     * @param integer $ownerId
     *
     * @return Client
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
     * @return Client
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
     * Set sourceId
     *
     * @param integer $sourceId
     *
     * @return Client
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
     * Set isVerified
     *
     * @param boolean $isVerified
     *
     * @return Client
     */
    public function setIsVerified($isVerified)
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * Get isVerified
     *
     * @return boolean
     */
    public function getIsVerified()
    {
        return $this->isVerified;
    }

    /**
     * Set isEnabled
     *
     * @param integer $isEnabled
     *
     * @return Client
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
     * @return Client
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
     * @return Client
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
    public function getOwner()
    {
        return $this->user;
    }

    /**
     *
     * @param type $user
     * @return \AppBundle\Entity\User
     */
    public function setOwner($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     *
     * @return type
     */
    public function getWorkspace()
    {
        return $this->workspace;
    }

    /**
     *
     * @param type $workspace
     * @return \AppBundle\Entity\Workspace
     */
    public function setWorkspace($workspace)
    {
        $this->workspace = $workspace;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function toArray()
    {
        //"clients":[{"id":200,"name":"Test 1","occupation":"","email":"test@lostbitz.com","phone":"","is_verified":false,"source":null}],
        $arr = [];
        $arr['id'] = $this->getId();
        $arr['name'] = $this->getName();
        $arr['occupation'] = $this->getOccupation();
        $arr['email'] = $this->getEmail();
        $arr['phone'] = $this->getPhone();
        $arr['company_id'] = $this->getCompanyId();
        //$arr['source'] = null;
        $arr['contacts'] = [];
        $arr['integrations'] = $this->getIntegrationsArray();
        $arr['social'] = json_decode($this->getSocial());
        $arr['is_verified'] = false;

        //$arr['created_at'] = $this->getCreatedAt();

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

    /**
     *
     * @return type
     */
    public function getIntegrationsArray()
    {
        $s = $this->getSourceId();
        if ($s == 0) {
            return [];
        }
        $integrations = [];
        $extra = json_decode($this->getSource()->getExtra());
        //$integrations[Source::BY_ID[$this->getSource()->getType()]['class']] = $extra;
        $integrations['twitter'] = $extra;
        return $integrations;
    }

    /**
     *
     * @return type
     */
    public function getMailsArray()
    {
        $mails = [];
        foreach ($this->getMails() as $mail) {
            $mails[] = $mail->toArray();
        }
        return $mails;
    }

    /**
     *
     * @return type
     */
    public function getMails()
    {
        return $this->mails;
    }

    /**
     *
     * @param type $mails
     * @return \AppBundle\Entity\Msg
     */
    public function setMails($mails)
    {
        $this->mails = $mails;
        return $this;
    }
}
