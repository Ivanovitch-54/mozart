<?php

namespace Core\Framework\Validator;

class ValidatorError
{
    private string $key;

    private string $rule;

    private array $message = [
        'required' => "Le champs %s est requis", // %s correspond a ce qu'on passe a la suite de sprintf 
        'email' => "Le champs %s doit être un email valide",
        'strMin' => "Le champs %s n'atteint pas le minimum de caractère requis",
        'strMax' => "Le champs %s dépasse le nombre de caractères autorisées",
        'confirm' => "Les mots de passe ne correspondent pas, merci de resaisir un Mot de Passe identique au premier",
        'unique' => "La valeur du champs %s est déjà connu du système ! Arrête d'essayer d'insérer des doublons !",
    ];

    public function __construct(string $key, string $rule)
    {
        $this->key = $key;
        $this->rule = $rule;
    }

    public function toString(): string
    {
        if (isset($this->message[$this->rule])) {
            if ($this->key === 'mdp'){
                return sprintf($this->message[$this->rule], 'mot de passe');    // Permet de placer rule au niveau de %s 
            } else {
                return sprintf($this->message[$this->rule], $this->key); // On peut remplacer les deux lignes par :  $this->key === 'mdp' ? 'mot de passe' : $this->key
            }
        }
        return $this->rule;
    }
}
