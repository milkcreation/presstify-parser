<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Parsers;

use Exception;
use Rodenastyle\StreamParser\Parsers\XMLParser;
use tiFy\Plugins\Parser\{
    Contracts\FileParser as FileParserContract,
    Contracts\JsonFileParser as XmlFileParserContract,
    FileParser
};
use Tightenco\Collect\Support\Collection as TightencoCollection;

class XmlFileParser extends FileParser implements XmlFileParserContract
{
    /**
     * @inheritDoc
     */
    public function parse(): FileParserContract
    {
        try {
            $this->stream = $this->open();

            TightencoCollection::macro('recursiveToArray', function () {
                /** @var TightencoCollection $self */
                $self = $this;

                return $self->map(function ($value) {
                    if ($value instanceof TightencoCollection) {
                        if ($value = $value->recursiveToArray()->all()) {
                            return is_array($value) && (count($value) === 1) ? reset($value) : $value;
                        }

                        return null;
                    }

                    return $value;
                });
            });

            (new XMLParser())->from($this->source)->each(function (TightencoCollection $item) use (&$records) {
                $this->records[] = $item->recursiveToArray()->all();
            });
        } catch (Exception $e) {
            throw $e;
        }

        if( ! fclose($this->stream)){
            throw new Exception();
        }

        return $this;
    }
}