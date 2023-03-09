<?php

namespace App\Admin;

use App\Admin\Action\AdminAction;
use App\Admin\Action\AdministratorAction;
use Core\Framework\Router\Router;
use Psr\Container\ContainerInterface;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\AbstractClass\AbstractModule;

class AdminModule extends AbstractModule
{
    public const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

    private Router $router;
    private RendererInterface $renderer;
    private ContainerInterface $container;



    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->router = $container->get(Router::class);
        $this->renderer = $container->get(RendererInterface::class);
        $adminAction = $container->get(AdministratorAction::class);
        $authAction = $container->get(AdminAction::class);

        $this->renderer->addPath('admin', __DIR__ . DIRECTORY_SEPARATOR . 'view'); // Crée un chemin vers la vue Admin

        $this->router->get('/admin/login', [$authAction, 'login'], 'admin.login'); // LogIn de l'Admin
        $this->router->post('/admin/login', [$authAction, 'login']); // LogIn de l'Admin
        $this->router->get('/admin/logout', [$authAction, 'logout'], 'admin.logout'); // LogOut de l'Admin 
        $this->router->get('/admin/home', [$adminAction, 'home'], 'admin.home'); // Page Home de l'Admin

        // TEST //
        $this->router->get('/admin/event', [$adminAction, 'event'], 'admin.event'); // Liste des Events
        $this->router->get('/admin/addEvent', [$adminAction, 'addEvent'], 'event.add'); // Page Ajout Event
        $this->router->post('/admin/addEvent', [$adminAction, 'addEvent']); // Ajout d'Event en POST
        $this->router->get('/admin/deleteEvent/{id:[\d]+}', [$adminAction, 'delete'], 'event.delete'); // Suppression D'Event 
        // Modifier un Event 
        $this->router->get('/admin/updateEvent/{id:[\d]+}', [$adminAction, 'update'], 'event.update'); // Modif Event
        $this->router->post('/admin/updateEvent/{id:[\d]+}', [$adminAction, 'update']); // Modif Event
    }
}
