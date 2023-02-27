<?php

namespace Core\Framework\Auth;

use Exception;
use Model\Entity\User;
use Core\Toaster\Toaster;
use Doctrine\ORM\EntityManager;
use Core\Framework\Router\Router;
use Core\Session\SessionInterface;
use Doctrine\ORM\EntityRepository;
use Psr\Container\ContainerInterface;
use Core\Framework\Router\RedirectTrait;

class UserAuth
{
    use RedirectTrait;

    private ContainerInterface $container;
    private EntityManager $manager;
    private Toaster $toaster;
    private Router $router;
    private SessionInterface $session;
    private EntityRepository $repository;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->manager = $container->get(EntityManager::class);
        $this->repository = $this->manager->getRepository(User::class);
        $this->toaster = $container->get(Toaster::class);
        $this->router = $container->get(Router::class);
        $this->session = $container->get(SessionInterface::class);
    }

    public function signIn(array $data)
    {
        $user = new User();
        $hash = password_hash($data['mdp'], PASSWORD_BCRYPT);
        $user->hydrate($data)
            ->setPassword($hash);

        try {
            $this->manager->persist($user); // Permet de faire vivre l'objet en dehors de la méthode 
            $this->manager->flush();
            return true;
        } catch (Exception $e) {
            $this->toaster->makeToast("Une erreur est survenue merci de réesayer", Toaster::ERROR);
            return $this->redirect('user.login');
        }
    }

    public function login(string $mail, string $pass): bool
    {
        $user = $this->repository->findOneBy(['mail' => $mail]); // Permet d'allez récupérer le champs mail en BDD
        if ($user && password_verify($pass, $user->getPassword())) { // Vérifie l'USER ainsi que le mot de passe 
            $this->session->set('auth', $user); // Authentifie l'uitlisateur
            return true;
        }
        return false;
    }

    public function isLogged(): bool
    {
        return $this->session->has('auth');
    }

    public function isUser(): bool
    {
        if ($this->isLogged()) {
            return $this->session->get('auth') instanceof User;
        }
        return false;
    }


    // HORS COURS
    public function logout(): void
    {
        $this->session->delete('auth');
    }
}
