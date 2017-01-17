<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Plan
 *
 * @ORM\Table(name="plan")
 * @ORM\Entity
 */
class Plan
{

    const BY_ID = [
        1 => ['id' => 1, 'class' => 'free'],
        2 => ['id' => 2, 'class' => 'pro_annual'],
        3 => ['id' => 3, 'class' => 'pro_monthly'],
        4 => ['id' => 4, 'class' => 'business_annual'],
        5 => ['id' => 5, 'class' => 'business_monthly'],
        6 => ['id' => 6, 'class' => 'enterprise_annual'],
        7 => ['id' => 7, 'class' => 'enterprise_monthly'],
    ];
    const BY_CLASS = [
        'free' => ['id' => 1, 'class' => 'free'],
        'pro_annual' => ['id' => 2, 'class' => 'pro_annual'],
        'pro_monthly' => ['id' => 3, 'class' => 'pro_monthly'],
        'business_annual' => ['id' => 4, 'class' => 'business_annual'],
        'business_monthly' => ['id' => 5, 'class' => 'business_monthly'],
        'enterprise_annual' => ['id' => 6, 'class' => 'enterprise_annual'],
        'enterprise_monthly' => ['id' => 7, 'class' => 'enterprise_monthly'],
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
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=20, nullable=false)
     */
    private $class;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=20, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="subscription_id", type="integer", nullable=false)
     */
    private $subscriptionId;

    /**
     * @var integer
     *
     * @ORM\Column(name="period", type="integer", nullable=false)
     */
    private $period;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", precision=10, scale=0, nullable=false)
     */
    private $price;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_enabled", type="integer", nullable=false)
     */
    private $isEnabled;

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
     * Set class
     *
     * @param string $class
     *
     * @return Plan
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Plan
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
     * Set subscriptionId
     *
     * @param integer $subscriptionId
     *
     * @return Plan
     */
    public function setSubscriptionId($subscriptionId)
    {
        $this->subscriptionId = $subscriptionId;

        return $this;
    }

    /**
     * Get subscriptionId
     *
     * @return integer
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }

    /**
     * Set period
     *
     * @param integer $period
     *
     * @return Plan
     */
    public function setPeriod($period)
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Get period
     *
     * @return integer
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return Plan
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set isEnabled
     *
     * @param integer $isEnabled
     *
     * @return Plan
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
}
