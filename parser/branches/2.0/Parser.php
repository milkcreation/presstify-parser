<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser;

/**
 * Class Parser
 *
 * @desc Extension PresstiFy de traitement, conversion et écriture de données au format CSV,Excel,JSON,XML.
 * @author Jordy Manner <jordy@milkcreation.fr>
 * @package tiFy\Plugins\Parser
 * @version 2.0.0
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
class Parser
{

}
