<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Contracts;

use League\Csv\Exception as LeagueCsvException;

interface CsvReader extends Reader
{
    /**
     * Définition du caractère de délimitation des colonnes.
     *
     * @param string $delimiter
     *
     * @return static
     *
     * @throws LeagueCsvException
     */
    public function setDelimiter(string $delimiter): CsvReader;

    /**
     * Définition de la convertion d'encodage des résultats.
     *
     * @param array $encoding {
     *      @type string $input Encodage à l'entrée.
     *      @type string $output Encodage à la sortie.
     * }
     *
     * @return static
     *
     * @throws LeagueCsvException
     */
    public function setEncoding(array $encoding): CsvReader;

    /**
     * Définition du caractère d'encapsulation des données.
     *
     * @param string $enclosure
     *
     * @return static
     *
     * @throws LeagueCsvException
     */
    public function setEnclosure(string $enclosure): CsvReader;

    /**
     * Définition du caractère d'échappemment des données.
     *
     * @param string $escape
     *
     * @return static
     *
     * @throws LeagueCsvException
     */
    public function setEscape(string $escape): CsvReader;
}