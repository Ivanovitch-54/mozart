<?php

namespace Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="evenement")
 */
class Evenement
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
     * @ORM\Column(type="string", length="255")
     * @var string
     */
    private string $description;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    private int $nbr_places_dispo;


    /**
     * @ORM\Column(type="datetime")
     * @var
     */
    private $startAt;

    /**
     * @ORM\Column(type="datetime")
     * @var 
     */
    private  $endAt;

    /**
     * Many Evenements have Many Users.
     * @ORM\ManyToMany(targetEntity="User", inversedBy="evenements", cascade={"merge"})
     * @ORM\JoinTable(name="evenements_users")
     * @var Collection<int, User>
     */
    private Collection $users;

    /**
     * Many Evenements have Many Intervenants.
     * @ORM\ManyToMany(targetEntity="Intervenant", inversedBy="evenements")
     * @ORM\JoinTable(name="evenements_intervenants")
     * @var Collection<int, Intervenant>
     */
    private Collection $intervenants;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->intervenants = new ArrayCollection();
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
     * @return Evenement
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
     * Set description.
     *
     * @param string $description
     *
     * @return Evenement
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set startAt.
     *
     * @param \DateTime $startAt
     *
     * @return Evenement
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;

        return $this;
    }

    /**
     * Get startAt.
     *
     * @return \DateTime
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * Set endAt.
     *
     * @param \DateTime $endAt
     *
     * @return Evenement
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * Get endAt.
     *
     * @return \DateTime
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    public function getTimesForInputStartAt(): string
    {
        return $this->startAt->format('Y-m-d\TH:i');
    }

    public function getTimesForInputEndAt(): string
    {
        return $this->endAt->format('Y-m-d\TH:i');
    }

    /**
     * Get user>
     *
     * @return  Collection<int,
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add user.
     *
     * @param \Model\Entity\User $user
     *
     * @return Evenement
     */
    public function addUser(\Model\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user.
     *
     * @param \Model\Entity\User $user
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeUser(\Model\Entity\User $user)
    {
        return $this->users->removeElement($user);
    }

    /**
     * Remove intervenant.
     *
     * @param \Model\Entity\Intervenant $intervenant
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeIntervenant(\Model\Entity\Intervenant $intervenant)
    {
        return $this->intervenants->removeElement($intervenant);
    }

    /**
     * Add intervenant.
     *
     * @param \Model\Entity\Intervenant $intervenant
     *
     * @return Evenement
     */
    public function addIntervenant(\Model\Entity\Intervenant $intervenant)
    {
        $this->intervenants[] = $intervenant;

        return $this;
    }

    /**
     * Get intervenants.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIntervenants()
    {
        return $this->intervenants;
    }

    /**
     * Set intervenant.
     *
     * @param \Model\Entity\Intervenant $intervenant
     *
     * @return Evenement
     */
    public function setIntervenants(\Model\Entity\Intervenant $intervenant)
    {
        $this->intervenants->clear();
        $this->intervenants[] = $intervenant;

        return $this;
    }


    /**
     * Set nbrPlacesDispo.
     *
     * @param int $nbrPlacesDispo
     *
     * @return Evenement
     */
    public function setNbrPlacesDispo($nbrPlacesDispo)
    {
        $this->nbr_places_dispo = $nbrPlacesDispo;

        return $this;
    }

    /**
     * Get nbrPlacesDispo.
     *
     * @return int
     */
    public function getNbrPlacesDispo()
    {
        return $this->nbr_places_dispo;
    }
}
