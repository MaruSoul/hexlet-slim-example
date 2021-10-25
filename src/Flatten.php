<?php

namespace Fakeldev\HexletSlimExample;

class Flatten
{
    public function flatten($collection, $depth = 1)
    {
        $flatten = new Flatten();
        $result = [];

        foreach ($collection as $value) {
            if (is_array($value) && $depth > 0) {
                $result = array_merge($result, $flatten->flatten($value, $depth - 1));
            } else {
                $result[] = $value;
            }
        }

        return $result;
    }
}
