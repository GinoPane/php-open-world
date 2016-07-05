<?php

namespace OpenWorld\Data\Interfaces;

interface DataFileInterface {

    public function load($fileName = '', $condition = '');

}