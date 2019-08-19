<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser;

use tiFy\Container\ServiceProvider;

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
     * @inheritdoc
     */
    public function boot()
    {
        add_action('after_setup_theme', function() {
            $this->getContainer()->get('parser');
        });
    }

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->getContainer()->share('parser', function() {
            return new Parser($this->getContainer());
        });
    }
}