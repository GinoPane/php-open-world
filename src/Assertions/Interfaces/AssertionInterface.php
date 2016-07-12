<?php

namespace OpenWorld\Assertions\Interfaces;

interface AssertionInterface
{
    public function assert(...$conditions) : bool;
}