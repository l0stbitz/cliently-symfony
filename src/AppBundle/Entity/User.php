<?php
namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user", indexes={@ORM\Index(name="account_status", columns={"status"})})
 * @ORM\Entity
 */
class User extends BaseUser
{

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_ACTIVE_NOCONFIRM = 2;
    const STATUS_SUSPENDED = 3;
    const STATUS_BLOCKED = 4;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=100, nullable=true)
     */
    private $firstName = '';

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=100, nullable=true)
     */
    private $lastName = '';

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=100, nullable=true)
     */
    private $location = '';

    /**
     * @var string
     *
     * @ORM\Column(name="coords", type="string", length=100, nullable=true)
     */
    private $coords = '';

    /**
     * @var string
     *
     * @ORM\Column(name="avatar", type="string", length=20, nullable=true)
     */
    private $avatar = '';

    /**
     * @var string
     *
     * @ORM\Column(name="company_name", type="string", length=100, nullable=true)
     */
    private $companyName;

    /**
     * @var string
     *
     * @ORM\Column(name="company_size", type="string", length=20, nullable=true)
     */
    private $companySize = '';

    /**
     * @var string
     *
     * @ORM\Column(name="company_logo", type="string", length=20, nullable=true)
     */
    private $companyLogo = '';

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=50, nullable=true)
     */
    private $phone = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="mail_accessed_at", type="integer", nullable=true)
     */
    private $mailAccessedAt = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="wizard", type="integer", nullable=true)
     */
    private $wizard = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="industries", type="text", length=65535, nullable=true)
     */
    private $industries = '[]';

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status = self::STATUS_INACTIVE;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_at", type="integer", nullable=true)
     */
    private $createdAt = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="updated_at", type="integer", nullable=true)
     */
    private $updatedAt = 0;

    /**
     *
     * @ORM\OneToMany(targetEntity="Client", mappedBy="user")
     */
    private $clients;

    /**
     *
     * @ORM\OneToMany(targetEntity="Workspace", mappedBy="user")
     */
    private $workspaces;

    /**
     *
     * @ORM\OneToMany(targetEntity="Workflow", mappedBy="user")
     */
    private $workflows;

    /**
     *
     * @ORM\OneToMany(targetEntity="WorkspaceMember", mappedBy="user")
     */
    private $workspaceMembers;

    /**
     *
     * @ORM\OneToMany(targetEntity="Account", mappedBy="user")
     */
    private $accounts;

    /**
     *
     * @ORM\OneToMany(targetEntity="Stage", mappedBy="user")
     */
    private $stages;

    /**
     *
     * @ORM\OneToMany(targetEntity="Deal", mappedBy="user")
     */
    private $deals;

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
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set location
     *
     * @param string $location
     *
     * @return User
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set coords
     *
     * @param string $coords
     *
     * @return User
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
     * Set avatar
     *
     * @param string $avatar
     *
     * @return User
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
     * Set companyName
     *
     * @param string $companyName
     *
     * @return User
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get companyName
     *
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * Set companySize
     *
     * @param string $companySize
     *
     * @return User
     */
    public function setCompanySize($companySize)
    {
        $this->companySize = $companySize;

        return $this;
    }

    /**
     * Get companySize
     *
     * @return string
     */
    public function getCompanySize()
    {
        return $this->companySize;
    }

    /**
     * Set companyLogo
     *
     * @param string $companyLogo
     *
     * @return User
     */
    public function setCompanyLogo($companyLogo)
    {
        $this->companyLogo = $companyLogo;

        return $this;
    }

    /**
     * Get companyLogo
     *
     * @return string
     */
    public function getCompanyLogo()
    {
        return $this->companyLogo;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return User
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
     * Set mailAccessedAt
     *
     * @param integer $mailAccessedAt
     *
     * @return User
     */
    public function setMailAccessedAt($mailAccessedAt)
    {
        $this->mailAccessedAt = $mailAccessedAt;

        return $this;
    }

    /**
     * Get mailAccessedAt
     *
     * @return integer
     */
    public function getMailAccessedAt()
    {
        return $this->mailAccessedAt;
    }

    /**
     * Set wizard
     *
     * @param integer $wizard
     *
     * @return User
     */
    public function setWizard($wizard)
    {
        $this->wizard = $wizard;

        return $this;
    }

    /**
     * Get wizard
     *
     * @return integer
     */
    public function getWizard()
    {
        return $this->wizard;
    }

    /**
     * Set industries
     *
     * @param string $industries
     *
     * @return User
     */
    public function setIndustries($industries)
    {
        $this->industries = $industries;

        return $this;
    }

    /**
     * Get industries
     *
     * @return string
     */
    public function getIndustries()
    {
        return $this->industries;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return User
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
     * @return User
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
     * @return User
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
        $arr['avatar'] = $this->getAvatar();
        $arr['email'] = $this->getEmail();
        $arr['first_name'] = $this->getFirstName();
        $arr['last_name'] = $this->getLastName();
        $arr['phone'] = $this->getPhone();
        $arr['company_name'] = $this->getCompanyName();
        $arr['company_logo'] = $this->getCompanyLogo();
        $arr['location'] = $this->getLocation();
        $arr['coords'] = $this->getCoords();
        $arr['wizard'] = $this->getWizard();
        $arr['industries'] = ["1"];
        $arr['integrations'] = [];
        $arr['integration_avatar'] = '';
        $arr['integration_type'] = '';
        $arr['accounts'] = [];
        $arr['workspaces'] = [];
        return $arr;
    }

    /**
     *
     * @return type
     */
    public function getWorkspacesArray()
    {
        $workspaces = [];
        foreach ($this->getWorkspaces() as $workspace) {
            $workspaces[] = $workspace->toArray();
        }
        return $workspaces;
    }

    /**
     *
     * @return type
     */
    public function getWorkspaces()
    {
        return $this->workspaces;
    }

    /**
     *
     * @param type $workspaces
     * @return \AppBundle\Entity\Workspace
     */
    public function setWorkspaces($workspaces)
    {
        $this->workspaces = $workspaces;
        return $this;
    }

    /**
     *
     * @return type
     */
    public function getAccounts()
    {
        return $this->accounts;
    }

    /**
     *
     * @param type $accounts
     * @return \AppBundle\Entity\Account
     */
    public function setAccounts($accounts)
    {
        $this->accounts = $accounts;
        return $this;
    }
}
