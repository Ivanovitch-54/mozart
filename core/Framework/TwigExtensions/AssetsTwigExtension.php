<?php

namespace Core\Framework\TwigExtensions;

use Exception;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;


/**
 * Extension Twig permettant d'accèder directement au dossier public 
 * utile pour donner les chemins des feuilles de style, des scripts js, des images et de toutr ce qui peut se trouver dans le dossier Assets
 */
class AssetsTwigExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('assets', [$this, 'asset']) // Permet de renommer assets coter twig pour utliser la fonction asset ici présente
            , new TwigFunction('absolute', [$this, 'absolutePath'])
        ];
    }

    public function asset(string $path): string
    {
        $file = dirname(__DIR__, 3) . '/public/' . $path;
        if (!file_exists($file)) {
            throw new Exception("Le fichier $file n'existe pas.");
        }
        $path .= '?' . filemtime($file);
        return $path;
    }

    public function absolutePath(string $path): string
    {
        $absolutePath = "http://localhost:8000/" . $path;
        return $absolutePath;
    }
}
