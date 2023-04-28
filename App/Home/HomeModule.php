<?php

namespace App\Home;


use Model\Entity\Evenement;
use Model\Entity\Intervenant;
use Doctrine\ORM\EntityManager;
use Core\Framework\Router\Router;
use Core\Session\SessionInterface;
use Doctrine\ORM\EntityRepository;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\AbstractClass\AbstractModule;
use Core\Framework\Router\RedirectTrait;
use Core\Toaster\Toaster;



class HomeModule extends AbstractModule

{

    public const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

    private Router $router;
    private RendererInterface $renderer;
    private EntityRepository $eventRepository;
    private EntityRepository $interRepository;
    private SessionInterface $session;
    use RedirectTrait;
    private Toaster $toaster;

    public function __construct(Router $router, RendererInterface $renderer, EntityManager $manager, Toaster $toaster)
    {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->toaster = $toaster;
        $this->eventRepository = $manager->getRepository(Evenement::class);
        $this->interRepository = $manager->getRepository(Intervenant::class);

        $this->renderer->addPath('home', __DIR__ . DIRECTORY_SEPARATOR . 'view');
        $this->router->get('/', [$this, 'index'], 'accueil'); // Index Correspond a la méthode appeler 
        $this->router->get('/quiSommes', [$this, 'quiSommes'], 'quiSommes');
        $this->router->get('/dons', [$this, 'dons'], 'dons');
        $this->router->get('/contact', [$this, 'contact'], 'contact');
        $this->router->post('/contact', [$this, 'contact']);
        $this->router->get('/events', [$this, 'homeEvents'], 'events');
        $this->router->get('/mentions', [$this, 'mentions'], 'mentions');
    }

    public function index()
    {
        return $this->renderer->render(
            '@home/index',
            ['siteName' => 'Epicerie Mozart']
        );
    }

    public function quiSommes()
    {
        return $this->renderer->render('@home/quiSommes');
    }

    public function dons()
    {
        return $this->renderer->render('@home/dons');
    }

    public function contact(ServerRequestInterface $request)
    {
        $method = $request->getMethod();

        if ($method === 'POST') {

            $data = $request->getParsedBody();

            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  //GMAIL SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'neo54880@gmail.com';  // E-mail
            $mail->Password = 'vyrdwqlmxieeysgs';   // 16 character obtained from app password created
            $mail->SMTPSecure = 'ssl';             // La méthode de chiffrement
            $mail->Port = 465;                    // SMTP port

            //sender information
            $mail->setFrom($data['email_user'], $data['name']);

            //receiver email address and name
            $mail->addAddress('ivan.noblecourt@gmail.com');

            $mail->Subject = $data['subject'];
            $mail->Body    = "Message envoyer par : ". $data['email_user'] . " // " . $data['message'];

            try {
                $mail->send();
                //Création d'un toast de succès et redirection vers la page de formulaire
                $this->toaster->makeToast('Votre message a bien été envoyé', Toaster::SUCCESS);
            } catch (Exception $e) {
                //Création d'un toast d'erreur et redirection vers la page de formulaire
                $this->toaster->makeToast('Une erreur est survenu lors de l\'envoi du message', Toaster::ERROR);
            }
        }

        return $this->renderer->render('@home/contact');
    }

    public function mentions()
    {
        return $this->renderer->render('@home/mentions');
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