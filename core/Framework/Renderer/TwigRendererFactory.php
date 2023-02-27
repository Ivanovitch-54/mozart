<?php
namespace Core\Framework\Renderer;


use Psr\Container\ContainerInterface;
use Core\Framework\Renderer\TwigRenderer;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Instancie notre Twig Renderer
 */
class TwigRendererFactory {

    /**
     * Methode magique "__invoke" qui est appelé au moment ou l'on essaye d'utiliser l'objet comme s'il s'agissait d'une fonction 
     * Exemple: $twig = TwigRendererFactory() 
     *
     * @param ContainerInterface $container
     * @return TwigRenderer|null
     */
    public function __invoke(ContainerInterface $container): ?TwigRenderer 
    {
        $loader = new FilesystemLoader($container->get('config.viewPath'));
        $twig = new Environment($loader, []);

        // Récupére la liste d'extensions Twig à charger 
        $extensions = $container->get("twig.extensions");

        // Boucle sur la liste d'extensions et ajout à Twig
        foreach ($extensions as $extension) {
            $twig->addExtension($container->get($extension));
        }

        return new TwigRenderer($loader, $twig);
    }
}