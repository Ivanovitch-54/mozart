<?php

namespace Core\Framework\Middleware;

use Core\Toaster\Toaster;
use GuzzleHttp\Psr7\Response;
use Core\Framework\Auth\AdminAuth;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Middleware\AbstractMiddleware;


/**
 * Vérifie si la route est protégé grâce au début de l'url,
 * si oui s'assure que l'utilisateur à le droit d'y accéder
 */
class AdminAuthMiddleware extends AbstractMiddleware
{
    private ContainerInterface $container;
    private Toaster $toaster;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->toaster = $container->get(Toaster::class);
    }

    public function process(ServerRequestInterface $request)
    {
        $uri = $request->getUri()->getPath();
        TODO:
        if (str_starts_with($uri, '/adminTEST') && $uri !== "/admin/login") // On vérifie si l'url commence par '/admin' et n'est pas égale à '/admin/login' car c'est une route qu'on ne veut pas protéger justement
        {
            $auth = $this->container->get(AdminAuth::class); // On récupère l'objet qui gère l'administrateur 
            if (!$auth->isLogged() || $auth->isAdmin()) { // On vérifie si l'administrateur est connecté et qu'il s'agit bien d'un administrateur
                if (!$auth->isLogged()) { //  Si personne n'est connecté on renvoi en conséquence 
                    $this->toaster->makeToast("Vous devez être connecté pour accéder à cette page ", Toaster::ERROR);
                } elseif (!$auth->isAdmin()) { // Si quelqu'un est connecté mais n'est pas un admin refuse l'accès
                    $this->toaster->makeToast("Vous ne passerez pas ! Tu n'a pas les droits d'accès à cette page ! Tu n'est pas ADMIN !", Toaster::ERROR);
                }
                return (new Response())
                    ->withHeader('Location', '/');
            }
        }

        return parent::process($request);
    }
}
