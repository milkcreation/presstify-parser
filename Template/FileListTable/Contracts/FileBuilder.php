<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Template\FileListTable\Contracts;

use tiFy\Template\Templates\ListTable\Contracts\Builder as BaseBuilderContract;

interface FileBuilder extends BaseBuilderContract
{
    /**
     * Récupération de la liste des éléments.
     *
     * @return static
     */
    public function fetchItems(): BaseBuilderContract;
}