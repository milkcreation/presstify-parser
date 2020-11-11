<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Driver;

use Exception;
use tiFy\Plugins\Parser\Lib\Xml2Assoc;
use tiFy\Plugins\Parser\Contracts\FileParser as FileParserContract;
use tiFy\Plugins\Parser\Contracts\JsonFileParser as XmlFileParserContract;
use tiFy\Plugins\Parser\FileParser;
use SimpleXMLElement;

class XmlFileParser extends FileParser implements XmlFileParserContract
{
    /**
     * @inheritDoc
     */
    public function parse(): FileParserContract
    {
        try {
            $this->stream = $this->open();

            /* */
            $xml = (new Xml2Assoc())->parseFile($this->source, true);
            $xml = reset($xml);
            $this->records = $xml;
            /**/

            /*  * /
             $xml = json_decode(json_encode(simplexml_load_file($this->source)), true);
             $xml = reset($xml);
             $this->records = $xml;
             /**/

             /* * /
             $this->records = $this->xmlToArray(simplexml_load_string(file_get_contents($this->source)));
             /**/
        } catch (Exception $e) {
            throw $e;
        }

        if( ! fclose($this->stream)){
            throw new Exception();
        }

        return $this;
    }

    /*
    public function xmlToArray(SimpleXMLElement $xml): array
    {
        $parser = function (SimpleXMLElement $xml, array $collection = []) use (&$parser) {
            $nodes = $xml->children();
            $attributes = $xml->attributes();

            if (0 !== count($attributes)) {
                foreach ($attributes as $attrName => $attrValue) {
                    $collection[$attrName] = strval($attrValue);
                }
            }

            if (0 === $nodes->count()) {
                $collection = strval($xml);
                return $collection;
            }

            foreach ($nodes as $nodeName => $nodeValue) {
                if (count($nodeValue->xpath('../' . $nodeName)) < 2) {
                    $collection[$nodeName] = $parser($nodeValue);
                    continue;
                }

                $collection[] = $parser($nodeValue);
            }

            return $collection;
        };

        return $parser($xml);
    }
    */
}