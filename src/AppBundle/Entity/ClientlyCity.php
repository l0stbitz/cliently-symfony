<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientlyCity
 *
 * @ORM\Table(name="cliently_city", indexes={@ORM\Index(name="city_slug", columns={"city_slug"}), @ORM\Index(name="city_admin1", columns={"state_slug"})})
 * @ORM\Entity
 */
class ClientlyCity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="city_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $cityId;

    /**
     * @var string
     *
     * @ORM\Column(name="city_name", type="string", length=200, nullable=false)
     */
    private $cityName;

    /**
     * @var string
     *
     * @ORM\Column(name="city_slug", type="string", length=200, nullable=false)
     */
    private $citySlug;

    /**
     * @var float
     *
     * @ORM\Column(name="city_latitude", type="float", precision=10, scale=0, nullable=false)
     */
    private $cityLatitude;

    /**
     * @var float
     *
     * @ORM\Column(name="city_longitude", type="float", precision=10, scale=0, nullable=false)
     */
    private $cityLongitude;

    /**
     * @var integer
     *
     * @ORM\Column(name="city_t_synced", type="integer", nullable=false)
     */
    private $cityTSynced;

    /**
     * @var string
     *
     * @ORM\Column(name="state_slug", type="string", length=50, nullable=false)
     */
    private $stateSlug;

    /**
     * @var integer
     *
     * @ORM\Column(name="state_id", type="integer", nullable=false)
     */
    private $stateId;



    /**
     * Get cityId
     *
     * @return integer
     */
    public function getCityId()
    {
        return $this->cityId;
    }

    /**
     * Set cityName
     *
     * @param string $cityName
     *
     * @return ClientlyCity
     */
    public function setCityName($cityName)
    {
        $this->cityName = $cityName;

        return $this;
    }

    /**
     * Get cityName
     *
     * @return string
     */
    public function getCityName()
    {
        return $this->cityName;
    }

    /**
     * Set citySlug
     *
     * @param string $citySlug
     *
     * @return ClientlyCity
     */
    public function setCitySlug($citySlug)
    {
        $this->citySlug = $citySlug;

        return $this;
    }

    /**
     * Get citySlug
     *
     * @return string
     */
    public function getCitySlug()
    {
        return $this->citySlug;
    }

    /**
     * Set cityLatitude
     *
     * @param float $cityLatitude
     *
     * @return ClientlyCity
     */
    public function setCityLatitude($cityLatitude)
    {
        $this->cityLatitude = $cityLatitude;

        return $this;
    }

    /**
     * Get cityLatitude
     *
     * @return float
     */
    public function getCityLatitude()
    {
        return $this->cityLatitude;
    }

    /**
     * Set cityLongitude
     *
     * @param float $cityLongitude
     *
     * @return ClientlyCity
     */
    public function setCityLongitude($cityLongitude)
    {
        $this->cityLongitude = $cityLongitude;

        return $this;
    }

    /**
     * Get cityLongitude
     *
     * @return float
     */
    public function getCityLongitude()
    {
        return $this->cityLongitude;
    }

    /**
     * Set cityTSynced
     *
     * @param integer $cityTSynced
     *
     * @return ClientlyCity
     */
    public function setCityTSynced($cityTSynced)
    {
        $this->cityTSynced = $cityTSynced;

        return $this;
    }

    /**
     * Get cityTSynced
     *
     * @return integer
     */
    public function getCityTSynced()
    {
        return $this->cityTSynced;
    }

    /**
     * Set stateSlug
     *
     * @param string $stateSlug
     *
     * @return ClientlyCity
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
     * Set stateId
     *
     * @param integer $stateId
     *
     * @return ClientlyCity
     */
    public function setStateId($stateId)
    {
        $this->stateId = $stateId;

        return $this;
    }

    /**
     * Get stateId
     *
     * @return integer
     */
    public function getStateId()
    {
        return $this->stateId;
    }
}
