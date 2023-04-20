<?php
use Core\Db\DataBaseFactory;
use Doctrine\ORM\EntityManager;
use Core\Framework\Router\Router;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Renderer\TwigRendererFactory;
use Core\Framework\Router\RouterTwigExtension;
use Core\Framework\Security\CSRFTwigExtension;
use Core\Framework\TwigExtensions\AssetsTwigExtension;
use Core\Session\PHPSession;
use Core\Session\SessionInterface;
use Core\Toaster\Toaster;
use Core\Toaster\ToasterTwigExtension;


return [
    "doctrine.user" => "root",
    "doctrine.dbname" => "mozart",
    "doctrine.mdp" => "",
    "doctrine.driver" => "pdo_mysql",
    "doctrine.devMode" => true,
    "config.viewPath"  => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'view',
    "twig.extensions" => [
        RouterTwigExtension::class,
        ToasterTwigExtension::class,
        AssetsTwigExtension::class,
        CSRFTwigExtension::class
    ],

    Router::class => \DI\create(),
    SessionInterface::class => \DI\get(PHPSession::class),
    RendererInterface::class => \DI\factory(TwigRendererFactory::class),
    EntityManager::class => \DI\factory(DataBaseFactory::class),
    Toaster::class => \DI\autowire(),
];

