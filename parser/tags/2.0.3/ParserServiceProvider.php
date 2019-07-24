<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser;

use tiFy\Container\ServiceProvider;
use tiFy\Plugins\Parser\Csv\Reader;


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

    /**
     * Définition des contrôleurs de traitement Csv
     *
     * @return void
     */
    public function registerCsv()
    {

    }
}