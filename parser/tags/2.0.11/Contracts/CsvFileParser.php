<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Contracts;

interface CsvFileParser extends FileParser
{
    /**
     * Définition du caractère de délimitation des colonnes.
     *
     * @param string $delimiter
     *
     * @return static
     */
    public function setDelimiter(string $delimiter): CsvFileParser;

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
    public function setEncoding(array $encoding): CsvFileParser;

    /**
     * Définition du caractère d'encapsulation des données.
     *
     * @param string $enclosure
     *
     * @return static
     */
    public function setEnclosure(string $enclosure): CsvFileParser;

    /**
     * Définition du caractère d'échappemment des données.
     *
     * @param string $escape
     *
     * @return static
     */
    public function setEscape(string $escape): CsvFileParser;

    /**
     * Définition de l'activation de l'entête.
     *
     * @param int|false $header Activation|indice de la ligne d'enregistrement.
     *
     * @return static
     */
    public function setHeader($header): CsvFileParser;
}