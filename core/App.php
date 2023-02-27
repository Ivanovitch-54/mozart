<?php

namespace Core;

use Core\Framework\Middleware\MiddlewareInterface;
use Core\Framework\Router\Router;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;

// A pour charge de charger toutes les parties du site 
class App
{

    private Router $router;

    private array $modules;

    private ContainerInterface $container;

    private MiddlewareInterface $middleware;


    /**
     * Initialise la liste des modules et enregistre le container de dépendance
     *
     * @param ContainerInterface $container
     * @param array $modules
     */
    public function __construct(ContainerInterface $container, array $modules = [])
    {
        $this->router = $container->get(Router::class);

        foreach ($modules as $module) {
            $this->modules[] = $container->get($module);
        }

        $this->container = $container;
    }


    /**
     * Permet de lancer la chaine de responsabiliter des middleware // Traite la requête du serveur en l'envoyant dans la chaine de responsabiliter
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function run(ServerRequestInterface $request): ResponseInterface  // Permet de démarrer la chaine de vérif 
    {
        return $this->middleware->process($request);
    }


    /**
     * Enregistre le premier Middleware de la chaine de responsabiliter
     *
     * @param MiddlewareInterface $middleware
     * @return MiddlewareInterface
     */
    public function linkFirst(MiddlewareInterface $middleware): MiddlewareInterface
    {
        $this->middleware = $middleware;
        return $middleware;
    }



    /**
     * Retourne l'instance de PHP DI 
     *
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
