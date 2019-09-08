<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Template\FileListTable\Contracts;

use SplFileInfo;
use tiFy\Contracts\Support\ParamsBag;
use tiFy\Contracts\Template\FactoryAwareTrait;
use tiFy\Plugins\Parser\Contracts\Reader;

interface Source extends FactoryAwareTrait, ParamsBag
{
    /**
     * Résolution de sortie de la classe sous la forme d'un chaîne de caractères.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Retrouve le fichier source de données.
     *
     * @return static
     */
    public function fetch(): Source;

    /**
     * @inheritDoc
     */
    public function getCurrent(): ?SplFileInfo;

    /**
     * @inheritDoc
     */
    public function getFiles();

    /**
     * Récupération du chemin absolu vers le fichier de données.
     *
     * @return string
     */
    public function getFilename(): string;

    /**
     * Récupération de l'instance du gestionnaire d'enregistrements.
     *
     * @return Reader
     */
    public function reader(): ?Reader;

    /**
     * Définition de l'instance du gestionnaire d'enregistrements.
     *
     * @param Reader|null $reader
     *
     * @return static
     */
    public function setReader(?Reader $reader = null): Source;
}