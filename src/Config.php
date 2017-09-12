<?php

namespace pxgamer\SQLBak;

class Config
{
    public function __construct($path)
    {
        $tmpConfig = json_decode(file_get_contents($path));

        foreach ($tmpConfig as $item => $value) {
            $this->$item = $value;
        }
    }
}