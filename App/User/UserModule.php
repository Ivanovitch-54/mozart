<?php

namespace App\User;

use App\User\Action\UserAction;
use Core\Framework\AbstractClass\AbstractModule;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Router\Router;
use Psr\Container\ContainerInterface;

class UserModule extends AbstractModule
{
    public const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

    private ContainerInterface $container;
    private RendererInterface $renderer;
    private Router $router;

    public function __construct(ContainerInterface $container)
    {
        $userAction = $container->get(UserAction::class);
        $this->container = $container;
        $this->router = $container->get(Router::class);
        $this->renderer = $container->get(RendererInterface::class);

        // Chemin des vues
        $this->renderer->addPath('user', __DIR__ . DIRECTORY_SEPARATOR . 'view');

        // On déclare notre première route
        $this->router->get('/login', [$userAction, 'logView'], 'user.login');

        // Déconnexion USER
        $this->router->get('/user/logout', [$userAction, 'logout'], 'user.logout');
        // Inscription USER
        $this->router->post('/newUser', [$userAction, 'signIn'], 'user.new');
        // Connexion USER
        $this->router->post('/connexion', [$userAction, 'login'], 'user.connection');

        // Vue des événements côté USER
        $this->router->get('/user/evenement', [$userAction, 'liste'], 'user.liste');
        // Inscription à un événement côté USER
        $this->router->get('/user/inscEvent/{id:[\d]+}', [$userAction, 'inscEvent'], 'user.inscEvent');
        // Liste des événements où le USER est inscrit 
        $this->router->get('/user/listEvent', [$userAction, 'listEvent'], 'user.listEvent');
        // Permet à un User de se déscinscrire d'un événement
        $this->router->get('/user/decoEvent/{id:[\d]+}', [$userAction, 'decoEvent'], 'user.decoEvent');
        // Permet à un User d'acceder a ses informations personnelles
        $this->router->get('/user/monCompte', [$userAction, 'monCompte'], 'user.monCompte');
        // Permet à un User de modifier ses informations personnelles
        $this->router->post('/user/monCompte', [$userAction, 'monCompte']);
    }
}
