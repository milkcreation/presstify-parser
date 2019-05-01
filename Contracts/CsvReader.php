<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Contracts;

use League\Csv\Exception;
use League\Csv\Reader;
use tiFy\Contracts\Support\Collection;

interface CsvReader extends Collection
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
    public static function createFromPath(string $path, array $params = [], ...$args): CsvReader;

    /**
     * Vérification d'existance d'une entête.
     *
     * @return boolean
     */
    public function hasHeader(): bool;

    /**
     * Récupération de la liste des colonnes.
     *
     * @return string[]
     */
    public function getHeader(): array;

    /**
     * Récupération du nombre d'éléments courant.
     *
     * @return int
     */
    public function getFounds(): int;

    /**
     * Récupération de la page courante.
     *
     * @return int
     */
    public function getPage(): int;

    /**
     * Récupération du nombre total de page.
     *
     * @return int
     */
    public function getPages(): int;

    /**
     * Récupération du nombre d'élément par page.
     *
     * @return int
     */
    public function getPerPage(): int;

    /**
     * Récupération de l'instance du controleur de traitement.
     *
     * @return Reader
     */
    public function getReader(): Reader;

    /**
     * Récupération du nombre total d'éléments.
     *
     * @return int
     */
    public function getTotal(): int;

    /**
     * Définition de la convertion d'encodage des résultats.
     *
     * @param array $encoding {
     *      @type string $input Encodage à l'entrée.
     *      @type string $output Encodage à la sortie.
     * }
     *
     * @return static
     */
    public function setEncoding(array $encoding): CsvReader;

    /**
     * Définition de la page courante de récupération des éléments.
     *
     * @param int $page
     *
     * @return static
     *
     * @throws Exception
     */
    public function page(int $page): CsvReader;

    /**
     * Récupération de la liste des éléments sous forme de tableau.
     *
     * @return array
     */
    public function toArray(): array;
}