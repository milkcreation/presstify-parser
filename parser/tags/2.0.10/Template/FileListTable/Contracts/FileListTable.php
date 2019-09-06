<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Template\FileListTable\Contracts;

use tiFy\Contracts\Template\FactoryBuilder;
use tiFy\Template\Templates\ListTable\{
    Contracts\Builder,
    Contracts\DbBuilder,
    Contracts\ListTable as BaseListTable
};

interface FileListTable extends BaseListTable
{
    /**
     * {@inheritDoc}
     *
     * @return Builder|DbBuilder|FileBuilder
     */
    public function builder(): FactoryBuilder;

    /**
     * Récupération de l'instance de gestion du fichier source.
     *
     * @return Source|null
     */
    public function source(): ?Source;
}