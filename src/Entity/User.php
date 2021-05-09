<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table("user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    // ALL ROLES
    const USER_ADMIN_ROLE = "ROLE_ADMIN";
    const USER_USER_ROLE = "ROLE_USER";

    //Anonymous User
    const ANONYMOUS_USERNAME = "anonymousUsername";
    const ANONYMOUS_EMAIL = "anonymousemail@todolist.fr";
    const ANONYMOUS_PASSWORD = "anonymousPassword";

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\NotBlank(message="Vous devez saisir un nom d'utilisateur.")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Assert\NotBlank(message="Vous devez saisir une adresse email.")
     * @Assert\Email(message="Le format de l'adresse n'est pas correcte.")
     */
    private $email;

    /**
    * @ORM\OneToMany(targetEntity=Task::class, cascade={"persist", "remove"}, mappedBy="user")
    */
    protected $tasks;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    /**
     * @return Int $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return String $username
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    /**
     * @param String $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @return String $password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param String $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return Email $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param Email $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return Array $roles
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function eraseCredentials()
    {
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    /**
     * @param Task $task
     * @return User $user
     */
    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setUser($this);
        }

        return $this;
    }

    /**
     * @param Task $task
     * @return User $user
     */
    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getUser() === $this) {
                $task->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @param Array $roles
     * @return User $user
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
