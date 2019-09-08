<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Template\FileListTable;

use tiFy\Plugins\Parser\Template\FileListTable\{
    Contracts\FileBuilder,
    Contracts\Source
};
use tiFy\Template\Templates\ListTable\{
    Contracts\Builder as BaseBuilderContract,
    Contracts\DbBuilder as BaseDbBuilderContract,
    ServiceProvider as BaseServiceProvider
};

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Instance du gabarit d'affichage.
     * @var Factory
     */
    protected $factory;

    /**
     * @inheritDoc
     */
    public function registerFactories(): void
    {
        parent::registerFactories();
        $this->registerFactorySource();
    }

    /**
     * @inheritDoc
     */
    public function registerFactoryBuilder(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('builder'), function () {
            $ctrl = $this->factory->provider('builder');

            if ($source = $this->factory->param('source', [])) {
                $ctrl = $ctrl instanceof FileBuilder
                    ? clone $ctrl
                    : $this->getContainer()->get(FileBuilder::class);
            } elseif ($this->factory->db()) {
                $ctrl = $ctrl instanceof BaseDbBuilderContract
                    ? clone $ctrl
                    : $this->getContainer()->get(BaseDbBuilderContract::class);
            } else {
                $ctrl = $ctrl instanceof BaseBuilderContract
                    ? clone $ctrl
                    : $this->getContainer()->get(BaseBuilderContract::class);
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
        $this->getContainer()->share($this->getFactoryAlias('source'), function (): Source {
            $ctrl = $this->factory->provider('source');

            if (!$attrs = $this->factory->param('source', [])) {
                return null;
            } else {
                $ctrl = $ctrl instanceof Source
                    ? clone $ctrl
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