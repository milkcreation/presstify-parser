<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Parsers;

use tiFy\Plugins\Parser\{
    AbstractReader,
    Contracts\FileParser as FileParserContract,
    Contracts\LogFileParser as LogFileParserContract,
    Contracts\LogReader as LogReaderContract,
    Contracts\Reader as BaseReaderContract
};

/**
 * USAGE :
 * ---------------------------------------------------------------------------------------------------------------------
 use tiFy\Plugins\Parser\Parsers\LogReader as Reader;

 $reader = Reader::createFromPath(
    VENDOR_PATH . '/presstify-plugins/parser/Resources/sample/sample.log', [
    'offset'        => 1,
    'primary'       => 'lastname',
    'page'          => 1,
    'per_page'      => -1
 ]);

 // Lancement de la récupération des éléments.
 // @var \tiFy\Plugins\Parser\Parsers\LogReader
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
class LogReader extends AbstractReader implements LogReaderContract
{
    /**
     * {@inheritDoc}
     *
     * @return LogFileParserContract
     */
    public function getParser(): FileParserContract
    {
        return parent::getParser();
    }

    /**
     * {@inheritDoc}
     *
     * @return LogReaderContract
     */
    public static function createFromPath(string $path, array $params = [], ...$args): BaseReaderContract
    {
        return (new static((new LogFileParser($args))->setSource($path)))->setParams($params);
    }
}