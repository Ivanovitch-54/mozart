<?php

namespace App\Admin\Action;
// AdministratorAction == AdminAction 
use DateTime;
use Core\Toaster\Toaster;
use Model\Entity\Evenement;
use GuzzleHttp\Psr7\Response;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Container\ContainerInterface;
use Core\Framework\Validator\Validator;
use Core\Framework\Router\RedirectTrait;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Renderer\RendererInterface;
use Model\Entity\Intervenant;

class AdministratorAction
{
    use RedirectTrait;
    private ContainerInterface $container;
    private RendererInterface $renderer;
    private $repository;
    private Toaster $toaster;
    private EntityManager $manager;
    private $interRepository;

    public function __construct(ContainerInterface $container, RendererInterface $renderer, EntityManager $manager, Toaster $toaster)
    {
        $this->container = $container;
        $this->manager = $manager;
        $this->renderer = $renderer;
        $this->toaster = $toaster;
        $this->repository = $manager->getRepository(Evenement::class);
        $this->interRepository = $manager->getRepository(Intervenant::class);

    }

    public function home(ServerRequest $request)
    {
        return $this->renderer->render('@admin/home');
    }

    public function event(ServerRequestInterface $request)
    {
        $events = $this->repository->findAll();

        return $this->renderer->render('@admin/event', [
            "evenements" => $events
        ]);
    }

    public function addEvent(ServerRequestInterface $request)
    {
        $method = $request->getMethod();

        if ($method === 'POST') {
            $data = $request->getParsedBody(); // On récupère le contenu de $_POST (les valeurs saisis dans le formualaire)
            $evenements = $this->repository->findAll();
            $validator = new Validator($data); // On instancie le Validator en lui passant le tableau de données à valider 
            $errors = $validator
                ->required('nom', 'description', 'startAt', 'endAt', 'intervenant', 'nbr_places_dispo')
                ->getErrors();

            if ($errors) {
                foreach ($errors as $error) {
                    $this->toaster->makeToast($error->toString(), Toaster::ERROR);
                }
                return $this->redirect('event.add');
            }

            foreach ($evenements as $evenement) {
                if ($evenement->getNom() === $data['nom']) { // Ici je vérifie si le nom de l'événement rentrer n'existe pas dèja en BDD

                    $this->toaster->makeToast('Cette événement existe déjà', Toaster::ERROR); // Créer et affiche le Toast "ERREUR"

                    return $this->renderer->render('@admin/addEvent'); // Retourne la page addEvent
                }
            }
            // Ici j'instancie un nouveau événement contenu dans la variable $new 
            $new = new Evenement();
            $intervenant = $this->interRepository->find($data['intervenant']);
            // Test

            $new->setNom($data['nom'])
                ->setDescription($data['description'])
                ->addIntervenant($intervenant)
                ->setStartAt(new DateTime($data['startAt']))
                ->setEndAt(new DateTime($data['endAt']))
                ->setNbrPlacesDispo($data['nbr_places_dispo']);

            $this->manager->persist($new);
            $this->manager->flush();
            $this->toaster->makeToast('Nouvelle événement enregistrer avec succès', Toaster::SUCCESS);

            return (new Response())
                ->withHeader('Location', '/admin/event');
        }
        $intervenants = $this->interRepository->findAll();
        return $this->renderer->render('@admin/addEvent',[
            "intervenants" => $intervenants
        ]);
    }

    public function delete(ServerRequestInterface $request): Response
    {
        $id = $request->getAttribute('id'); // Permet de récup l'ID de l'event
        $event = $this->repository->find($id); // Permet de récup l'event en fonction de son ID

        $this->manager->remove($event); // Supprime l'event
        $this->manager->flush(); // Applique la suppresion 

        $this->toaster->makeToast('Evenement supprimer avec succès', Toaster::SUCCESS);

        return (new Response())
            ->withHeader('Location', '/admin/event');
    }


    // Mise à jour d'Evenement //
    public function update(ServerRequestInterface $request)
    {
        $id = $request->getAttribute('id'); // Pour allez recup l'id de l'event 
        $event = $this->repository->find($id); // Récup l'Event selon son ID
        $method = $request->getMethod();

        if ($method === 'POST') {
            $data = $request->getParsedBody(); // Récup les datas envoyer en POST
            $validator = new Validator($data);
            $errors = $validator->required('nom', 'description', 'startAt', 'endAt')
                ->getErrors();
            if ($errors) {
                foreach ($errors as $error) {
                    $this->toaster->makeToast($error->toString(), Toaster::ERROR);
                }
                return (new Response())
                    ->withHeader('Location', '/admin/updateEvent');
            }
            $event->setNom($data['nom'])
                ->setDescription($data['description'])
                ->setStartAt(new DateTime($data['startAt']))
                ->setEndAt(new DateTime($data['endAt']))
                ->setNbrPlacesDispo($data['nbr_places_dispo']);

            $this->manager->flush();
            $this->toaster->makeToast('Mise à jour de l\'événement réussi', Toaster::SUCCESS);

            return (new Response())
                ->withHeader('Location', '/admin/event'); // En cas de succès retourne la page liste d'EVENT
        }
        return $this->renderer->render('@admin/updateEvent', [
            "evenement" => $event
        ]);
    }

    // A partir d'ici je gère les intervenants 

    public function showInter (ServerRequestInterface $request)
    {
        $intervenants = $this->interRepository->findAll();

        return $this->renderer->render('@admin/inter', [
            "intervenants" => $intervenants
        ]);
    }

    public function addInter(ServerRequestInterface $request)
    {
        $method = $request->getMethod();

        if ($method === 'POST') {
            $data = $request->getParsedBody();
            $intervenants = $this->interRepository->findAll();
            $validator = new Validator($data);
            $errors = $validator
            ->required('nom','prenom','role')
            ->getErrors();

            if ($errors) {
                foreach ($errors as $error) {
                    $this->toaster->makeToast($error->toString(), Toaster::ERROR);
                }
                return $this->redirect('inter.add');
            }

            foreach ($intervenants as $intervenant) {
                if ($intervenant->getNom() === $data['nom']){
                    $this->toaster->makeToast('Intervenant déjà enregistrer', Toaster::ERROR);
                    return $this->renderer->render('@admin/addInter');
                }
            }

            $new = new Intervenant();
            $new->setNom($data['nom'])
                ->setPrenom($data['prenom'])
                ->setRole($data['role']);

            $this->manager->persist($new);
            $this->manager->flush();
            $this->toaster->makeToast('Nouvelle Intervenant enregistrer avec succès', Toaster::SUCCESS);

            return (new Response())
            ->withHeader('Location', '/admin/inter');
        }
        return $this->renderer->render('@admin/addInter');
    }

    public function deleteInter(ServerRequestInterface $request): Response
    {
        $id = $request->getAttribute('id'); // Permet de récup l'ID de l'event
        $intervenant = $this->interRepository->find($id); // Permet de récup l'event en fonction de son ID

        $this->manager->remove($intervenant); // Supprime l'event
        $this->manager->flush(); // Applique la suppresion 

        $this->toaster->makeToast('Intervenant supprimer avec succès', Toaster::SUCCESS);

        return (new Response())
            ->withHeader('Location', '/admin/inter');
    }
}
