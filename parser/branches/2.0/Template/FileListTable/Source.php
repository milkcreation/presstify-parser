<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Template\FileListTable;

use Exception;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use tiFy\Plugins\Parser\{
    Contracts\Reader as ReaderContract,
    Exceptions\ReaderException,
    Reader,
    Template\FileListTable\Contracts\Source as SourceContract};
use tiFy\Support\ParamsBag;
use tiFy\Template\Factory\FactoryAwareTrait;

class Source extends ParamsBag implements SourceContract
{
    use FactoryAwareTrait;

    /**
     * Instance du gabarit d'affichage.
     * @var FileListTable
     */
    protected $factory;

    /**
     * Instance du fichier de données courant.
     * @var SplFileInfo|null
     */
    protected $current;

    /**
     * Liste des instances des fichiers de données.
     * @var SplFileInfo[]|array
     */
    protected $files = [];

    /**
     * Indicateur de préparation de la classe.
     * @var boolean
     */
    protected $ready = false;

    /**
     * Instance du gestionnaire de récupération des enregistrements.
     * @var ReaderContract|null
     */
    protected $reader;

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->getFilename();
    }

    /**
     * @inheritDoc
     */
    public function fetch(): SourceContract
    {
        if (!$this->ready) {
            if ($dir = $this->get('dir')) {
                $finder = new Finder();

                try {
                    $finder->files()->depth('== 0')->in($dir)->reverseSorting()->sortByModifiedTime();
                    if ($finder->hasResults()) {
                        $this->files = iterator_to_array($finder);
                        $this->current = current($this->files);
                    }
                } catch (Exception $e) {
                    $this->current = null;
                }
            } elseif ($filename = $this->get('filename')) {
                $this->current = new SplFileInfo($filename);
            }

            $this->setReader();

            $this->ready = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCurrent(): ?SplFileInfo
    {
        return $this->current;
    }

    /**
     * @inheritDoc
     */
    public function getFilename(): string
    {
        return $this->getCurrent() instanceof SplFileInfo ? $this->getCurrent()->getPathname() : '';
    }

    /**
     * @inheritDoc
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @inheritDoc
     */
    public function reader(): ?ReaderContract
    {
        return $this->reader;
    }

    /**
     * @inheritDoc
     */
    public function setReader(?ReaderContract $reader = null): SourceContract
    {
        if (is_null($reader) && $this->getCurrent())  {
            try {
                $reader = Reader::createFromPath($this->getFilename());
            } catch (ReaderException $e) {
                $this->factory->label(['no_item' => $e->getMessage()]);
            }
        }
        $this->reader = $reader;

        return $this;
    }
}