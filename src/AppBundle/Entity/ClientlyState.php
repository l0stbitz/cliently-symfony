<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientlyState
 *
 * @ORM\Table(name="cliently_state", indexes={@ORM\Index(name="state_slug", columns={"state_slug"}), @ORM\Index(name="state_admin1", columns={"state_minislug"})})
 * @ORM\Entity
 */
class ClientlyState
{
    /**
     * @var integer
     *
     * @ORM\Column(name="state_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $stateId;

    /**
     * @var string
     *
     * @ORM\Column(name="state_name", type="string", length=200, nullable=false)
     */
    private $stateName;

    /**
     * @var string
     *
     * @ORM\Column(name="state_slug", type="string", length=50, nullable=false)
     */
    private $stateSlug;

    /**
     * @var string
     *
     * @ORM\Column(name="state_minislug", type="string", length=2, nullable=false)
     */
    private $stateMinislug;

    /**
     * @var integer
     *
     * @ORM\Column(name="state_t_synced", type="integer", nullable=false)
     */
    private $stateTSynced;



    /**
     * Get stateId
     *
     * @return integer
     */
    public function getStateId()
    {
        return $this->stateId;
    }

    /**
     * Set stateName
     *
     * @param string $stateName
     *
     * @return ClientlyState
     */
    public function setStateName($stateName)
    {
        $this->stateName = $stateName;

        return $this;
    }

    /**
     * Get stateName
     *
     * @return string
     */
    public function getStateName()
    {
        return $this->stateName;
    }

    /**
     * Set stateSlug
     *
     * @param string $stateSlug
     *
     * @return ClientlyState
     */
    public function setStateSlug($stateSlug)
    {
        $this->stateSlug = $stateSlug;

        return $this;
    }

    /**
     * Get stateSlug
     *
     * @return string
     */
    public function getStateSlug()
    {
        return $this->stateSlug;
    }

    /**
     * Set stateMinislug
     *
     * @param string $stateMinislug
     *
     * @return ClientlyState
     */
    public function setStateMinislug($stateMinislug)
    {
        $this->stateMinislug = $stateMinislug;

        return $this;
    }

    /**
     * Get stateMinislug
     *
     * @return string
     */
    public function getStateMinislug()
    {
        return $this->stateMinislug;
    }

    /**
     * Set stateTSynced
     *
     * @param integer $stateTSynced
     *
     * @return ClientlyState
     */
    public function setStateTSynced($stateTSynced)
    {
        $this->stateTSynced = $stateTSynced;

        return $this;
    }

    /**
     * Get stateTSynced
     *
     * @return integer
     */
    public function getStateTSynced()
    {
        return $this->stateTSynced;
    }
}
