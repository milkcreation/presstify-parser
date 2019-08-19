<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser;

use Exception;
use tiFy\Plugins\Parser\{
    Contracts\Reader as ReaderContract,
    Csv\Reader as CsvReader,
    Exceptions\ReaderException,
    Json\Reader as JsonReader,
    Xml\Reader as XmlReader
};

/**
 * USAGE :
 * ---------------------------------------------------------------------------------------------------------------------
 use tiFy\Plugins\Parser\Reader

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
class Reader extends AbstractReader implements ReaderContract
{
    /**
     * @inheritDoc
     *
     * @throws ReaderException
     */
    public static function createFromPath(string $path, array $params = [], ...$args): ReaderContract
    {
        if (!file_exists($path)) {
            throw new ReaderException(__('Le fichier n\'est pas accessible.', 'theme'));
        }

        try {
            switch ($ext = pathinfo($path, PATHINFO_EXTENSION)) {
                default:
                    throw new ReaderException(__('Le type de fichier n\'est pas pris en charge.', 'theme'));
                    break;
                case 'csv' :
                    return CsvReader::createFromPath($path, $params, ...$args);
                    break;
                case 'json' :
                    return JsonReader::createFromPath($path, $params, ...$args);
                    break;
                case 'xml' :
                    return XmlReader::createFromPath($path, $params, ...$args);
                    break;
            }
        } catch (Exception $e) {
            throw new ReaderException(__('Impossible de lire le fichier.', 'theme'));
        }
    }
}