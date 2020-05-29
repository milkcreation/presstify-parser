<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Contracts;

use Exception;
use Illuminate\Support\Collection;
use tiFy\Plugins\Parser\Exceptions\UnableOpenFileException;

interface FileParser
{
    /**
     * Récupération d'une instance de la liste des enregistrements.
     *
     * @return Collection
     */
    public function collect(): Collection;

    /**
     * Récupération d'une instance de la liste des enregistrements.
     *
     * @return resource
     *
     * @throws UnableOpenFileException
     */
    public function open();

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function parse(): FileParser;

    /**
     * Définition du fichier source de récupération des enregistrements.
     *
     * @param string $source
     *
     * @return static
     */
    public function setSource(string $source): FileParser;
}