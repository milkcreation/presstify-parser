<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Template\FileListTable;

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
            $source->fetch();

            if ($reader = $source->reader()) {
                $reader
                    ->setPerPage(null)
                    ->fetch();

                $this->factory->items()->clear()->set(array_values($reader->getRecords()->all()));

                return $this->factory->items()->collect()->first(function (Item $item) use ($key) {
                    return $item->getKeyValue() === $key;
                });
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
            $source->fetch();

            if ($reader = $source->reader()) {
                $reader = $this->factory->source()->reader();

                $reader
                    ->setPage($this->getPage())
                    ->setPerPage($this->getPerPage())
                    ->fetch();

                if ($count = $reader->count()) {
                    $this->factory->items()->set(array_values($reader->all()));

                    $this->factory->pagination()
                        ->setCount($count)
                        ->setPage($reader->getPage())
                        ->setPerPage($reader->getPerPage())
                        ->setLastPage($reader->getLastPage())
                        ->setTotal($reader->getTotal())
                        ->parse();
                }
            }
        }

        return $this;
    }
}