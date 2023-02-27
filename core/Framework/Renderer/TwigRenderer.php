<?php

namespace Core\Framework\Renderer;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRenderer implements RendererInterface

{
    private $twig;
    private $loader;


    /**
     * S'attend à une instance de FileSystemLoader et d'Environment 
     *
     * @param FilesystemLoader $loader Objet qui ressence les Chemins vers les différents dossier de Vus 
     * @param Environment $twig Objet qui enregistre nos différentes extensions et permet de faire communiquer VU et CONTROLLER
     */
    public function __construct(FilesystemLoader $loader, Environment $twig)
    {
        $this->loader = $loader;
        $this->twig = $twig;
    }

    /**
     * Permet d'enregistrer un chemin vers un ensemble de VUES
     *
     * @param string $namespace Si $path est définie $namespace réprésente un raccourci pour un alias du chemin  vers les VUES
     *  Sinon contient simplement le chemin 
     * @param string|null $path Si définie contient le chemin vers les vues qui seront enregistrer sous la valeur de $namespace 
     * @return void
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        $this->loader->addPath($path, $namespace);
    }


    /**
     * Permet d'afficher la VU qui est demander
     *
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string
    {
        return $this->twig->render($view . '.html.twig', $params);
    }


    /**
     * Permet d'ajouter des variables Globales commune a toutes les VUES 
     *
     * @param string $key
     * @param [type] $value
     * @return void
     */
    public function addGlobal(string $key, $value): void
    {
        $this->twig->addGlobal($key, $value);
    }
}
