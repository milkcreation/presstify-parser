<?php declare(strict_types=1);

namespace tiFy\Plugins\Parser\Parsers;

use Exception;
use SplFileObject;
use tiFy\Plugins\Parser\{Contracts\FileParser as FileParserContract,
    Contracts\LogFileParser as LogFileParserContract,
    FileParser};

class LogFileParser extends FileParser implements LogFileParserContract
{
    /**
     * Motif de traitement des éléments d'une ligne
     * @var string
     */
    protected $pattern = '/\[(?P<date>.*)\]\s(?P<logger>.*)\.(?P<level>\w+)\:\s(?P<message>[^\[\{]+)\s(?P<context>[\[\{].*[\]\}])\s(?P<extra>[\[\{].*[\]\}])/';

    /**
     * @inheritDoc
     */
    public function parse(): FileParserContract
    {
        try {
            $file = new SplFileObject($this->source, 'r');

            while (!$file->eof()) {
                if ($line = $this->parseLine($file->current())) {
                    $this->records[] = $line;
                }
                $file->next();
            }

        } catch (Exception $e) {
            throw $e;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseLine(string $line): array
    {
        if ((strlen($line) !== 0) && preg_match($this->pattern, $line, $data)) {
            return [
                'date'    => $data['date'],
                'logger'  => $data['logger'],
                'level'   => $data['level'],
                'message' => $data['message'],
                'context' => json_decode($data['context'], true),
                'extra'   => json_decode($data['extra'], true)
            ];
        }

        return [];
    }
}