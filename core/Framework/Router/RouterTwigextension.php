<?php

namespace Core\Framework\Router;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;


/**
 * Extension Twig permettant d'appeler des fonctions défini du Router à l'intérieur des vues twig 
 */
class RouterTwigExtension extends AbstractExtension
{
    private Router $router;


    /**
     * Récupère l'instance du Router et l'enregistre
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }


    /**
     * Déclare les fonctions disponibles côté Vue
     *
     * @return void
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('path', [$this, 'path'])
        ];
    }

    /**
     * Fait appel à la méthode generateUri() du Router et retourne son résultat
     *
     * @param string $name
     * @param array $params
     * @return string
     */
    public function path(string $name, array $params = []): string
    {
        return $this->router->generateUri($name, $params);
    }
}
