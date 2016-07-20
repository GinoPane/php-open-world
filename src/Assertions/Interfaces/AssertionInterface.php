<?php

namespace OpenWorld\Assertions\Interfaces;


interface AssertionInterface
{

    public function assertSingle($item);

    public function assertMultiple(array $items = []);

}