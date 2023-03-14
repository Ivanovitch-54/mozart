<?php
namespace App\Home;

use Model\Entity\Evenement;
use Model\Entity\Intervenant;
use Doctrine\ORM\EntityManager;
use Core\Framework\Router\Router;
use Doctrine\ORM\EntityRepository;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\AbstractClass\AbstractModule;

class HomeModule extends AbstractModule

{

    public const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

    private Router $router;
    private RendererInterface $renderer;
    // TEST
    private EntityRepository $eventRepository;
    private EntityRepository $interRepository;

    public function __construct(Router $router, RendererInterface $renderer, EntityManager $manager)
    {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->eventRepository = $manager->getRepository(Evenement::class);
        $this->interRepository = $manager->getRepository(Intervenant::class);

        $this->renderer->addPath('home', __DIR__ . DIRECTORY_SEPARATOR . 'view');
        $this->router->get('/', [$this, 'index'], 'accueil'); // Index Correspond a la méthode appeler 
        $this->router->get('/quiSommes', [$this, 'quiSommes'], 'quiSommes'); 
        $this->router->get('/dons', [$this, 'dons'], 'dons'); 
        $this->router->get('/contact', [$this, 'contact'], 'contact'); 
        $this->router->get('/events', [$this, 'homeEvents'], 'events'); 

    }

    public function index()
    {
        return $this->renderer->render('@home/index',
    ['siteName' => 'Epicerie Mozart']);
    }

    public function quiSommes()
    {
        return $this->renderer->render('@home/quiSommes');
    }

    public function dons()
    {
        return $this->renderer->render('@home/dons');
    }

    public function contact()
    {
        return $this->renderer->render('@home/contact');
    }

    public function homeEvents()
    {
        $inter = $this->interRepository->findAll();
        $events = $this->eventRepository->findAll();

        foreach ($events as &$event) {
            $event->places_dispo = $event->getNbrPlacesDispo() - $event->getUsers()->count(); // Ici je crée un nouveau champs dans l'objet (non visible en BDD)
        }

        return $this->renderer->render('@home/events', [
            "evenements" => $events,
            "intervenant" => $inter
        ]);
    }
}

// Cet extrait de code fait partie d'une application PHP utilisant des concepts tels que l'injection de dépendances et l'utilisation de modèles de conception tels que le pattern MVC.
// Il définit une classe appelée HomeModule qui appartient au namespace App\Home.

// La classe a deux propriétés privées, $router et $renderer, qui sont injectées lors de la création d'une instance de cette classe.
// Ces propriétés sont des objets de la classe Router et RendererInterface respectivement.

// Dans le constructeur de la classe, une méthode addPath() est appelée sur l'objet $renderer pour ajouter un chemin d'accès aux vues du module.
// Ensuite, un itinéraire est ajouté à l'objet $router en utilisant la méthode get().
// Cet itinéraire est défini pour le chemin racine '/', et la méthode index() de cette classe est spécifiée comme la fonction de rappel à utiliser lorsque cet itinéraire est appelé.
// La méthode index() utilise l'objet $renderer pour rendre la vue '@home/index'.