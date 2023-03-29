<?php

namespace App\User\Action;

use Model\Entity\User;
use Core\Toaster\Toaster;
use Model\Entity\Evenement;
use Model\Entity\Intervenant;
use Doctrine\ORM\EntityManager;
use Core\Framework\Auth\UserAuth;
use Core\Framework\Router\Router;
use Core\Session\SessionInterface;
use Doctrine\ORM\EntityRepository;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Container\ContainerInterface;
use Core\Framework\Validator\Validator;
use Core\Framework\Router\RedirectTrait;
use Core\Framework\Renderer\RendererInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

class UserAction
{
    use RedirectTrait;

    private ContainerInterface $container;
    private RendererInterface $renderer;
    private Router $router;
    private Toaster $toaster;
    private EntityRepository $repository;
    private SessionInterface $session;
    private EntityManager $manager;
    private EntityRepository $eventRepository;
    private EntityRepository $interRepository;

    public function __construct(ContainerInterface $container, EntityManager $manager)
    {
        $this->container = $container;
        $this->manager = $manager;
        $this->renderer = $container->get(RendererInterface::class);
        $this->toaster = $container->get(Toaster::class);
        $this->router = $container->get(Router::class);
        $this->session = $container->get(SessionInterface::class);
        $this->repository = $container->get(EntityManager::class)->getRepository(User::class);
        $this->eventRepository = $manager->getRepository(Evenement::class);
        $this->interRepository = $manager->getRepository(Intervenant::class);

        $user = $this->session->get('auth');
        if ($user) {
            $this->renderer->addGlobal('user', $user);
        }
    }

    public function logView() // Permet de se connecter à la Vue Utilisateur
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
        $this->toaster->makeToast('Inscription réussie, vous pouvez désormais vous connecter pour participer aux événements de l\'épicerie', Toaster::SUCCESS);
        return $this->redirect('accueil');
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
            return $this->redirect('accueil');
        }
        $this->toaster->makeToast("Connexion échoué, merci de vérifier Email et MDP", Toaster::ERROR);
        return $this->redirect('user.login');
    }

    public function logout()
    {
        $auth = $this->container->get(UserAuth::class);
        $auth->logout();
        $this->toaster->makeToast('Déconnexion Réussie', Toaster::SUCCESS);
        return $this->redirect('accueil');
    }

    public function home()
    {
        $user = $this->session->get('auth');
        return $this->renderer->render('@user/home', [
            'user' => $user,
        ]); // Retourne la vu USER/HOME et permet de récupérer le User.Nom en même temps afin de l'afficher dans le home.html.twig
    }

    public function liste()
    {
        $inter = $this->interRepository->findAll();
        $events = $this->eventRepository->findAll();
        $sess = $this->session->get('auth');
        $user = $this->repository->find($sess->getId());

        foreach ($events as &$event) {
            $event->places_dispo = $event->getNbrPlacesDispo() - $event->getUsers()->count(); // Ici je crée un nouveau champs dans l'objet (non visible en BDD)
            $event->user_register = $event->getUsers()->contains($user);
        }

        return $this->renderer->render('@user/evenement', [
            "evenements" => $events,
            "intervenant" => $inter
        ]);
    }

    public function inscEvent(ServerRequestInterface $request)
    {

        $event = $this->eventRepository->find($request->getAttribute('id')); // Permet d'allez récupérer l'ID

        if ($event) {
            $sess = $this->session->get('auth'); // Permet d'allez récupérer la personne authentifier'
            $user = $this->repository->find($sess->getId()); // Permet de récuperer l'id de l'user auth 

            if ($event->getUsers()->count() >= $event->getNbrPlacesDispo()) { // Si le nombre d'Users inscrits a l'event est supérieur aux nbr de places dispo
                $this->toaster->makeToast('Attention nombres de participants maximum atteint', Toaster::ERROR);
                return (new Response())
                    ->withHeader('Location', '/user/listEvent');
            }

            $event->addUser($user);

            $this->manager->flush();

            $this->toaster->makeToast('Inscription à l\'événement réussi !', Toaster::SUCCESS);
        }

        return (new Response())
            ->withHeader('Location', '/user/listEvent');
    }

    public function decoEvent(ServerRequestInterface $request)
    {

        $event = $this->eventRepository->find($request->getAttribute('id'));

        if ($event) {
            $sess = $this->session->get('auth');
            $user = $this->repository->find($sess->getId());

            $event->removeUser($user);

            $this->manager->flush();

            $this->toaster->makeToast('Désinscription de l\'événement avec succès !', Toaster::SUCCESS);
        }

        return (new Response())
            ->withHeader('Location', '/user/listEvent');
    }

    public function listEvent()
    {
        $inter = $this->interRepository->findAll();

        $sess = $this->session->get('auth');
        $user = $this->repository->find($sess->getId()); // on récupére l'utilisateur qui coresspond a l'id de la session 
        $events = $user->getEvenements(); // récupération de la liste des évenement a l'aide du GET

        return $this->renderer->render('@user/listEvent', [
            "evenements" => $events, // "evenements" = nom de la variable / $response = Valeur de la variable 
            "intervenant" => $inter,
        ]);
    }

    //TODO Modifier les informations d'un utilisateurs 
    public function monCompte(ServerRequest $request)
    {
        $method = $request->getMethod();
        $sess = $this->session->get('auth');
        $user = $this->repository->find($sess->getId());

        if ($method === 'POST') {
            $data = $request->getParsedBody();
            $validator = new Validator($data);
            $errors = $validator->required('nom','prenom','mdp')
            ->getErrors();

            if ($errors) {
                foreach ($errors as $error) {
                    $this->toaster->makeToast($error->toString(), Toaster::ERROR);
                }
                return (new Response())
                ->withHeader('Location', 'user/monCompte');
            }

            $user
            ->setNom($data['nom'])
            ->setPrenom($data['prenom'])
            ->setPassword($data['mdp']);

            $this->manager->flush();
            $this->toaster->makeToast('Mise à jour réussi', Toaster::SUCCESS);

            return (new Response())
            ->withHeader('Location', '/user/monCompte'); // En cas de succès retourne la page liste d'EVENT
        }

        return $this->renderer->render('@user/monCompte');
    }
}
