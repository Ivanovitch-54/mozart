<?php

namespace App\Admin\Action;
// AdminAction == AdminAuth (différent de Brasero)
use Core\Toaster\Toaster;
use GuzzleHttp\Psr7\Response;
use Core\Framework\Router\Router;
use Core\Framework\Auth\AdminAuth;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Container\ContainerInterface;
use Core\Framework\Validator\Validator;
use Core\Framework\Renderer\RendererInterface;

class AdminAction
{
    private RendererInterface $renderer; // Ici c'est la déclaration 
    private ContainerInterface $container; // Ici c'est la déclaration 
    private Router $router;
    private Toaster $toaster;

    public function __construct(ContainerInterface $container) // Ici j'injecte mes dépendances dans les parenthèses
    {
        $this->container = $container;
        $this->renderer = $container->get(RendererInterface::class);
        $this->router = $container->get(Router::class);
        $this->toaster = $container->get(Toaster::class);
    }

    public function login(ServerRequest $request)
    {
        $method = $request->getMethod();
        if ($method === 'POST') {
            $auth = $this->container->get(AdminAuth::class);
            $data = $request->getParsedBody();
            $validator = new Validator($data);
            $errors = $validator->required('mail', 'mdp')
                ->getErrors();
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $this->toaster->makeToast($error->toString(), Toaster::ERROR);
                }
                return (new Response())
                    ->withHeader('location', '/admin/login');
            }
            if ($auth->login($data['mail'], $data['mdp'])) {
                $this->toaster->makeToast('Connexion réussie', Toaster::SUCCESS);
                return (new Response())
                    ->withHeader('Location', '/admin/home');
            }
            $this->toaster->makeToast('Connexion problème avec le MDP ou le Login', Toaster::ERROR);
            return (new Response())
                ->withHeader('Location', '/admin/login');
        }
        return $this->renderer->render('@admin/login'); // D'abord le nom du dossier ensuite le nom du fichier 
    }

    public function logout()
    {
        $auth = $this->container->get(AdminAuth::class);
        $auth->logout();
        $this->toaster->makeToast('Disconnected Succeed', Toaster::SUCCESS);
        return (new Response())
            ->withHeader('Location', '/admin/login');
    }
}
