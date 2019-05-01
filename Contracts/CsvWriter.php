<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Contracts;

use League\Csv\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;
use tiFy\Plugins\Parser\Exceptions\CsvException;
use Traversable;

interface CsvWriter
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
    public static function createFromPath(?string $path = null, array $params = [], ...$args): CsvWriter;

    /**
     * Résolution de la classe sous la forme d'une chaîne de caractères.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Ajout d'une ligne.
     *
     * @param array $line
     *
     * @return $this
     *
     * @throws CsvException
     */
    public function addLine(array $line): CsvWriter;

    /**
     * Ajout de plusieurs lignes.
     *
     * @param Traversable|array $lines
     *
     * @return $this
     *
     * @throws CsvException
     */
    public function addLines($lines): CsvWriter;

    /**
     * Téléchargement du fichier.
     *
     * @param string|null $name Nom de qualification du fichier.
     * @param array $headers Liste des entêtes complémentaires.
     *
     * @return StreamedResponse
     */
    public function download(string $name = 'file.csv', array $headers = []): StreamedResponse;

    /**
     * Récupération de l'instance du controleur de traitement.
     *
     * @return Writer
     */
    public function getWriter(): Writer;

    /**
     * Définition de la convertion d'encodage des résultats.
     *
     * @param array $encoding {
     *
     * @type string $input Encodage à l'entrée.
     * @type string $output Encodage à la sortie.
     * }
     *
     * @return static
     */
    public function setEncoding(array $encoding): CsvWriter;

    /**
     * Définition de la cartographie des messages d'erreurs.
     *
     * @param string[][] $errors Tableau associatif de la liste des messages d'erreurs.
     *
     * @return static
     */
    public function setErrors(array $errors = []): CsvWriter;

    /**
     * Génération de la réponse HTTP.
     *
     * @param string|null $name Nom de qualification du fichier.
     * @param array $headers Liste des entêtes complémentaires.
     * @param string $disposition type de disposition. inline|attachment.
     *
     * @return StreamedResponse
     */
    public function response(
        string $name = 'file.csv',
        array $headers = [],
        string $disposition = 'inline'
    ): StreamedResponse;
}