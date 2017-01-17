<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Node
 *
 * @ORM\Table(name="node")
 * @ORM\Entity
 */
class Node
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
     * @ORM\Column(name="group", type="integer", nullable=false)
     */
    private $group;

    /**
     * @var integer
     *
     * @ORM\Column(name="subgroup", type="integer", nullable=false)
     */
    private $subgroup;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=100, nullable=false)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="extra", type="string", length=100, nullable=false)
     */
    private $extra;

    /**
     * @var integer
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=false)
     */
    private $parentId;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_last", type="integer", nullable=false)
     */
    private $isLast;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=100, nullable=false)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="alphapath", type="string", length=200, nullable=false)
     */
    private $alphapath;

    /**
     * @var integer
     *
     * @ORM\Column(name="path_position", type="integer", nullable=false)
     */
    private $pathPosition;

    /**
     * @var integer
     *
     * @ORM\Column(name="level", type="integer", nullable=false)
     */
    private $level;

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
     * Set group
     *
     * @param integer $group
     *
     * @return Node
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return integer
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set subgroup
     *
     * @param integer $subgroup
     *
     * @return Node
     */
    public function setSubgroup($subgroup)
    {
        $this->subgroup = $subgroup;

        return $this;
    }

    /**
     * Get subgroup
     *
     * @return integer
     */
    public function getSubgroup()
    {
        return $this->subgroup;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Node
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
     * Set name
     *
     * @param string $name
     *
     * @return Node
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
     * Set extra
     *
     * @param string $extra
     *
     * @return Node
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
     * Set parentId
     *
     * @param integer $parentId
     *
     * @return Node
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId
     *
     * @return integer
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set isLast
     *
     * @param integer $isLast
     *
     * @return Node
     */
    public function setIsLast($isLast)
    {
        $this->isLast = $isLast;

        return $this;
    }

    /**
     * Get isLast
     *
     * @return integer
     */
    public function getIsLast()
    {
        return $this->isLast;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return Node
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set alphapath
     *
     * @param string $alphapath
     *
     * @return Node
     */
    public function setAlphapath($alphapath)
    {
        $this->alphapath = $alphapath;

        return $this;
    }

    /**
     * Get alphapath
     *
     * @return string
     */
    public function getAlphapath()
    {
        return $this->alphapath;
    }

    /**
     * Set pathPosition
     *
     * @param integer $pathPosition
     *
     * @return Node
     */
    public function setPathPosition($pathPosition)
    {
        $this->pathPosition = $pathPosition;

        return $this;
    }

    /**
     * Get pathPosition
     *
     * @return integer
     */
    public function getPathPosition()
    {
        return $this->pathPosition;
    }

    /**
     * Set level
     *
     * @param integer $level
     *
     * @return Node
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set isEnabled
     *
     * @param integer $isEnabled
     *
     * @return Node
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
