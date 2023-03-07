<?php

namespace App\Evenement\Action;

use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Router\Router;
use Core\Toaster\Toaster;
use Doctrine\ORM\EntityManager;
use Model\Entity\Evenement;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class EventAction
{
    private RendererInterface $renderer;

    private EntityManager $manager;

    private Toaster $toaster;

    private ContainerInterface $container;

    private Router $router;

    private $repository;

    public function __construct(RendererInterface $renderer, EntityManager $manager, Toaster $toaster, ContainerInterface $container)
    {
        $this->renderer = $renderer;
        $this->container = $container;
        $this->router = $container->get(Router::class);
        $this->manager = $manager;
        $this->toaster = $toaster;
        $this->repository = $manager->getRepository(Evenement::class);
    }

    public function showEvent(ServerRequestInterface $request)
    {
        $events = $this->repository->findAll();

        return $this->renderer->render('@Evenement/event', [
            "evenements" => $events
        ]);
    }
}
