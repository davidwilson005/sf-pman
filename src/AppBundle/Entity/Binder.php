<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
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
     * @Assert\NotBlank()
     * @Gedmo\Versioned
     * @var string
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Facility", inversedBy="binders", fetch="EAGER")
     * @var ArrayCollection
     */
    private $facilities;

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

    public function __construct()
    {
        $this->facilities = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getFacilities()
    {
        return $this->facilities;
    }

    /**
     * @return string
     */
    public function getName()
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

    public function addFacility(Facility $facility)
    {
        if ($this->facilities->contains($facility)) {
            return;
        }

        $this->facilities[] = $facility;
    }
}
