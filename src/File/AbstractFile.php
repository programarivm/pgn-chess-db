<?php

namespace PGNChessData\File;

use PGNChessData\Exception\PgnFileCharacterEncodingException;

abstract class AbstractFile
{
    protected $filepath;

    protected $line;

    protected $result;

    public function __construct(string $filepath)
    {
        $content = file_get_contents($filepath);
        $encoding = mb_detect_encoding($content);

        if ($encoding !== 'ASCII' && $encoding !== 'UTF-8') {
            throw new PgnFileCharacterEncodingException(
                "Character encoding detected: $encoding. Needs to be UTF-8."
            );
        }

        $this->filepath = $filepath;
        $this->line = new Line;
        $this->result = (object) [
            'total' => 0,
            'valid' => 0,
        ];
    }
}
