<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser;

use Exception;
use tiFy\Plugins\Parser\{
    Contracts\Reader as ReaderContract,
    Exceptions\ReaderException,
    Parsers\CsvReader,
    Parsers\JsonReader,
    Parsers\LogReader,
    Parsers\XmlReader
};

/**
 * USAGE :
 * ---------------------------------------------------------------------------------------------------------------------
 use tiFy\Plugins\Parser\Reader;

 $reader = Reader::createFromPath(
    VENDOR_PATH . '/presstify-plugins/parser/Resources/sample/sample.json', [
    'offset'        => 1,
    'primary'       => 'lastname',
    'page'          => 1,
    'per_page'      => -1
 ]);

 // Lancement de la récupération des éléments.
 // @var \tiFy\Plugins\Contracts\Reader
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
class Reader extends AbstractReader implements ReaderContract
{
    /**
     * @inheritDoc
     *
     * @throws ReaderException
     */
    public static function createFromPath(string $path, array $params = [], ...$args): ReaderContract
    {
        try {
            switch ($ext = pathinfo($path, PATHINFO_EXTENSION)) {
                default:
                    throw new ReaderException(__('Le type de fichier n\'est pas pris en charge.', 'tify'));
                    break;
                case 'csv' :
                    return CsvReader::createFromPath($path, $params, ...$args);
                    break;
                case 'json' :
                    return JsonReader::createFromPath($path, $params, ...$args);
                    break;
                case 'log' :
                    return LogReader::createFromPath($path, $params, ...$args);
                    break;
                case 'xml' :
                    return XmlReader::createFromPath($path, $params, ...$args);
                    break;
            }
        } catch (Exception $e) {
            throw new ReaderException(__('Impossible de lire le fichier.', 'tify'));
        }
    }
}