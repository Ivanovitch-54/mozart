<?php

namespace Core\Framework\Router;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\Route as ZendRoute;
use Core\Framework\Router\Route;

class Router
{
    private FastRouteRouter $router;

    private array $routes = [];

    /**
     * Instance un FastRouteRouter et l'enregistre
     */
    public function __construct()
    {
        $this->router = new FastRouteRouter();
    }


    /**
     * Permet de rajouter une route disponible en methode GET
     *
     * @param string $path
     * @param [type] $callable
     * @param string $name
     * @return void
     */
    public function get(string $path, $callable, string $name): void // $callable est égal à la fonction à appeler 
    {
        $this->router->addRoute(new ZendRoute($path, $callable, ['GET'], $name));
        $this->routes[] = $name;
    }


    /**
     * Permet de rajouter une route disponible en methode POST
     *
     * @param string $path
     * @param [type] $callable
     * @param string|null $name
     * @return void
     */
    public function post(string $path, $callable, string $name = null): void // $callable est égal à la fonction à appeler 
    {
        $this->router->addRoute(new ZendRoute($path, $callable, ['POST'], $name));
    }


    /**
     * Vérifie que l'url et la methode de la requete correspondent à une route connue
     * si oui, retourne un objet Route qui correspond
     * @param ServerRequestInterface $request
     * @return Route|null
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        $result = $this->router->match($request);

        if ($result->isSuccess()) {
            return new Route(
                $result->getMatchedRouteName(),
                $result->getMatchedMiddleware(),
                $result->getMatchedParams()
            );
        } else {
            return null;
        }
    }


    /**
     * Génére l'uri (fin d'url) de la route demandée en fonction de son nom 
     * [Optionnel] : On peut ajotuer un tableau de parametre 
     *
     * @param string $name
     * @param array|null $params [Optionnel]
     * @return string|null
     */
    public function generateUri(string $name, ?array $params = []): ?string
    {
        return $this->router->generateUri($name, $params);
    }
}

// Ceci est une classe en langage PHP appelée Router.
// Elle fait partie du namespace Core\Framework\Router. Cette classe est utilisée pour créer et gérer les routes dans une application.
// Elle utilise la classe FastRouteRouter du namespace Zend\Expressive\Router, la classe ServerRequestInterface du namespace Psr\Http\Message
// et la classe Route du namespace Zend\Expressive\Router.

// La classe possède deux propriétés privées :

// $router: une instance de la classe FastRouteRouter qui est responsable de la gestion du routage de l'application.
// $routes: un tableau qui stocke les noms des routes créées par la classe.
// La classe possède deux méthodes publiques:

// get(): prend en entrée un chemin, une fonction de rappel et un nom en tant que paramètres, crée une nouvelle route avec la méthode GET et le chemin,
// la fonction de rappel et le nom donnés, et l'ajoute au routeur et au tableau $routes.
// match(): prend en entrée une ServerRequestInterface $request en tant que paramètre, utilise le routeur pour faire correspondre la demande à une route,
// et renvoie le résultat de la correspondance. Si la correspondance n'est pas réussie, il renvoie null.

// Cette classe permet de créer et gérer facilement les routes dans une application en utilisant la bibliothèque FastRouteRouter,
// ce qui facilite la gestion des demandes HTTP et la redirection vers la fonction de rappel appropriée.