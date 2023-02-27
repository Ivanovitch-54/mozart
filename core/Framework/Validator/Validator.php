<?php

namespace Core\Framework\Validator;

use Doctrine\ORM\EntityRepository;

// Permet de valider les données (Sorte de Regex)

class Validator
{
    private array $data;

    private array $errors;


    /**
     * Enregistre le tableau de données à valider 
     *
     * @param array $data Tableau de données (Habituellement il s'agit du tableau récupérer par $request->getParsedBody())
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }


    /**
     * Liste les index attendu et obligatoire  dans le tableau donnée 
     *
     * @param string ...$keys Liste de chaine de caractères,
     *  "...$keys" permet de précisé que l'on s'attend à un nombre indéfini de valeur 
     * @return self
     */
    public function required(string ...$keys): self
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $this->data) || $this->data[$key] === '' || $this->data[$key] === null) { // Permet de savoir si la clé n'existe pas dans le tableau 
                $this->addError($key, 'required');
            }
        }

        return $this;
    }


    /**
     * S'assure que le champs est une adresse email valide 
     *
     * @param string $key
     * @return self
     */
    public function email(string $key): self
    {
        // filter_var fonction native qui permet de vérifié la conformité d'une Valeur en fonction d'un filtre (autre filtre : php manual)
        if (!filter_var($this->data[$key], FILTER_VALIDATE_EMAIL)) {
            $this->addError($key, 'email');
        }

        return $this;
    }


    /**
     * S'assure que le nombre de carcactère d'une chaîne soit bien compris entre un min et un max
     *
     * @param string $key
     * @param integer $min
     * @param integer $max
     * @return void
     */
    public function strSize(string $key, int $min, int $max): self // A besoin de la clé et d'une valeur MIN/MAX
    {
        if (!array_key_exists($key, $this->data)) {
            return $this;
        }
        $length = mb_strlen($this->data[$key]);     // mb_strlen permet de compter avec la même valeur malgré les accents 
        if ($length < $min) {
            $this->addError($key, 'strMin');
        }
        if ($length > $max) {
            $this->addError($key, 'strMax');
        }
        return $this;
    }


    /**
     * S'assure que le champ saisi possède la même valeur que son champs de confirmation 
     * Si la valeur de $key est "mdp" le champ de confirmation doit absolument se nommée "mdp_confirm"
     * @param string $key
     * @return self
     */
    public function confirm(string $key): self
    {
        $confirm = $key . "_confirm";

        if (!array_key_exists($key, $this->data)) {
            return $this;
        }

        if (!array_key_exists($confirm, $this->data)) {
            return $this;
        }

        if ($this->data[$key] !== $this->data[$confirm]) {  // Ici je vérifie que le mdp saisi la première fois correspond a la confirmation du deuxieme input
            $this->addError($key, 'confirm');
        }

        return $this;
    }


    /**
     * S'assure qu'une valeur soit unique en base de données
     *
     * @param string $key Index du Tableau
     * @param EntityRepository $repo Doctrine Repositories de l'élément à vérifier 
     * @param string $field Champs à vérifier en Base De Données (par défault vaut nom)
     * @return self
     */
    public function isUnique(string $key, EntityRepository $repo, string $field = 'nom'): self
    {

        $all = $repo->findAll(); // $all Récupére toutes les entités du repositories (BDD) 
        $method = 'get' . ucfirst($field); // Créer le nom de la méthode utilisable pour récupérer la valeur (exemple: $field = 'model' alors $method = 'getModel')
        foreach ($all as $item) { // On boucle sur tous les enregistrement de la base de données 
            if (strcasecmp($item->$method(), $this->data[$key]) === 0) // On vérifié si la valeur saisie par l'utilisateur correspond à une valeur existante en base de données sans tenir compte des accents, si c'est le cas soulève une erreur
            {
                $this->addError($key, 'unique'); // Vérifie que la clé voulu sois bien unique 
                break;
            }
        }

        return $this;
    }


    /**
     * Renvoi le Tableau d'Erreur , doit être appelé seulement après les autres methodes 
     *
     * @return array|null
     */
    public function getErrors(): ?array
    {
        return $this->errors ?? null;
    }

    private function addError(string $key, string $rule): void
    {
        if (!isset($this->errors[$key])) {
            $this->errors[$key] = new ValidatorError($key, $rule);
        }
    }
}
