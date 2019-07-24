<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Xml;

use Rodenastyle\StreamParser\{Parsers\XMLParser, StreamParserInterface};
use tiFy\Plugins\Parser\Contracts\XmlReader;
use XMLReader as xml;
use tiFy\Support\Collection;

class Reader extends Collection implements XmlReader
{
    /**
     * Instance du controleur de traitement.
     * @return XMLParser|StreamParserInterface
     */
    private $_parser;

    /**
     * Indicateur d'intégrité du controleur.
     * @var boolean
     */
    private $_prepared = false;

    /**
     * Liste des complète des éléments du XML.
     * @var array
     */
    protected $records = [];

    /**
     * Liste des éléments courants.
     * @var array|null
     */
    protected $items;

    /**
     * @inheritDoc
     */
    public static function createFromPath(string $path, array $params = [], ...$args): XmlReader
    {
        return (new static())->prepare((new XMLParser())->from($path), $params)->fetchItems();
    }

    /**
     * Récupération de la liste des éléments courant.
     *
     * @return $this
     *
     * @throws Exception
     * @throws CsvException
     */
    protected function fetchItems(): XmlReader
    {
        if (is_null($this->items)) {
            $this->items = $this->records;
        }

        return $this;
    }

    /**
     * Préparation du controleur.
     *
     * @param XMLParser $parser
     * @param array $params Liste des paramètres.
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function prepare(XMLParser $parser, array $params = []): XmlReader
    {
        if (!$this->_prepared) {
            $this->_parser = $parser;

            $this->_parser->each(function ($item) {
                $this->records[] = $item->all();
            });

            $this->_prepared = true;
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        try {
            $this->fetchItems();
            return $this->all() ?: [];
        } catch (Exception $e) {
            return [];
        }
    }
}