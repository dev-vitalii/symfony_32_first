<?php


namespace AppBundle\Entity;

use AppBundle\Repository\GenusRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GenusRepository")
 * @ORM\Table(name="genus")
 */
class Genus
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SubFamily")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $subFamily;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Range(min=0, minMessage="Negative species! Come on...")
     */
    private $speciesCount;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $funFact;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished = true;

    /**
     * @ORM\Column(type="date", nullable=true, options={"default" : "1970-01-01"})
     * @Assert\NotBlank()
     */
    private $firstDiscoveredAt;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Gedmo\Slug(fields={"name"})
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="GenusNote", mappedBy="genus")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $notes;

    /**
     * @XXORM\ManyToMany(targetEntity="AppBundle\Entity\User", inversedBy="studiedGenuses", fetch="EXTRA_LAZY")
     * @XXORM\JoinTable(name="genus_scientist")
     */
    /**
     * @ORM\OneToMany(targetEntity="GenusScientist",
     *      mappedBy="genus",
     *      fetch="EXTRA_LAZY",
     *      orphanRemoval=true,
     *      cascade={"persist"}
     * )
     * @Assert\Valid()
     */
    private $genusScientists;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
        $this->genusScientists = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }

    /**
     * @param mixed $isPublished
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return SubFamily
     */
    public function getSubFamily()
    {
        return $this->subFamily;
    }

    /**
     * @param SubFamily $subFamily
     */
    public function setSubFamily(SubFamily $subFamily)
    {
        $this->subFamily = $subFamily;
    }

    /**
     * @return mixed
     */
    public function getSpeciesCount()
    {
        return $this->speciesCount;
    }

    /**
     * @param mixed $speciesCount
     */
    public function setSpeciesCount($speciesCount)
    {
        $this->speciesCount = $speciesCount;
    }

    /**
     * @return mixed
     */
    public function getFunFact()
    {
        return $this->funFact;
    }

    /**
     * @param mixed $funFact
     */
    public function setFunFact($funFact)
    {
        $this->funFact = $funFact;
    }

    public function getUpdatedAt()
    {
        return new \DateTime('-'.rand(0, 100).' days');
    }

    /**
     * @return mixed
     */
    public function getFirstDiscoveredAt()
    {
        return $this->firstDiscoveredAt;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @param mixed $firstDiscoveredAt
     */
    public function setFirstDiscoveredAt($firstDiscoveredAt)
    {
        $this->firstDiscoveredAt = $firstDiscoveredAt;
    }

    /**
     * @return ArrayCollection|GenusNote[]
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @return ArrayCollection|GenusScientist[]
     */
    public function getGenusScientists()
    {
        return $this->genusScientists;
    }

//    /**
//     * @param ArrayCollection|User[]
//     */
//    public function setGenusScientists($genusScientists)
//    {
//        $this->genusScientists = $genusScientists;
//    }

//    /**
//     * @param User
//     */
//    public function addGenusScientist(User $user)
//    {
//        if ($this->genusScientists->contains($user)) {
//            return;
//        }
//        $this->genusScientists[] = $user;
//        $user->addStudiedGenus($this);
//    }

//    /**
//     * @param User
//     */
//    public function removeGenusScientist(User $user)
//    {
//        if (!$this->genusScientists->contains($user)) {
//            return;
//        }
//        $this->genusScientists->removeElement($user);
//        $user->removeStudiedGenus($this);
//    }


    /**
     * @param GenusScientist
     */
    public function addGenusScientist(GenusScientist $genusScientist)
    {
        if ($this->genusScientists->contains($genusScientist)) {
            return;
        }
        $this->genusScientists[] = $genusScientist;
        // needed to update the owning side of the relationship!
        $genusScientist->setGenus($this);
    }
    /**
     * @param GenusScientist
     */
    public function removeGenusScientist(GenusScientist $genusScientist)
    {
        if (!$this->genusScientists->contains($genusScientist)) {
            return;
        }
        $this->genusScientists->removeElement($genusScientist);
        // needed to update the owning side of the relationship!
        $genusScientist->setGenus(null);
    }

    public function getExpertScientists()
    {
        return $this->getGenusScientists()->matching(
            GenusRepository::createExpertCriteria()
        );
    }

}