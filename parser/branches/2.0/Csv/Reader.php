<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Csv;

use Exception;
use Illuminate\Support\Collection as LaraCollection;
use League\Csv\{
    CharsetConverter as LeagueCsvCharsetConverter,
    Reader as LeagueCsvReader,
    Statement as LeagueCsvStatement
};
use tiFy\Plugins\Parser\{
    AbstractReader,
    Contracts\CsvReader,
    Contracts\Reader as BaseReaderContract
};

/**
 * USAGE :
 * ---------------------------------------------------------------------------------------------------------------------
 use tiFy\Plugins\Parser\Csv\Reader

 $reader = Reader::createFromPath(
    VENDOR_PATH . '/presstify-plugins/parser/Resources/sample/sample.csv', [
    'delimiter'     => ',',
    'enclosure'     => '"',
    'escape'        => '\\',
    'header'        => ['lastname', 'firstname', 'email'],
    'offset'        => 1,
    'primary'       => 'lastname',
    'page'          => 1,
    'per_page'      => -1
 ]);

 // Récupération du tableau de la liste des éléments courants.
 // @var array
 $reader->toArray();

 // Récupération la liste des éléments de la page 2.
 // @var array
 $reader->fetchForPage(2)->all();

 // Récupération du nombre total de resultats.
 // @var int
 $reader->getTotal();

 // Récupération du numéro de la dernière de page.
 // @var int
 $reader->getLastPage();

 // Récupération du nombre d'éléments courants.
 // @var int
 $reader->getCount();
 */
class Reader extends AbstractReader implements CsvReader
{
    /**
     * Instance du controleur de traitement.
     * @var LeagueCsvReader|null
     */
    private $_parser;

    /**
     * Caractère de délimitation des colonnes.
     * @var string
     */
    protected $delimiter = ',';

    /**
     * Attribut d'encodage en entrée et en sortie.
     * @var string[]
     */
    protected $encoding = [];

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
     * CONSTRUCTEUR.
     *
     * @param LeagueCsvReader $parser
     *
     * @return void
     */
    public function __construct(LeagueCsvReader $parser)
    {
        $this->_parser = $parser;
    }

    /**
     * Récupération de l'instance du controleur de traitement.
     *
     * @return LeagueCsvReader
     */
    protected function getParser(): LeagueCsvReader
    {
        return $this->_parser;
    }

    /**
     * {@inheritDoc}
     *
     * @return CsvReader
     */
    public static function createFromPath(string $path, array $params = [], ...$args): BaseReaderContract
    {
        array_unshift($args, $path);

        return (new static(LeagueCsvReader::createFromPath(...$args)))->setParams($params)->fetch();
    }

    /**
     * @inheritDoc
     */
    public function fetchRecords(): BaseReaderContract
    {
        if (is_null($this->records)) {
            $records = (new LeagueCsvStatement())->process($this->getParser(), $this->getHeader());
            $this->records = new LaraCollection(iterator_to_array($records));
            $this->setTotal($this->records->count() - $this->getOffset());
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDelimiter(string $delimiter): CsvReader
    {
        $this->delimiter = $delimiter;
        $this->getParser()->setDelimiter($this->delimiter);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setEncoding(array $encoding): CsvReader
    {
        $this->encoding = [$encoding[0] ?? 'utf-8', $encoding[1] ?? 'utf-8'];
        LeagueCsvCharsetConverter::addTo($this->getParser(), $this->encoding[0], $this->encoding[1]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setEnclosure(string $enclosure): CsvReader
    {
        $this->enclosure = $enclosure;
        $this->getParser()->setEnclosure($this->enclosure);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setEscape(string $escape): CsvReader
    {
        $this->escape = $escape;
        $this->getParser()->setEscape($this->escape);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function setParams(array $params = []): BaseReaderContract
    {
        foreach ($params as $key => $param) {
            switch ($key) {
                case 'delimiter' :
                    $this->setDelimiter($param);
                    break;
                case 'encoding' :
                    $this->setEncoding($param);
                    break;
                case 'enclosure' :
                    $this->setEnclosure($param);
                    break;
                case 'escape' :
                    $this->setEscape($param);
                    break;
                case 'header' :
                    $this->header = $param;
                    break;
                case 'offset' :
                    $this->setOffset($param);
                    break;
                case 'page' :
                    $this->setPage($param);
                    break;
                case 'per_page' :
                    $this->setPerPage($param);
                    break;
            }
        }

        if ($this->hasHeader()) {
            if ( ! $this->getHeader()) {
                $this->getParser()->setHeaderOffset(0);
                $this->header = $this->getParser()->getHeader();
            }
        }

        if (isset($params['primary'])) {
            $this->setPrimary($params['primary']);
        }

        return $this;
    }
}