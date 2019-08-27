<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Template\FileListTable;

use tiFy\Plugins\Parser\Template\FileListTable\{
    Contracts\FileBuilder,
    Contracts\Source
};
use tiFy\Template\Templates\ListTable\Contracts\{Builder, DbBuilder};
use tiFy\Template\Templates\ListTable\ListTableServiceProvider as BaseListTableServiceProvider;

class FileListTableServiceProvider extends BaseListTableServiceProvider
{
    /**
     * Instance du gabarit d'affichage.
     * @var FileListTable
     */
    protected $factory;

    /**
     * @inheritDoc
     */
    public function registerFactories(): void
    {
        parent::registerFactories();
        $this->registerFactoryBuilder();
        $this->registerFactorySource();
    }

    /**
     * @inheritDoc
     */
    public function registerFactoryBuilder(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('builder'), function () {
            $ctrl = $this->factory->get('providers.builder');

            if ($source = $this->factory->param('source', [])) {
                $ctrl = $ctrl instanceof FileBuilder
                    ? clone $ctrl
                    : $this->getContainer()->get(FileBuilder::class);
            } elseif ($this->factory->db()) {
                $ctrl = $ctrl instanceof DbBuilder
                    ? clone $ctrl
                    : $this->getContainer()->get(DbBuilder::class);
            } else {
                $ctrl = $ctrl instanceof Builder
                    ? clone $ctrl
                    : $this->getContainer()->get(Builder::class);
            }

            $attrs = $this->factory->param('query_args', []);

            return $ctrl->setTemplateFactory($this->factory)->set(is_array($attrs) ? $attrs : []);
        });
    }

    /**
     * Déclaration du controleur de gestion de traitement de fichier de données.
     *
     * @return void
     */
    public function registerFactorySource(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('source'), function () {
            $ctrl = $this->factory->get('providers.source');

            if (!$attrs = $this->factory->param('source', [])) {
                return null;
            } else {
                $ctrl = $ctrl instanceof Source
                    ? $ctrl
                    : $this->getContainer()->get(Source::class);
            }

            if(is_string($attrs)) {
                if (is_file($attrs)) {
                    $attrs = ['filename' => $attrs];
                } else {
                    $attrs = ['dir' => $attrs];
                }
            }

            return $ctrl->setTemplateFactory($this->factory)->set(is_array($attrs) ? $attrs : []);
        });
    }
}