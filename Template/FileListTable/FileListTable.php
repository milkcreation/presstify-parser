<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Template\FileListTable;

use tiFy\Contracts\Template\FactoryBuilder;
use tiFy\Template\Templates\ListTable\{
    Contracts\Builder,
    Contracts\DbBuilder,
    ListTable as BaseListTable
};
use tiFy\Plugins\Parser\Template\FileListTable\Contracts\{
    FileBuilder,
    FileListTable as FileListTableContract,
    Source
};

class FileListTable extends BaseListTable implements FileListTableContract
{
    /**
     * Liste des fournisseurs de services.
     * @var string[]
     */
    protected $serviceProviders = [
        FileListTableServiceProvider::class,
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