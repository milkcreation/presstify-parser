<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Driver;

use Exception;
use tiFy\Plugins\Parser\{
    AbstractReader,
    Contracts\CsvFileParser as CsvFileParserContract,
    Contracts\FileParser as FileParserContract,
    Contracts\CsvReader as CsvReaderContract,
    Contracts\Reader as BaseReaderContract
};

/**
 * USAGE :
 * ---------------------------------------------------------------------------------------------------------------------
 use tiFy\Plugins\Parser\Driver\CsvReader as Reader;

 // Initialisation
 $reader = Reader::createFromPath(
    VENDOR_PATH . '/presstify-plugins/parser/Resources/sample/sample.csv', [
    'delimiter'     => ',',
    'enclosure'     => '"',
    'escape'        => '\\',
    'header'        => true,
    'offset'        => 0,
    'primary'       => 'lastname',
    'page'          => 1,
    'per_page'      => -1
 ]);

 // Lancement de la récupération des éléments.
 // @var \tiFy\Plugins\Parser\Driver\CsvReader
 $reader->fetch();

 // Récupération de la liste des complète des enregistrements.
 // @var array
 $reader->getRecords();

 // Récupération du tableau de la liste des éléments courants.
 // @var array
 $reader->fetch()->all();

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
class CsvReader extends AbstractReader implements CsvReaderContract
{
    /**
     * {@inheritDoc}
     *
     * @return CsvFileParserContract
     */
    public function getParser(): FileParserContract
    {
        return parent::getParser();
    }

    /**
     * {@inheritDoc}
     *
     * @return CsvReaderContract
     *
     * @throws Exception
     */
    public static function createFromPath(string $path, array $params = [], ...$args): BaseReaderContract
    {
        return (new static((new CsvFileParser($args))->setSource($path)))->setParams($params);
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
                    $this->getParser()->setDelimiter($param);
                    break;
                case 'encoding' :
                    $this->getParser()->setEncoding($param);
                    break;
                case 'enclosure' :
                    $this->getParser()->setEnclosure($param);
                    break;
                case 'escape' :
                    $this->getParser()->setEscape($param);
                    break;
                case 'header' :
                    $this->getParser()->setHeader($param);
                    break;
            }
        }

        return parent::setParams($params);
    }
}