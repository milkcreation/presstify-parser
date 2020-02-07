<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser;

use Exception;
use Illuminate\Support\Collection;
use tiFy\Plugins\Parser\{
    Contracts\FileParser as FileParserContract,
    Exceptions\UnableOpenFileException
};

class FileParser implements FileParserContract
{
    /**
     * Liste des arguments de traitement complémentaires.
     * @var array
     */
    protected $args = [];

    /**
     * Liste des enregistrements du fichier.
     * @var array
     */
    public $records = [];

    /**
     * @var resource|null
     */
    protected $stream;

    /**
     * Fichier source de la liste des enregistrements.
     * @var string
     */
    protected $source = '';

    /**
     * CONSTRUCTEUR.
     *
     * @param array $args Liste d'arguments de traitement complémentaires.
     *
     * @return void
     */
    public function __construct($args)
    {
        $this->args = $args;
    }

    /**
     * @inheritDoc
     */
    public function collect(): Collection
    {
        return new Collection($this->records);
    }

    /**
     * @inheritDoc
     */
    public function open()
    {
        $stream = @fopen($this->source, 'r');
        if (false === $stream) {
            throw new UnableOpenFileException(
                sprintf(__('Impossible d\'ouvrir le fichier source %s', 'tify'), $this->source)
            );
        }

        return $stream;
    }

    /**
     * @inheritDoc
     */
    public function parse(): FileParserContract
    {
        try {
            $this->stream = $this->open();
        } catch (Exception $e) {
            throw $e;
        }

        if( ! fclose($this->stream)){
            throw new Exception();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSource(string $source): FileParserContract
    {
        $this->source = $source;

        return $this;
    }
}