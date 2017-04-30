<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="binder")
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\MaterializedPathRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\Tree(type="materializedPath")
 * @Gedmo\Loggable
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
     * @Gedmo\Versioned
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
    private $createdAt;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Gedmo\Blameable(on="create")
     * @var string
     */
    private $createdBy;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Gedmo\Blameable(on="update")
     * @var string
     */
    private $updatedBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $deletedAt;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var \DateTime
     */
    private $deletedBy;

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

    public function getLevel()
    {
        return $this->level;
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
}
