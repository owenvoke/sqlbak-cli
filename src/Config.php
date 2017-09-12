<?php

namespace pxgamer\SQLBak;

/**
 * Class Config
 * @package pxgamer\SQLBak
 */
class Config
{
    /**
     * Config constructor.
     * @param string $path
     */
    public function __construct($path)
    {
        $tmpConfig = json_decode(file_get_contents($path));

        foreach ($tmpConfig as $item => $value) {
            $this->$item = $value;
        }
    }
}