<?php

namespace App\User\Action;

use Core\Framework\Router\RedirectTrait;
use Model\Entity\User;
use Core\Toaster\Toaster;
use Doctrine\ORM\EntityManager;
use Core\Framework\Auth\UserAuth;
use Core\Framework\Router\Router;
use Doctrine\ORM\EntityRepository;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Container\ContainerInterface;
use Core\Framework\Validator\Validator;
use Core\Framework\Renderer\RendererInterface;
use Core\Session\SessionInterface;

class UserAction
{
    use RedirectTrait;

    private ContainerInterface $container;
    private RendererInterface $renderer;
    private Router $router;
    private Toaster $toaster;
    private EntityRepository $repository;
    private SessionInterface $session;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->renderer = $container->get(RendererInterface::class);
        $this->toaster = $container->get(Toaster::class);
        $this->router = $container->get(Router::class);
        $this->repository = $container->get(EntityManager::class)->getRepository(User::class);
        $this->session = $container->get(SessionInterface::class);
        $user = $this->session->get('auth');
        if ($user) {
            $this->renderer->addGlobal('user', $user);
        }
    }

    public function logView(ServerRequest $request) // Permet de se connecter à la Vue Utilisateur
    {
        return $this->renderer->render('@user/forms'); // Retourne la vue avec les formulaires d'insc/co 
    }

    public function signIn(ServerRequest $request) // Ici je gère le formulaire d'inscription USER
    {
        $auth = $this->container->get(UserAuth::class);
        $data = $request->getParsedBody();
        $validator = new Validator($data); // Validator permet de vérifier les informations rentrer par le USER

        $errors = $validator
            ->required('nom', 'prenom', 'mail', 'mdp', 'mdp_confirm')
            ->email('mail')
            ->strSize('mdp', 12, 50)
            ->confirm('mdp')
            ->isUnique('mail', $this->repository, 'mail')
            ->getErrors();

        if ($errors) {
            foreach ($errors as $error) {
                $this->toaster->makeToast($error->toString(), Toaster::ERROR);
            }
            return $this->redirect('user.login');  // Redirige l'utilisateur 
        }

        $result = $auth->signIn($data);

        if ($result !== true) {
            return $result;
        }
        $this->toaster->makeToast('Inscription Réussie, vous pouvez désormais vous connecter ! :)', Toaster::SUCCESS);
        return $this->redirect('user.login');
    }

    // TEST // 

    public function change(ServerRequest $request)
    {
        $auth = $this->container->get(UserAuth::class);
        $data = $request->getParsedBody();
        $validator = new Validator($data); // Validator permet de vérifier les informations rentrer par le USER

        $errors = $validator
            ->required('nom', 'prenom', 'mdp', 'mdp_confirm')
            ->strSize('mdp', 12, 50)
            ->confirm('mdp')
            ->getErrors();

        if ($errors) {
            foreach ($errors as $error) {
                $this->toaster->makeToast($error->toString(), Toaster::ERROR);
            }
            return $this->redirect('user.login');  // Redirige l'utilisateur 
        }

        $user = $this->session->get('auth');
        $user->setNom($data['nom'])
            ->setPrenom($data['prenom'])
            ->setPassword($data['password']);
    }

    public function login(ServerRequest $request)
    {
        $data = $request->getParsedBody();

        $validator = new Validator($data);
        $errors = $validator
            ->required('mail', 'mdp')
            ->email('mail')
            ->getErrors();

        if ($errors) {
            foreach ($errors as $error) {
                $this->toaster->makeToast($error->toString(), Toaster::ERROR);
            }
            return $this->redirect('user.login');
        }

        $auth = $this->container->get(UserAuth::class);
        $response = $auth->login($data['mail'], $data['mdp']);
        if ($response) {
            $this->toaster->makeToast('Connexion Réussi', Toaster::SUCCESS);
            return $this->redirect('user.home');
        }
        $this->toaster->makeToast("Connexion échoué, merci de vérifier Email et MDP", Toaster::ERROR);
        return $this->redirect('user.login');
    }

    public function home(ServerRequest $request)
    {
        $user = $this->session->get('auth');
        return $this->renderer->render('@user/home', [
            'user' => $user
        ]); // Retourne la vu USER/HOME et permet de récupérer le User.Nom en même temps afin de l'afficher dans le home.html.twig
    }

    public function logout()
    {
        $auth = $this->container->get(UserAuth::class);
        $auth->logout();
        $this->toaster->makeToast('User Disconnected with success, please log in again to resume your activity ', Toaster::SUCCESS);
        return $this->redirect('user.login');
    }
}
   TODO: