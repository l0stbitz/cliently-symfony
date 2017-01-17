<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Country
 *
 * @ORM\Table(name="country", uniqueConstraints={@ORM\UniqueConstraint(name="iso", columns={"iso"}), @ORM\UniqueConstraint(name="name", columns={"name"})})
 * @ORM\Entity
 */
class Country
{
    /**
     * @var integer
     *
     * @ORM\Column(name="geoname_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $geonameId;

    /**
     * @var string
     *
     * @ORM\Column(name="iso", type="string", length=2, nullable=false)
     */
    private $iso;

    /**
     * @var string
     *
     * @ORM\Column(name="iso3", type="string", length=3, nullable=false)
     */
    private $iso3;

    /**
     * @var integer
     *
     * @ORM\Column(name="iso_numeric", type="integer", nullable=false)
     */
    private $isoNumeric;

    /**
     * @var string
     *
     * @ORM\Column(name="fips", type="string", length=2, nullable=false)
     */
    private $fips;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="capital", type="string", length=50, nullable=false)
     */
    private $capital;

    /**
     * @var integer
     *
     * @ORM\Column(name="area", type="integer", nullable=false)
     */
    private $area;

    /**
     * @var integer
     *
     * @ORM\Column(name="population", type="integer", nullable=false)
     */
    private $population;

    /**
     * @var string
     *
     * @ORM\Column(name="continent", type="string", length=2, nullable=false)
     */
    private $continent;

    /**
     * @var string
     *
     * @ORM\Column(name="tld", type="string", length=3, nullable=false)
     */
    private $tld;

    /**
     * @var string
     *
     * @ORM\Column(name="currency_code", type="string", length=3, nullable=false)
     */
    private $currencyCode;

    /**
     * @var string
     *
     * @ORM\Column(name="currency_name", type="string", length=50, nullable=false)
     */
    private $currencyName;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=50, nullable=false)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="postal_code_format", type="string", length=100, nullable=false)
     */
    private $postalCodeFormat;

    /**
     * @var string
     *
     * @ORM\Column(name="postal_code_regex", type="string", length=150, nullable=false)
     */
    private $postalCodeRegex;

    /**
     * @var string
     *
     * @ORM\Column(name="languages", type="string", length=100, nullable=false)
     */
    private $languages;

    /**
     * @var string
     *
     * @ORM\Column(name="neighbours", type="string", length=50, nullable=false)
     */
    private $neighbours;

    /**
     * @var string
     *
     * @ORM\Column(name="equivalent_fips_code", type="string", length=2, nullable=false)
     */
    private $equivalentFipsCode;



    /**
     * Get geonameId
     *
     * @return integer
     */
    public function getGeonameId()
    {
        return $this->geonameId;
    }

    /**
     * Set iso
     *
     * @param string $iso
     *
     * @return Country
     */
    public function setIso($iso)
    {
        $this->iso = $iso;

        return $this;
    }

    /**
     * Get iso
     *
     * @return string
     */
    public function getIso()
    {
        return $this->iso;
    }

    /**
     * Set iso3
     *
     * @param string $iso3
     *
     * @return Country
     */
    public function setIso3($iso3)
    {
        $this->iso3 = $iso3;

        return $this;
    }

    /**
     * Get iso3
     *
     * @return string
     */
    public function getIso3()
    {
        return $this->iso3;
    }

    /**
     * Set isoNumeric
     *
     * @param integer $isoNumeric
     *
     * @return Country
     */
    public function setIsoNumeric($isoNumeric)
    {
        $this->isoNumeric = $isoNumeric;

        return $this;
    }

    /**
     * Get isoNumeric
     *
     * @return integer
     */
    public function getIsoNumeric()
    {
        return $this->isoNumeric;
    }

    /**
     * Set fips
     *
     * @param string $fips
     *
     * @return Country
     */
    public function setFips($fips)
    {
        $this->fips = $fips;

        return $this;
    }

    /**
     * Get fips
     *
     * @return string
     */
    public function getFips()
    {
        return $this->fips;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Country
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
     * Set capital
     *
     * @param string $capital
     *
     * @return Country
     */
    public function setCapital($capital)
    {
        $this->capital = $capital;

        return $this;
    }

    /**
     * Get capital
     *
     * @return string
     */
    public function getCapital()
    {
        return $this->capital;
    }

    /**
     * Set area
     *
     * @param integer $area
     *
     * @return Country
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return integer
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set population
     *
     * @param integer $population
     *
     * @return Country
     */
    public function setPopulation($population)
    {
        $this->population = $population;

        return $this;
    }

    /**
     * Get population
     *
     * @return integer
     */
    public function getPopulation()
    {
        return $this->population;
    }

    /**
     * Set continent
     *
     * @param string $continent
     *
     * @return Country
     */
    public function setContinent($continent)
    {
        $this->continent = $continent;

        return $this;
    }

    /**
     * Get continent
     *
     * @return string
     */
    public function getContinent()
    {
        return $this->continent;
    }

    /**
     * Set tld
     *
     * @param string $tld
     *
     * @return Country
     */
    public function setTld($tld)
    {
        $this->tld = $tld;

        return $this;
    }

    /**
     * Get tld
     *
     * @return string
     */
    public function getTld()
    {
        return $this->tld;
    }

    /**
     * Set currencyCode
     *
     * @param string $currencyCode
     *
     * @return Country
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    /**
     * Get currencyCode
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * Set currencyName
     *
     * @param string $currencyName
     *
     * @return Country
     */
    public function setCurrencyName($currencyName)
    {
        $this->currencyName = $currencyName;

        return $this;
    }

    /**
     * Get currencyName
     *
     * @return string
     */
    public function getCurrencyName()
    {
        return $this->currencyName;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Country
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
     * Set postalCodeFormat
     *
     * @param string $postalCodeFormat
     *
     * @return Country
     */
    public function setPostalCodeFormat($postalCodeFormat)
    {
        $this->postalCodeFormat = $postalCodeFormat;

        return $this;
    }

    /**
     * Get postalCodeFormat
     *
     * @return string
     */
    public function getPostalCodeFormat()
    {
        return $this->postalCodeFormat;
    }

    /**
     * Set postalCodeRegex
     *
     * @param string $postalCodeRegex
     *
     * @return Country
     */
    public function setPostalCodeRegex($postalCodeRegex)
    {
        $this->postalCodeRegex = $postalCodeRegex;

        return $this;
    }

    /**
     * Get postalCodeRegex
     *
     * @return string
     */
    public function getPostalCodeRegex()
    {
        return $this->postalCodeRegex;
    }

    /**
     * Set languages
     *
     * @param string $languages
     *
     * @return Country
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;

        return $this;
    }

    /**
     * Get languages
     *
     * @return string
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Set neighbours
     *
     * @param string $neighbours
     *
     * @return Country
     */
    public function setNeighbours($neighbours)
    {
        $this->neighbours = $neighbours;

        return $this;
    }

    /**
     * Get neighbours
     *
     * @return string
     */
    public function getNeighbours()
    {
        return $this->neighbours;
    }

    /**
     * Set equivalentFipsCode
     *
     * @param string $equivalentFipsCode
     *
     * @return Country
     */
    public function setEquivalentFipsCode($equivalentFipsCode)
    {
        $this->equivalentFipsCode = $equivalentFipsCode;

        return $this;
    }

    /**
     * Get equivalentFipsCode
     *
     * @return string
     */
    public function getEquivalentFipsCode()
    {
        return $this->equivalentFipsCode;
    }
}
