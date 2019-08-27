<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser;

use tiFy\Container\ServiceProvider;
use tiFy\Plugins\Parser\Template\FileListTable\{
    Contracts\FileBuilder as FileListTableFileBuilderContract,
    FileBuilder as FileListTableFileBuilder,
    Contracts\Source as SourceListTableFileBuilderContract,
    Source as SourceListTableFileBuilder
};

class ParserServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * @internal requis. Tous les noms de qualification de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = [
        'parser'
    ];

    /**
     * @inheritDoc
     */
    public function boot()
    {
        add_action('after_setup_theme', function() {
            $this->getContainer()->get('parser');
        });
    }

    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->getContainer()->share('parser', function() {
            return new ParserManager($this->getContainer());
        });
        $this->registerImportListTable();
    }

    /**
     * Déclaration du template d'import.
     *
     * @return void
     */
    public function registerImportListTable(): void
    {
        $this->getContainer()->add(FileListTableFileBuilderContract::class, function () {
            return new FileListTableFileBuilder();
        });

        $this->getContainer()->add(SourceListTableFileBuilderContract::class, function () {
            return new SourceListTableFileBuilder();
        });
    }
}