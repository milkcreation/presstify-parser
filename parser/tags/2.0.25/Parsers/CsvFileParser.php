<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Parsers;

use Exception;
use League\Csv\{
    CharsetConverter as LeagueCsvCharsetConverter,
    Reader as LeagueCsvReader,
    Statement as LeagueCsvStatement
};
use tiFy\Plugins\Parser\{
    Contracts\CsvFileParser as CsvFileParserContract,
    Contracts\FileParser as FileParserContract,
    FileParser
};

class CsvFileParser extends FileParser implements CsvFileParserContract
{
    /**
     * Caractère de délimitation des colonnes.
     * @var string
     */
    protected $delimiter = ',';

    /**
     * Attribut d'encodage en entrée et en sortie.
     * @var string[]
     */
    protected $encoding = ['UTF-8', 'UTF-8'];

    /**
     * Caractère d'encapsulation des données.
     * @var string
     */
    protected $enclosure = '"';

    /**
     * Caractère d'échappemment des données.
     * @var string
     */
    protected $escape = '\\';

    /**
     * Entête.
     * {@internal
     * - array > Tableau indexé. Indice de qualification des colonnes utilisées pour indéxer la valeur des éléments.
     * - true > La première ligne d'enregistrement est utilisée pour indexer la valeur des élements.
     * - false > Les élements sont indexés numériquement. Tableau indexé.
     * }
     * @var int|false
     */
    protected $header = false;

    /**
     * @inheritDoc
     */
    public function parse(): FileParserContract
    {
        try {
            $this->stream = $this->open();

            $adapter = LeagueCsvReader::createFromStream($this->stream)
                ->setDelimiter($this->delimiter)
                ->setEnclosure($this->enclosure)
                ->setEscape($this->escape);

            LeagueCsvCharsetConverter::addTo($adapter, $this->encoding[0], $this->encoding[1]);

            $header = [];
            if ($this->header !== false) {
                if (is_int($this->header)) {
                    $header = $adapter->setHeaderOffset($this->header ?:0)->getHeader();
                } elseif(is_array($this->header)) {
                    $header = $this->header;
                }
            }

            $results = (new LeagueCsvStatement())->process($adapter, $header);

            $this->records = iterator_to_array($results->getRecords());
        } catch (Exception $e) {
            throw $e;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDelimiter(string $delimiter): CsvFileParserContract
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setEncoding(array $encoding): CsvFileParserContract
    {
        $this->encoding = [$encoding[0] ?? 'utf-8', $encoding[1] ?? 'utf-8'];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setEnclosure(string $enclosure): CsvFileParserContract
    {
        $this->enclosure = $enclosure;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setEscape(string $escape): CsvFileParserContract
    {
        $this->escape = $escape;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setHeader($header): CsvFileParserContract
    {
        $this->header = $header;

        return $this;
    }
}