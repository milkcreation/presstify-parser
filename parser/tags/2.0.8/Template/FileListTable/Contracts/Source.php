<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Template\FileListTable\Contracts;

use SplFileInfo;
use tiFy\Contracts\Support\ParamsBag;
use tiFy\Contracts\Template\FactoryAwareTrait;

interface Source extends FactoryAwareTrait, ParamsBag
{
    /**
     * Résolution de sortie de la classe sous la forme d'un chaîne de caractères.
     *
     * @return string
     */
    public function __toString(): string;

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
     * Retrouve le fichier source de données.
     *
     * @return static
     */
    public function fetch(): Source;
}