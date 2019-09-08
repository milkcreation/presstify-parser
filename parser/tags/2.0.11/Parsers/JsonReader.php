<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Parsers;

use tiFy\Plugins\Parser\{
    AbstractReader,
    Contracts\JsonFileParser as JsonFileParserContract,
    Contracts\FileParser as FileParserContract,
    Contracts\JsonReader as JsonReaderContract,
    Contracts\Reader as BaseReaderContract
};

/**
 * USAGE :
 * ---------------------------------------------------------------------------------------------------------------------
 use tiFy\Plugins\Parser\Parsers\JsonReader as Reader;

 $reader = Reader::createFromPath(
    VENDOR_PATH . '/presstify-plugins/parser/Resources/sample/sample.json', [
    'offset'        => 1,
    'primary'       => 'lastname',
    'page'          => 1,
    'per_page'      => -1
 ]);

 // Lancement de la récupération des éléments.
 // @var \tiFy\Plugins\Parser\Parsers\JsonReader
 $reader->fetch();

 // Récupération du tableau de la liste des éléments courants.
 // @var array
 $reader->all();

 // Récupération de la liste des complète des enregistrements.
 // @var array
 $reader->getRecords();

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
class JsonReader extends AbstractReader implements JsonReaderContract
{
    /**
     * {@inheritDoc}
     *
     * @return JsonFileParserContract
     */
    public function getParser(): FileParserContract
    {
        return parent::getParser();
    }

    /**
     * {@inheritDoc}
     *
     * @return JsonReaderContract
     */
    public static function createFromPath(string $path, array $params = [], ...$args): BaseReaderContract
    {
        return (new static((new JsonFileParser($args))->setSource($path)))->setParams($params);
    }
}