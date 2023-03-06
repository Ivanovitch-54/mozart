<?php

namespace App\User;

use App\Car\Action\CarAction;
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
        $carAction = $container->get(CarAction::class); // Ensembles d'actions possibles

        // Chemin des vues
        $this->renderer->addPath('user', __DIR__ . DIRECTORY_SEPARATOR . 'view');

        // On déclare notre première route
        $this->router->get('/login', [$userAction, 'logView'], 'user.login');

        // Suite des routes en méthode GET
        $this->router->get('/user/home', [$userAction, 'home'], 'user.home');
        $this->router->get('/user/logout', [$userAction, 'logout'], 'user.logout');
        // Suite des routes en méthode POST
        $this->router->post('/newUser', [$userAction, 'signIn'], 'user.new');
        $this->router->post('/connexion', [$userAction, 'login'], 'user.connection');

        // TEST // 
        $this->router->post('/change', [$userAction, 'change'], 'user.change');

    }
}
