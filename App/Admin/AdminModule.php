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

        // LogIn de l'Admin
        $this->router->get('/admin/login', [$authAction, 'login'], 'admin.login');
        // LogIn de l'Admin
        $this->router->post('/admin/login', [$authAction, 'login']);
        // LogOut de l'Admin
        $this->router->get('/admin/logout', [$authAction, 'logout'], 'admin.logout');
        // Page Home de l'Admin
        $this->router->get('/admin/home', [$adminAction, 'home'], 'admin.home');
        // Liste Des Evenements
        $this->router->get('/admin/event', [$adminAction, 'event'], 'admin.event');
        // Ajouter Un Evenement
        $this->router->get('/admin/addEvent', [$adminAction, 'addEvent'], 'event.add');
        $this->router->post('/admin/addEvent', [$adminAction, 'addEvent']);
        // Suppression D'Evenement
        $this->router->get('/admin/deleteEvent/{id:[\d]+}', [$adminAction, 'delete'], 'event.delete');
        // Modifier un Evenement
        $this->router->get('/admin/updateEvent/{id:[\d]+}', [$adminAction, 'update'], 'event.update');
        $this->router->post('/admin/updateEvent/{id:[\d]+}', [$adminAction, 'update']);
        // Liste des Intervenants
        $this->router->get('/admin/inter', [$adminAction, 'showInter'], 'admin.inter');
        // Ajouter un Intervenant
        $this->router->get('/admin/addInter', [$adminAction, 'addInter'], 'inter.add');
        $this->router->post('/admin/addInter', [$adminAction, 'addInter']);
        // Supprimer un Intervenant
        $this->router->get('/admin/deleteInter/{id:[\d]+}', [$adminAction, 'deleteInter'], 'inter.delete');
        // Désinscrire un USER
        $this->router->get('/admin/removeUsers/{idUser:[\d]+}-{idEvent:[\d]+}', [$adminAction, 'removeUsers'], 'users.remove');
    }
}
