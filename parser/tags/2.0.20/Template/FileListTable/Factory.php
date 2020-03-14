<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Template\FileListTable;

use tiFy\Contracts\Template\FactoryBuilder;
use tiFy\Template\Templates\ListTable\{
    Contracts\Builder,
    Contracts\DbBuilder,
    Factory as BaseFactory
};
use tiFy\Plugins\Parser\Template\FileListTable\Contracts\{
    Factory as FactoryContract,
    FileBuilder,
    Source
};

class Factory extends BaseFactory implements FactoryContract
{
    /**
     * Liste des fournisseurs de services.
     * @var string[]
     */
    protected $serviceProviders = [
        ServiceProvider::class,
    ];

    /**
     * {@inheritDoc}
     *
     * @return Builder|DbBuilder|FileBuilder
     */
    public function builder(): FactoryBuilder
    {
        return parent::builder();
    }

    /**
     * @inheritDoc
     */
    public function source(): ?Source
    {
        return $this->resolve('source');
    }
}