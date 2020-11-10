<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser;

use tiFy\Plugins\Parser\Contracts\ParserManager as ParserManagerContract;
use tiFy\Support\Manager;

/**
 * @desc Extension PresstiFy de traitement, conversion et écriture de données au format CSV,Excel,JSON,LOG,XML.
 * @author Jordy Manner <jordy@milkcreation.fr>
 * @package tiFy\Plugins\Parser
 * @version 2.0.28
 *
 * USAGE :
 * Activation
 * ---------------------------------------------------------------------------------------------------------------------
 * Dans config/app.php ajouter \tiFy\Plugins\Parser\ParserServiceProvider à la liste des fournisseurs de services.
 * ex.
 * <?php
 * ...
 * use tiFy\Plugins\Parser\ParserServiceProvider;
 * ...
 *
 * return [
 *      ...
 *      'providers' => [
 *          ...
 *          ParserServiceProvider::class
 *          ...
 *      ]
 * ];
 *
 * Configuration
 * ---------------------------------------------------------------------------------------------------------------------
 * Dans le dossier de config, créer le fichier parser.php
 * @see Resources/config/parser.php
 */
class ParserManager extends Manager implements ParserManagerContract
{

}
