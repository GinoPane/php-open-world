<?php

namespace OpenWorld\Data;

use OpenWorld\Data\Interfaces\DataFileInterface;

class DataFile implements DataFileInterface {

    public function load($fileName = '', $condition = '')
    {
        // TODO: Implement load() method.
        foreach ($this->providers() as $provider) {
            if ($provider->accept($condition)) {
                $provider->load($fileName)->asArray();
            }
        }
    }
}