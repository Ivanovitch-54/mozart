<?php 
namespace Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="utilisateur")
 */
class User 
{
    /**
     * Id de l'utilisateur
     * @ORM\GeneratedValue(strategy="IDENTIFY")
     * @ORM\Column(type="integer")
     * @var integer
     */
    private int $id;

    /**
     * Nom de l'utilisateur
     * @ORM\Column(type="string", length="55")
     * @var string
     */
    private string $nom;

    /**
     * Prenom de l'utilisateur
     * @ORM\Column(type="string", length="55")
     * @var string
     */
    private string $prenom;

    /**
     * Email de l'utilisateur 
     * @ORM\Column(type="string", length="150")
     * @var string
     */
    private string $mail;


    /**
     * Mot de passe de l'utilisateur
     * @ORM\Column(type="string", length="255")
     * @var string
     */
    private string $password;

    /**
     * Get id de l'utilisateur
     *
     * @return  integer
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get nom de l'utilisateur
     *
     * @return  string
     */ 
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set nom de l'utilisateur
     *
     * @param  string  $nom  Nom de l'utilisateur
     *
     * @return  self
     */ 
    public function setNom(string $nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get prenom de l'utilisateur
     *
     * @return  string
     */ 
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set prenom de l'utilisateur
     *
     * @param  string  $prenom  Prenom de l'utilisateur
     *
     * @return  self
     */ 
    public function setPrenom(string $prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get email de l'utilisateur
     *
     * @return  string
     */ 
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set email de l'utilisateur
     *
     * @param  string  $mail  Email de l'utilisateur
     *
     * @return  self
     */ 
    public function setMail(string $mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mot de passe de l'utilisateur
     *
     * @return  string
     */ 
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set mot de passe de l'utilisateur
     *
     * @param  string  $password  Mot de passe de l'utilisateur
     *
     * @return  self
     */ 
    public function setPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }
}
