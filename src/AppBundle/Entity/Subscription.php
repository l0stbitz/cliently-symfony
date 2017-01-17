<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Subscription
 *
 * @ORM\Table(name="subscription")
 * @ORM\Entity
 */
class Subscription
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
     * @ORM\Column(name="class", type="string", length=50, nullable=false)
     */
    private $class;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="users", type="integer", nullable=false)
     */
    private $users;

    /**
     * @var integer
     *
     * @ORM\Column(name="workspace_limit", type="integer", nullable=false)
     */
    private $workspaceLimit;

    /**
     * @var integer
     *
     * @ORM\Column(name="pipeline_limit", type="integer", nullable=false)
     */
    private $pipelineLimit;

    /**
     * @var integer
     *
     * @ORM\Column(name="deals", type="integer", nullable=false)
     */
    private $deals;

    /**
     * @var integer
     *
     * @ORM\Column(name="workflows", type="integer", nullable=false)
     */
    private $workflows;

    /**
     * @var integer
     *
     * @ORM\Column(name="sources", type="integer", nullable=false)
     */
    private $sources;

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
     * @return Subscription
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
     * @return Subscription
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
     * Set users
     *
     * @param integer $users
     *
     * @return Subscription
     */
    public function setUsers($users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Get users
     *
     * @return integer
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set workspaceLimit
     *
     * @param integer $workspaceLimit
     *
     * @return Subscription
     */
    public function setWorkspaceLimit($workspaceLimit)
    {
        $this->workspaceLimit = $workspaceLimit;

        return $this;
    }

    /**
     * Get workspaceLimit
     *
     * @return integer
     */
    public function getWorkspaceLimit()
    {
        return $this->workspaceLimit;
    }

    /**
     * Set pipelineLimit
     *
     * @param integer $pipelineLimit
     *
     * @return Subscription
     */
    public function setPipelineLimit($pipelineLimit)
    {
        $this->pipelineLimit = $pipelineLimit;

        return $this;
    }

    /**
     * Get pipelineLimit
     *
     * @return integer
     */
    public function getPipelineLimit()
    {
        return $this->pipelineLimit;
    }

    /**
     * Set deals
     *
     * @param integer $deals
     *
     * @return Subscription
     */
    public function setDeals($deals)
    {
        $this->deals = $deals;

        return $this;
    }

    /**
     * Get deals
     *
     * @return integer
     */
    public function getDeals()
    {
        return $this->deals;
    }

    /**
     * Set workflows
     *
     * @param integer $workflows
     *
     * @return Subscription
     */
    public function setWorkflows($workflows)
    {
        $this->workflows = $workflows;

        return $this;
    }

    /**
     * Get workflows
     *
     * @return integer
     */
    public function getWorkflows()
    {
        return $this->workflows;
    }

    /**
     * Set sources
     *
     * @param integer $sources
     *
     * @return Subscription
     */
    public function setSources($sources)
    {
        $this->sources = $sources;

        return $this;
    }

    /**
     * Get sources
     *
     * @return integer
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * Set isEnabled
     *
     * @param integer $isEnabled
     *
     * @return Subscription
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
