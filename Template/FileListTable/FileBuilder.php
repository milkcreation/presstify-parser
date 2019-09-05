<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Template\FileListTable;

use tiFy\Plugins\Parser\{
    Exceptions\ReaderException,
    Reader
};
use tiFy\Plugins\Parser\Template\FileListTable\Contracts\FileBuilder as FileBuilderContract;
use tiFy\Template\Factory\FactoryAwareTrait;
use tiFy\Template\Templates\ListTable\{
    Builder as BaseBuilder,
    Contracts\Builder as BaseBuilderContract,
    Contracts\Item
};

class FileBuilder extends BaseBuilder implements FileBuilderContract
{
    use FactoryAwareTrait;

    /**
     * Instance du gabarit d'affichage.
     * @var FileListTable
     */
    protected $factory;

    /**
     * @inheritDoc
     */
    public function getItem(string $key): ?Item
    {
        $this->parse();

        if ($source = $this->factory->source()) {
            try {
                $reader = Reader::createFromPath($source->fetch()->getFilename())->fetchRecords();

                $this->factory->items()->set($reader->getRecords());

                return $this->factory->items()->collect()->first(function (Item $item) use ($key) {
                    return $item->getKeyValue() === $key;
                });
            } catch (ReaderException $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function fetchItems(): BaseBuilderContract
    {
        $this->parse();

        if ($source = $this->factory->source()) {
            try {
                $reader = Reader::createFromPath(
                    $source->fetch()->getFilename(), [
                    'page'     => $this->getPage(),
                    'per_page' => $this->getPerPage(),
                ])->fetch();

                $this->factory->items()->set(array_values($reader->all()));

                if ($count = $reader->count()) {
                    $this->factory->pagination()
                        ->setCount($count)
                        ->setCurrentPage($reader->getPage())
                        ->setPerPage($reader->getPerPage())
                        ->setLastPage($reader->getLastPage())
                        ->setTotal($reader->getTotal())
                        ->parse();
                }
            } catch (ReaderException $e) {
                $this->factory->label(['no_item' => $e->getMessage()]);
            }
        }

        return $this;
    }
}