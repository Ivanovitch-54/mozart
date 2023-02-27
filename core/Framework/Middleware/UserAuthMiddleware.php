<?php

namespace Core\Framework\Middleware;

use Core\Framework\Auth\UserAuth;
use Core\Framework\Router\Router;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Middleware\AbstractMiddleware;
use Core\Framework\Router\RedirectTrait;
use Core\Toaster\Toaster;


/**
 * Vérifie si la route est protégé grâce au début de l'url,
 * si oui s'assure que l'utilisateur à le droit d'y accéder
 */
class UserAuthMiddleware extends AbstractMiddleware
{
    use RedirectTrait;

    private ContainerInterface $container;
    private Router $router;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->router = $container->get(Router::class);
    }

    public function process(ServerRequestInterface $request)
    {
        $uri = $request->getUri()->getPath(); // getUri permet de récuperer Localhost et getPath ce qui suit l'url 
        if (str_starts_with($uri, '/user')) { // Vérifie si l'url commence par '/user'
            $auth = $this->container->get(UserAuth::class);
            if (!$auth->isLogged() or !$auth->isUser()) { // Si le User n'est pas connecter Ni un utilisateur ( or = || )
                $toaster = $this->container->get(Toaster::class);
                $toaster->makeToast("Veuillez vous connecté pour continuer", Toaster::ERROR);
                return $this->redirect('user.login');
            }
        }
        return parent::process($request);
    }
}
