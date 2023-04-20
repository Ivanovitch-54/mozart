<?php

use Core\App;
use App\Home\HomeModule;
use DI\ContainerBuilder;
use App\Admin\AdminModule;
use App\User\UserModule;

use function Http\Response\send;
use GuzzleHttp\Psr7\ServerRequest;
use Core\Framework\Middleware\RouterMiddleware;
use Core\Framework\Middleware\NotFoundMiddleware;
use Core\Framework\Middleware\AdminAuthMiddleware;
use Core\Framework\Middleware\CSRFMiddleware;
use Core\Framework\Middleware\TrailingSlashMiddleware;


use Core\Framework\Middleware\RouterDispatcherMiddleware;
use Core\Framework\Middleware\UserAuthMiddleware;

// Inclusion de l'AUTOLOADER de COMPOSER
require dirname(__DIR__) . "/vendor/autoload.php";

// Déclaration du tableau de MODULE à charger 
$modules = [
    HomeModule::class,
    AdminModule::class,
    UserModule::class,
];

// Instanciation du BUILDER de CONTENEUR de DEPENDANCES, le BUILDER permet de construire l'objet de container de DEPENDANCE  
// Mais ce n'est pas le container de DEPENDANCE
$builder = new ContainerBuilder();

// Ajout de la feuille de configuration principale 
$builder->addDefinitions(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');

foreach ($modules as $module) {
    if (!is_null($module::DEFINITIONS)) {
        // Si les MODULES possédent une feuille de configuration personallisé on l'ajoute également 
        $builder->addDefinitions($module::DEFINITIONS);
    }
}

// On récupère l'instance du container de dépendance 
$container = $builder->build();

// On instancie notre application en lui passant la liste des modules et le container de dépendances
$app = new App($container, $modules);

// On LINK le premier Middleware de la chaîne de responsabilité à l'application 
// Puis on ajoute les Middleware suivants en leurs passant le container de dépendances si besoin 
$app->linkFirst(new TrailingSlashMiddleware())
    ->linkWith(new RouterMiddleware($container))
    ->linkWith(new CSRFMiddleware($container, []))
    ->linkWith(new AdminAuthMiddleware($container))
    ->linkWith(new UserAuthMiddleware($container))
    ->linkWith(new RouterDispatcherMiddleware())
    ->linkWith(new NotFoundMiddleware);


// Si l'index n'est pas executer à partir de la CLI (Command Line Interface)
if (php_sapi_name() !== 'cli') {
    // On récupère la réponse de notre application en lançant la methode 'run' et en lui passant un objet ServerRequest 
    // Rempli avec toutes les informations de la requete envoyé par la machine Client 
    $reponse = $app->run(ServerRequest::fromGlobals());
    // On renvoi la réponse au server après avoir tranformer le retour de l'application en une réponse compréhensible par la machine client 
    send($reponse);
}

