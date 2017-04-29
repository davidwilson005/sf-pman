<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="binder")
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\MaterializedPathRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedOn", timeAware=false)
 * @Gedmo\Tree(type="materializedPath")
 */
class Binder
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @Gedmo\TreePathSource
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Gedmo\TreePath(separator="/", startsWithSeparator=false, endsWithSeparator=false)
     * @var string
     */
    private $path;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Binder", inversedBy="children")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     * @Gedmo\TreeParent
     * @var Binder
     */
    private $parent;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Gedmo\TreeLevel
     * @var int
     */
    private $level;

    /**
     * @ORM\OneToMany(targetEntity="Binder", mappedBy="parent")
     * @var Collection
     */
    private $children;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @var \DateTime
     */
    private $createdOn;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     * @var \DateTime
     */
    private $updatedOn;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $deletedOn;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


    /**
     * @param  string $name
     * @return Binder
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function setParent(Binder $parent = null)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function getChildren()
    {
        return $this->children;
    }

}
