<?php

namespace Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="administrateur")
 */

class Admin
{

    /**
     * Id de l'administrateur
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    private int $id;


    /**
     * Nom de l'Admin
     * @ORM\Column(type="string", name="admin" , length="55")
     * @var string
     */
    private string $name;


    /**
     * Mot de passe de l'Admin
     *@ORM\Column(type="string", name="password", length="255")
     * @var string
     */
    private string $password;


    /**
     * Email de l'admin 
     *@ORM\Column(type="string", name="email", length="55")
     * @var string
     */
    private string $mail;


    /**
     * Numéro de téléphone de l'Admin
     *@ORM\Column(type="string", name="phone_number", length="20")
     * @var string
     */
    private string $phone;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Admin
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return Admin
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set mail.
     *
     * @param string $mail
     *
     * @return Admin
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail.
     *
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set phone.
     *
     * @param string $phone
     *
     * @return Admin
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }
}
