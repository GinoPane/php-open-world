<?php

namespace OpenWorld\Data\Provider;

interface DataProviderInterface {

    public function load($fileName = '');

    public function accept();

}