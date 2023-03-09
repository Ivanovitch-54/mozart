<?php

namespace Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity
 * @ORM\Table(name="intervenant")
 */
class Intervenant
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     * @var integer
     */
    private int $id;


    /**
     * @ORM\Column(type="string", length="55")
     * @var string
     */
    private string $nom;

    /**
     * @ORM\Column(type="string", length="55")
     * @var string
     */
    private string $prenom;

    /**
     * Role de l'intervenant 
     * @ORM\Column(type="string", length="60")
     * @var string
     */
    private string $role;

    /**
     * Many Intervenants have Many Evenements.
     * @ORM\ManyToMany(targetEntity="Evenement")
     * @ORM\JoinTable(
     * joinColumns={@ORM\JoinColumn(name="id_intervenant",referencedColumnName="id", onDelete="CASCADE")},
     * inverseJoinColumns={@ORM\JoinColumn(name="id_evenement",referencedColumnName="id")})
     * @var Collection<int, Evenement>
     */
    private Collection $evenements;

    public function __construct()
    {
        $this->evenements = new ArrayCollection();
    }

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
     * Set nom.
     *
     * @param string $nom
     *
     * @return self
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom.
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom.
     *
     * @param string $prenom
     *
     * @return self
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom.
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    public function hydrate(array $data): self  // Permet d'éviter de réécrire a chaque fois set avant chaque méthode
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * Get role de l'intervenant
     *
     * @return  string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set role de l'intervenant
     *
     * @param  string  $role  Role de l'intervenant
     *
     * @return  self
     */
    public function setRole(string $role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Add evenement.
     *
     * @param \Model\Entity\Evenement $evenement
     *
     * @return User
     */
    public function addEvenement(\Model\Entity\Evenement $evenement)
    {
        $this->evenements[] = $evenement;

        return $this;
    }

    /**
     * Remove evenement.
     *
     * @param \Model\Entity\Evenement $evenement
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeEvenement(\Model\Entity\Evenement $evenement)
    {
        return $this->evenements->removeElement($evenement);
    }

    /**
     * Get evenements.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvenements()
    {
        return $this->evenements;
    }
}
