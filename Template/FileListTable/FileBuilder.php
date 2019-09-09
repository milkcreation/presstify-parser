<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Template\FileListTable;

use tiFy\Plugins\Parser\Template\FileListTable\Contracts\FileBuilder as FileBuilderContract;
use tiFy\Template\Templates\ListTable\{
    Builder as BaseBuilder,
    Contracts\Builder as BaseBuilderContract,
    Contracts\Item as BaseItem
};

class FileBuilder extends BaseBuilder implements FileBuilderContract
{
    /**
     * Instance du gabarit d'affichage.
     * @var Factory
     */
    protected $factory;

    /**
     * @inheritDoc
     */
    public function getItem(string $key): ?BaseItem
    {
        if (($source = $this->factory->source()) && ($reader = $source->fetch()->reader())) {
           if ($item = $this->factory->items()->collect()->first(function (BaseItem $item) use ($key) {
               return $item->getKeyValue() === $key;
           })) {
                return $item;
           } else {
               $offset = 0;
               if ($record = $reader->getRecords()->first(function ($i, $k) use ($key, &$offset){
                   $offset = $k;
                   return $i[$this->factory->items()->primaryKey()] === $key;
               })) {
                   return ($item = $this->factory->items()->setItem($record))
                       ? $item->setOffset($offset)->parse()
                       : null;
               }
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

        if (($source = $this->factory->source()) && ($reader = $source->fetch()->reader())) {
            $reader
                ->setPage($this->getPage())
                ->setPerPage($this->getPerPage())
                ->fetch();

            if ($count = $reader->count()) {
                $this->factory->items()->set($reader->all());

                $this->factory->pagination()
                    ->setCount($count)
                    ->setPage($reader->getPage())
                    ->setPerPage($reader->getPerPage())
                    ->setLastPage($reader->getLastPage())
                    ->setTotal($reader->getTotal())
                    ->parse();
            }
        }

        return $this;
    }
}