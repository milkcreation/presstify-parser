<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Contracts;

use Illuminate\Support\Collection as LaraCollection;
use tiFy\Contracts\Support\Collection;

interface Reader extends Collection
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
    public static function createFromPath(string $path, array $params = [], ...$args): Reader;

    /**
     * Récupération de la liste des éléments courant.
     *
     * @return static
     */
    public function fetch(): Reader;

    /**
     * Récupération de la liste des éléments courant associé à une page.
     *
     * @param int $page
     *
     * @return static
     */
    public function fetchForPage(int $page = 1): Reader;

    /**
     * Récupération de la liste complète des enregistrements.
     *
     * @return static
     */
    public function fetchRecords(): Reader;

    /**
     * Vérification d'existance d'une colonne de clés primaires d'indexation des éléments.
     *
     * @return boolean
     */
    public function hasPrimary(): bool;

    /**
     * Récupération du nombre d'éléments courant.
     *
     * @return int
     */
    public function getCount(): int;

    /**
     * Récupération du numéro de la dernière page.
     *
     * @return int
     */
    public function getLastPage(): int;

    /**
     * Récupération de la ligne de démarrage du traitement.
     *
     * @return int
     */
    public function getOffset(): int;

    /**
     * Récupération de la page courante.
     *
     * @return int
     */
    public function getPage(): int;

    /**
     * Récupération de l'instance du controleur de traitement.
     *
     * @return FileParser
     */
    public function getParser(): FileParser;

    /**
     * Récupération du nombre d'élément par page.
     *
     * @return int|null
     */
    public function getPerPage(): ?int;

    /**
     * Récupération de la colonne de clés primaires d'indexation des éléments.
     *
     * @return int|string|null
     */
    public function getPrimary();

    /**
     * Récupération de la liste complète des enregistrements.
     *
     * @return LaraCollection|null
     */
    public function getRecords(): ?LaraCollection;

    /**
     * Récupération du nombre total d'éléments.
     *
     * @return int
     */
    public function getTotal(): int;

    /**
     * Définition du nombre d'éléments courants trouvés.
     *
     * @param int $count
     *
     * @return static
     */
    public function setCount(int $count): Reader;

    /**
     * Définition du numéro de la dernière page.
     *
     * @param int $last_page
     *
     * @return static
     */
    public function setLastPage(int $last_page): Reader;

    /**
     * Définition de la ligne de démarrage du traitement de récupération des éléments.
     *
     * @param int $offset
     *
     * @return static
     */
    public function setOffset(int $offset): Reader;

    /**
     * Définition de la page courante de récupération des éléments.
     *
     * @param int $page
     *
     * @return static
     */
    public function setPage(int $page): Reader;

    /**
     * Définition de la liste des paramètres.
     *
     * @param array $params Liste des paramètres.
     *
     * @return static
     */
    public function setParams(array $params = []): Reader;

    /**
     * Définition de l'instance de la classe de traitement du fichier source.
     *
     * @param FileParser $parser
     *
     * @return static
     */
    public function setParser(FileParser $parser): Reader;

    /**
     * Définition du nombre total d'éléments par page.
     *
     * @param int $per_page
     *
     * @return static
     */
    public function setPerPage(int $per_page): Reader;

    /**
     * Définition de la colonne de clé primaire.
     *
     * @param string|int $primary Indice ou Nom de qualification de la colonne de clé primaire.
     *
     * @return static
     */
    public function setPrimary($primary): Reader;

    /**
     * Définition du nombre total d'éléments.
     *
     * @param int $total
     *
     * @return static
     */
    public function setTotal(int $total): Reader;

    /**
     * Récupération de la liste des éléments sous forme de tableau.
     *
     * @return array
     */
    public function toArray(): array;
}