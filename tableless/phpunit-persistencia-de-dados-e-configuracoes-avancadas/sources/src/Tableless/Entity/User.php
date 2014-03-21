<?php

namespace tableless\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\Hydrator;
use Zend\Math\Rand;
use Zend\Crypt\Key\Derivation\Pbkdf2;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User
{

    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var integer
     * O GeneratedValue indica que este campo será um auto_increment
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     *
     * @var string
     */
    private $email;
    
    /**
     * @ORM\Column(type="text", length=255, nullable=false)
     *
     * @var string
     */
    private $password;
    
    
    /**
     * @ORM\Column(type="text")
     * @var string
     */
    protected $salt;

    /**
     * @ORM\Column(name="profile_pic", type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $profilePic;

    
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }
    
    public function getPassword()
    {
        return $this->password;
    }
    
    public function getSalt()
    {
        return $this->salt;
    }

    public function getProfilePic()
    {
        return $this->profilePic;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }
    
    public function setPassword( $password )
    {
        $this->password = $this->encryptPassword($password);
        return $this;
    }
    
    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    public function setProfilePic($profilePic)
    {
        $this->profilePic = $profilePic;
        return $this;
    }

    /**
     * 
     * @param array $data
     */
    public function __construct($data = array())
    {
        /*
         * Utilizando o Hydrator do Zend Framework
         * 
         * Esta ferramenta é responsável por transformar, neste caso, 
         * um array em valores dos atributos do objeto User.
         */
        $hydrator = new Hydrator\ClassMethods();
        $hydrator->hydrate($data, $this);
        
        // Definindo o salt para o hash de senha
        $this->setSalt(base64_encode(Rand::getBytes(8, true)));
        
    }
    
    public function encryptPassword($password)
    {
        return base64_encode(Pbkdf2::calc('sha256', $password, $this->salt, 10000, strlen($password*2)));
    }

}
