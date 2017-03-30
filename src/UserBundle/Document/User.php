<?php

namespace UserBundle\Document;

use Symfony\Component\Security\Core\User\UserInterface;
use TBoileau\RethinkBundle\ODM\Metadata as Rethink;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Rethink\Table(name="user")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @Rethink\Id
     */
    protected $id;

    /**
     * @Rethink\Column(name="username",type="string")
     */
    protected $username;

    /**
     * @Rethink\Column(name="password",type="string")
     */
    protected $password;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Rethink\Column(name="email",type="string")
     */
    protected $email;

    /**
     * @Assert\NotBlank()
     * @Rethink\Column(name="firstname",type="string")
     */
    protected $firstname;

    /**
     * @Assert\NotBlank()
     * @Rethink\Column(name="lastname",type="string")
     */
    protected $lastname;

    /**
     * @Rethink\Column(name="facebook_id",type="string")
     */
    protected $facebookId;

    /**
     * @Rethink\Column(name="google_id",type="string")
     */
    protected $googleId;

    /**
     * @Assert\NotBlank(groups={"Registration"})
     */
    protected $plainPassword;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
    }

    public function getFacebookId()
    {
        return $this->facebookId;
    }

    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;
    }

    public function getGoogleId()
    {
        return $this->googleId;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
        $this->password = null;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function getSalt()
    {
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function serialize()
     {
         return serialize(array(
             $this->id,
             $this->username,
             $this->password,
             $this->firstname,
             $this->lastname,
             $this->email,
             $this->facebookId,
             $this->googleId
         ));
     }
     public function unserialize($serialized)
     {
         list (
             $this->id,
             $this->username,
             $this->password,
             $this->firstname,
             $this->lastname,
             $this->email,
             $this->facebookId,
             $this->googleId
         ) = unserialize($serialized);
     }
}
