<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Contracts;

use tiFy\Contracts\Support\Collection;

interface XmlReader extends Collection
{
    /**
     * Création d'une instance basé sur un chemin.
     *
     * @param string $path Chemin vers le fichier à traiter.
     * @param array $params Liste des paramètres de configuration.
     * @param array $args Liste des arguments dynamiques fopen. Seuls mode et context sont permis.
     *
     * @return static
     */
    public static function createFromPath(string $path, array $params = [], ...$args): XmlReader;
}