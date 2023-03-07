<?php 

namespace App\Evenement;

use App\Evenement\Action\EventAction;
use Core\Framework\AbstractClass\AbstractModule;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Router\Router;
use Psr\Container\ContainerInterface;

class EvenementModule extends AbstractModule
{
    private Router $router;

    private RendererInterface $renderer;

    public const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

    public function __construct(ContainerInterface $container)
    {
        $this->router = $container->get(Router::class);
        $this->renderer = $container->get(RendererInterface::class);
        $eventAction = $container->get(EventAction::class);

        $this->renderer->addPath('Evenement',__DIR__ . DIRECTORY_SEPARATOR . 'view'); 

        $this->router->get('/event', [$eventAction, 'showEvent'], 'evenement'); // 1er param (/event correspond au chemin de la route) 2eme et 3eme param fonction à appeler, en dernier nom de la route
    }
}

