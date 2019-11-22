<?php


namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @UniqueEntity(fields={"email"}, message="It looks like your already have an account!")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * The encoded password
     *
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * A non-persisted field that's used to create the encoded password.
     *
     * @var string
     * @Assert\NotBlank(groups={"Registration"})
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="json_array")
     */
    private $roles = ['ROLE_MANAGE_GENUS'];

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default" : false})
     */
    private $isScientist;

    /**
     * @ORM\Column(type="string")
     */
    private $avatarUri;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $universityName;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @XXORM\ManyToMany(targetEntity="AppBundle\Entity\Genus", mappedBy="genusScientists")
     * @XXORM\OrderBy({"name" = "ASC"})
     */
    /**
     * @ORM\OneToMany(targetEntity="GenusScientist", mappedBy="user")
     */
    private $studiedGenuses;

    /**
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="last_updated_by_id", referencedColumnName="id", nullable=true)
     */
    protected $lastUpdatedBy;

    public function __construct()
    {
        $this->studiedGenuses = new ArrayCollection();
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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function getRoles()
    {
        $roles = $this->roles;
        // give everyone ROLE_USER!
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }
        return $roles;
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        // forces the object to look "dirty" to Doctrine. Avoids
        // Doctrine *not* saving this entity, if only plainPassword changes
        $this->password = null;
    }

    /**
     * @return mixed
     */
    public function getIsScientist()
    {
        return $this->isScientist;
    }

    /**
     * @param mixed $isScientist
     */
    public function setIsScientist($isScientist)
    {
        $this->isScientist = $isScientist;
    }

    /**
     * @return mixed
     */
    public function getAvatarUri()
    {
        return $this->avatarUri;
    }

    /**
     * @param mixed $avatarUri
     */
    public function setAvatarUri($avatarUri)
    {
        $this->avatarUri = $avatarUri;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getUniversityName()
    {
        return $this->universityName;
    }

    /**
     * @param mixed $universityName
     */
    public function setUniversityName($universityName)
    {
        $this->universityName = $universityName;
    }

    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * @return ArrayCollection|GenusScientist[]
     */
    public function getStudiedGenuses()
    {
        return $this->studiedGenuses;
    }

//    public function addStudiedGenus(Genus $genus)
//    {
//        if ($this->studiedGenuses->contains($genus)) {
//            return;
//        }
//        $this->studiedGenuses[] = $genus;
//        $genus->addGenusScientist($this);
//    }

//    public function removeStudiedGenus(Genus $genus)
//    {
//        if(!$this->studiedGenuses->contains($genus)) {
//            return;
//        }
//        $this->studiedGenuses->removeElement($genus);
//        $genus->removeGenusScientist($this);
//    }

//    /**
//     * @param GenusScientist
//     */
//    public function addStudiedGenus(GenusScientist $genusScientist)
//    {
//        if ($this->studiedGenuses->contains($genusScientist)) {
//            return;
//        }
//        $this->studiedGenuses[] = $genusScientist;
//        // needed to update the owning side of the relationship!
//        $genusScientist->setUser($this);
//    }
//    /**
//     * @param GenusScientist
//     */
//    public function removeStudiedGenus(GenusScientist $genusScientist)
//    {
//        if (!$this->studiedGenuses->contains($genusScientist)) {
//            return;
//        }
//        $this->studiedGenuses->removeElement($genusScientist);
//        // needed to update the owning side of the relationship!
//        $genusScientist->setUser(null);
//    }

    public function setFullName($fullName)
    {
        $names = explode(' ', $fullName);
        $firstName = array_shift($names);
        $lastName = implode(' ', $names);
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return User
     */
    public function getLastUpdatedBy()
    {
        return $this->lastUpdatedBy;
    }

    /**
     * @param User $lastUpdatedBy
     */
    public function setLastUpdatedBy(User $lastUpdatedBy)
    {
        $this->lastUpdatedBy = $lastUpdatedBy;
    }

    public function __toString()
    {
        return (string) $this->getFullName() ? $this->getFullName() : $this->getEmail();
    }
}