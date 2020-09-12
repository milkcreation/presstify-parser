<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser;

use Exception;
use Illuminate\Support\Collection as LaraCollection;
use tiFy\Plugins\Parser\{Contracts\FileParser as FileParserContract, Contracts\Reader as ReaderContract};
use tiFy\Support\{Collection, Traits\PaginationAwareTrait};

abstract class AbstractReader extends Collection implements ReaderContract
{
    use PaginationAwareTrait;

    /**
     * Instance du jeu de résultat courant.
     * @var LaraCollection|null
     */
    protected $chunks;

    /**
     * Instance de la classe de traitement du fichier source.
     * @var FileParser|null
     */
    protected $parser;

    /**
     * Colonne de clés primaires d'indexation des éléments.
     * @var string|int|null
     */
    protected $primary;

    /**
     * Instance du jeu de résultat complet.
     * @var LaraCollection|null
     */
    protected $records;

    /**
     * CONSTRUCTEUR.
     *
     * @param FileParserContract $parser Instance de la classe de traitement du fichier source.
     *
     * @return void
     */
    public function __construct(FileParserContract $parser)
    {
        $this->setParser($parser);
    }

    /**
     * @inheritDoc
     */
    abstract public static function createFromPath(string $path, array $params = [], ...$args): ReaderContract;

    /**
     * @inheritDoc
     */
    public function fetch(): ReaderContract
    {
        $this->fetchRecords();
        $this->fetchItems();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function fetchItems(): ReaderContract
    {
        $this->items = [];

        $per_page = $this->getPerPage();
        $page = $this->getCurrentPage();
        $total = $this->getTotal();
        $offset = $this->getOffset();
        $records = clone $this->getRecords();

        $this->chunks = $records->splice($offset);
        $this->chunks = $this->chunks->forPage($page, $per_page);
        if ($this->hasPrimary()) {
            $this->chunks = $this->chunks->keyBy($this->getPrimary());
        }

        $this->setCount($this->chunks->count());
        $this->setLastPage($per_page > -1 ? (int)ceil($total / $per_page) : 1);

        $this->set($this->chunks->all());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function fetchForPage(int $page = 1): ReaderContract
    {
        if ($this->page !== $page) {
            $this->setCurrentPage($page > 0 ? $page : 1);
            $this->fetch();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function fetchRecords(): ReaderContract
    {
        if (is_null($this->records)) {
            try {
                $this->getParser()->parse();
            } catch (Exception $e) {

            }
            $this->records = $this->getParser()->collect();

            $this->setTotal($this->records->count() - $this->getOffset());
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasPrimary(): bool
    {
        return ! ! $this->primary;
    }

    /**
     * @inheritDoc
     */
    public function getParser(): FileParserContract
    {
        return $this->parser;
    }

    /**
     * @inheritDoc
     */
    public function getPrimary()
    {
        return $this->primary;
    }

    /**
     * @inheritDoc
     */
    public function getRecords(): ?LaraCollection
    {
        return $this->records;
    }

    /**
     * @inheritDoc
     */
    public function setParams(array $params = []): ReaderContract
    {
        foreach ($params as $key => $param) {
            switch ($key) {
                case 'offset' :
                    $this->setOffset($param);
                    break;
                case 'page' :
                    $this->setCurrentPage($param);
                    break;
                case 'per_page' :
                    $this->setPerPage($param);
                    break;
                case 'primary' :
                    $this->setPrimary($param);
                    break;
                /**
                 * @todo
                case 'orderby' :
                break;
                case 'search' :
                break;
                 */
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setParser(FileParserContract $parser): ReaderContract
    {
        $this->parser = $parser;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPrimary($primary): ReaderContract
    {
        if (is_numeric($primary)) {
            $this->primary = intval($primary);
        } elseif (is_string($primary)) {
            $this->primary = $primary;
        }

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function toArray(): array
    {
        try {
            return $this->all() ?: [];
        } catch (Exception $e) {
            return [];
        }
    }
}