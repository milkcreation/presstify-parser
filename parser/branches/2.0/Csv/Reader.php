<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Csv;

use League\Csv\CharsetConverter;
use League\Csv\Exception;
use League\Csv\Reader as LeagueReader;
use League\Csv\ResultSet;
use League\Csv\Statement;
use tiFy\Support\Collection;
use tiFy\Plugins\Parser\Contracts\CsvReader;

/**
 *  USAGE :
 *
 * use tiFy\Plugins\Parser\Csv\Reader
 *
 * $csv = Reader::createFromPath('/example.csv', [
 *      'delimiter'     => ',',
 *      'enclosure'     => '"',
 *      'escape'        => '\\',
 *      'header'        => ['lastname', 'firstname', 'email'],
 *      'page'          => 1,
 *      'per_page'      => -1
 * ]);
 *
 * // Récupération du tableau de la liste des éléments courants.
 * // @var array
 * $csv->toArray();
 *
 * // Récupération la liste des éléments de la page 2.
 * $csv->page(2);
 *
 * // Récupération du nombre total de resultats.
 * // @var int
 * $csv->getTotal();
 *
 * // Récupération du nombre de page de résultats.
 * // @var int
 * $csv->getPages();
 *
 * // Récupération du nombre d'éléments courants.
 * // @var int
 * $csv->getFounds();
 */
class Reader extends Collection implements CsvReader
{
    /**
     * Nombre d'éléments courant.
     * @var int
     */
    private $_founds = 0;

    /**
     * Nombre total d'éléments.
     * @var int
     */
    private $_total = 0;

    /**
     * Nombre total de page.
     * @var int
     */
    private $_pages = 0;

    /**
     * Indicateur d'intégrité du controleur.
     * @var boolean
     */
    private $_prepared = false;

    /**
     * Instance du controleur de traitement.
     * @var LeagueReader
     */
    private $_reader;

    /**
     * Instance du jeu de résultat complet.
     * @var ResultSet
     */
    private $_records;

    /**
     * Instance du jeu de résultat courant.
     * @var ResultSet|null
     */
    private $_result;

    /**
     * Instance de déclaration des enregistrements.
     * @var Statement
     */
    private $_stat;

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
     * Caractère d'échappemment.
     * @var string
     */
    protected $escape = '\\';

    /**
     * Entête.
     * {@internal
     * - array > Indice de qualification des colonnes utilisé pour indexé la valeur des éléments. Tableau associatif.
     * - true > La première ligne d'enregistrement est utilisée pour indexer la valeur des élements. Tableau associatif.
     * - false > Les élements sont indexés numériquement. Tableau indexé.
     * }
     * @var string[]|boolean
     */
    protected $header = false;

    /**
     * Liste des éléments courants.
     * @var array|null
     */
    protected $items;

    /**
     * La ligne de démarrage du traitement.
     * @var int
     */
    protected $offset = 0;

    /**
     * Numéro de la page courante.
     * @var int
     */
    protected $page = 1;

    /**
     * Nombre d'éléments par page.
     * @var int
     */
    protected $perPage = -1;

    /**
     * Définition du nombre d'éléments courants trouvés.
     *
     * @param int $founds
     *
     * @return $this
     */
    private function _setFounds(int $founds): CsvReader
    {
        $this->_founds = $founds;

        return $this;
    }

    /**
     * Définition du nombre total de page d'éléments.
     *
     * @param int $pages
     *
     * @return $this
     */
    private function _setPages(int $pages): CsvReader
    {
        $this->_pages = $pages;

        return $this;
    }

    /**
     * Définition du nombre total d'éléments.
     *
     * @param int $total
     *
     * @return $this
     */
    private function _setTotal(int $total): CsvReader
    {
        $this->_total = $total;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function createFromPath(string $path, array $params = [], ...$args): CsvReader
    {
        array_unshift($args, $path);

        return (new static())->prepare(LeagueReader::createFromPath(...$args), $params)->fetchItems();
    }

    /**
     * Récupération de la liste des éléments courant.
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function fetchItems(): CsvReader
    {
        if (is_null($this->items)) {
            $per_page = $this->getPerPage();
            $page = $this->getPage();
            $offset = $per_page > -1
                ? (($page - 1) * $per_page) + $this->offset
                : ($this->getPage() > 1 ? $this->getTotal() + 1 : $this->offset);

            $this->_result = $this->getStat()
                ->offset(intval($offset))
                ->limit($per_page)
                ->process($this->getReader(), $this->getHeader());

            $this->_setFounds(count($this->_result));

            $total_pages = ($per_page > -1) ? ceil($this->getTotal() / $per_page) : 1;
            $this->_setPages(intval($total_pages));

            $this->items = iterator_to_array($this->_result);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasHeader(): bool
    {
        return !!$this->header;
    }

    /**
     * @inheritDoc
     */
    public function getFounds(): int
    {
        return intval($this->_founds);
    }

    /**
     * @inheritDoc
     */
    public function getHeader(): array
    {
        return is_array($this->header) ? $this->header : [];
    }

    /**
     * @inheritDoc
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @inheritDoc
     */
    public function getPages(): int
    {
        return intval($this->_pages);
    }

    /**
     * @inheritDoc
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @inheritDoc
     */
    public function getReader(): LeagueReader
    {
        return $this->_reader;
    }

    /**
     * Récupération de l'instance de déclaration des enregistrements.
     *
     * @return Statement
     */
    protected function getStat(): Statement
    {
        return $this->_stat;
    }

    /**
     * @inheritDoc
     */
    public function getTotal(): int
    {
        return intval($this->_total);
    }

    /**
     * Préparation du controleur.
     *
     * @param LeagueReader $reader
     * @param array $params Liste des paramètres.
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function prepare(LeagueReader $reader, array $params = []): CsvReader
    {
        if (!$this->_prepared) {
            $this->_reader = $reader;

            foreach ($params as $key => $value) {
                switch ($key) {
                    case 'delimiter' :
                        $this->delimiter = $value;
                        break;
                    case 'encoding' :
                        $this->setEncoding($value);
                        break;
                    case 'enclosure' :
                        $this->enclosure = $value;
                        break;
                    case 'escape' :
                        $this->escape = $value;
                        break;
                    case 'header' :
                        $this->header = $value;
                        break;
                    case 'offset' :
                        $this->offset = intval($value);
                        break;
                    case 'orderby' :
                        // @todo
                        // $this->orderBy = $value;
                        break;
                    case 'page' :
                        $this->page = intval($value);
                        break;
                    case 'per_page' :
                        $this->perPage = intval($value);
                        break;
                    case 'search' :
                        // @todo
                        // $this->searchArgs = $value;
                        break;
                }
            }

            if ($this->hasHeader() && !$this->getHeader()) {
                $this->_reader->setHeaderOffset(0);
                $this->header = $this->_reader->getHeader();
            }

            $this->_reader->setDelimiter($this->delimiter)->setEnclosure($this->enclosure)->setEscape($this->escape);

            if ($this->encoding) {
                CharsetConverter::addTo($this->_reader, $this->encoding[0], $this->encoding[1]);
            }

            $this->_stat = new Statement();
            $this->_records = $this->getStat()->process($this->_reader);

            $this->_setTotal((count($this->_records) - $this->offset));

            $this->_prepared = true;
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setEncoding(array $encoding): CsvReader
    {
        $this->encoding = [$encoding[0] ?? 'utf-8', $encoding[1] ?? 'utf-8'];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function page(int $page): CsvReader
    {
        if ($this->page !== $page) {
            $this->page = $page > 0 ? $page : 1;
            $this->items = null;
            $this->fetchItems();
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