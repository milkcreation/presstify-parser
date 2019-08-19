<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Json;

use Illuminate\Support\Collection as LaraCollection;
use Rodenastyle\StreamParser\{Parsers\JSONParser as Parser, StreamParserInterface};
use tiFy\Plugins\Parser\{
    AbstractReader,
    Contracts\JsonReader,
    Contracts\Reader as BaseReaderContract
};
use Tightenco\Collect\Support\Collection as TightencoCollection;

/**
 * USAGE :
 * ---------------------------------------------------------------------------------------------------------------------
 use tiFy\Plugins\Parser\Json\Reader

 $reader = Reader::createFromPath(
    VENDOR_PATH . '/presstify-plugins/parser/Resources/sample/sample.json', [
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
class Reader extends AbstractReader implements JsonReader
{
    /**
     * Instance du controleur de traitement.
     * @return Parser|StreamParserInterface
     */
    private $_parser;

    /**
     * CONSTRUCTEUR.
     *
     * @param Parser|StreamParserInterface $parser
     *
     * @return void
     */
    public function __construct(StreamParserInterface $parser)
    {
        $this->_parser = $parser;

        TightencoCollection::macro('recursive', function () {
            return $this->map(function ($value) {
                if (is_array($value) || is_object($value)) {
                    return (new TightencoCollection($value))->recursive();
                }
                return $value;
            });
        });
    }

    /**
     * Récupération de l'instance du controleur de traitement.
     *
     * @return Parser
     */
    protected function getParser(): Parser
    {
        return $this->_parser;
    }

    /**
     * @inheritDoc
     */
    public static function createFromPath(string $path, array $params = [], ...$args): BaseReaderContract
    {
        return (new static((new Parser())->from($path)))->setParams($params)->fetch();
    }

    /**
     * @inheritDoc
     */
    public function fetchRecords(): BaseReaderContract
    {
        if (is_null($this->records)) {
            $records = [];
            $this->getParser()->each(function (TightencoCollection $item) use (&$records) {
                $records[] = $item->all();
            });
            $this->records = new LaraCollection($records);
            $this->setTotal($this->records->count() - $this->getOffset());
        }

        return $this;
    }
}