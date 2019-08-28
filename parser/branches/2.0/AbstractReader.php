<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser;

use Exception;
use Illuminate\Support\Collection as LaraCollection;
use tiFy\Support\Collection;
use tiFy\Plugins\Parser\{
    Contracts\FileParser as FileParserContract,
    Contracts\Reader as ReaderContract
};

abstract class AbstractReader extends Collection implements ReaderContract
{
    /**
     * Instance du jeu de résultat courant.
     * @var LaraCollection|null
     */
    protected $chunks;

    /**
     * Nombre d'éléments courant.
     * @var int
     */
    protected $count = 0;

    /**
     * Numéro de la dernière page.
     * @var int
     */
    protected $lastPage = 1;

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
     * Instance de la classe de traitement du fichier source.
     * @var FileParser|null
     */
    protected $parser;

    /**
     * Nombre d'éléments par page.
     * @var int|null
     */
    protected $perPage;

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
     * Nombre total d'éléments.
     * @var int
     */
    protected $total = 0;

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
        $page     = $this->getPage();
        $total    = $this->getTotal();
        $offset   = $this->getOffset();

        $this->chunks = $this->getRecords()->splice($offset);
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
            $this->setPage($page > 0 ? $page : 1);
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
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @inheritDoc
     */
    public function getLastPage(): int
    {
        return $this->lastPage;
    }

    /**
     * @inheritDoc
     */
    public function getOffset(): int
    {
        return $this->offset;
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
    public function getParser(): FileParserContract
    {
        return $this->parser;
    }

    /**
     * @inheritDoc
     */
    public function getPerPage(): ?int
    {
        return $this->perPage;
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
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @inheritDoc
     */
    public function setCount(int $count): ReaderContract
    {
        $this->count = $count;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setLastPage(int $last_page): ReaderContract
    {
        $this->lastPage = $last_page;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setOffset(int $offset): ReaderContract
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPage(int $page): ReaderContract
    {
        $this->page = $page > 0 ? $page : 1;

        return $this;
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
                    $this->setPage($param);
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
    public function setPerPage(?int $per_page = null): ReaderContract
    {
        $this->perPage = $per_page > 0 ? $per_page : null;

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
     */
    public function setTotal(int $total): ReaderContract
    {
        $this->total = $total;

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