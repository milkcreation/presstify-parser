<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Driver;

use Exception;
use tiFy\Plugins\Parser\{
    Contracts\FileParser as FileParserContract,
    Contracts\JsonFileParser as JsonFileParserContract,
    FileParser
};
use JsonCollectionParser\Listener;
use JsonStreamingParser\Parser as StreamingParser;

class JsonFileParser extends FileParser implements JsonFileParserContract
{
    /**
     * @inheritDoc
     */
    public function parse(): FileParserContract
    {
        try {
            $this->stream = $this->open();

           (new StreamingParser($this->stream, new Listener(function(array $item) {
               $this->records[] = $item;
           }, true)))->parse();
        } catch (Exception $e) {
            throw $e;
        }

        if( ! fclose($this->stream)){
            throw new Exception();
        }

        return $this;
    }
}