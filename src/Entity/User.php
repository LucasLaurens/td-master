<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Description: on indique que la classe est implémentée par le UserInterface pour que Symfony comprenne que l'on est sur le côté sécurité de l'App
 * on créer une nouvelle entité dans laquelle on va définir le rôle 
 * Ensuite l'initialisation des champs et leur getter 
 * puis ajouter des méthodes afin de passer nos données en objet ou en string afin de communiquer entre la vue et le contrôler (& la base de données)
 * 
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return (Role|String)[] The user roles
     */
    public function getRoles () 
    {
        return ['ROLE_ADMIN'];
    }

    /**
     * @return string|null The salt
     */
    public function getSalt () 
    {
        return null;
    }

   
    public function eraseCredentials()
    {
    }

    /**
     * @link https://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize () 
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password
        ]);
    }

    /**
     * @link https://php.net/manual/en/serializable.serialize.php
     * @param string $serialized <p>
     * The string representation of the <object data="</p>" type="" class=""></object>
     * @return void
     * @since 5.1.0
     */
    public function unserialize ($serialized) 
    {
        list(
            $this->id,
            $this->username,
            $this->password
        ) = unserialize($serialized, ['allowed_classes' => false]);
    }
}
