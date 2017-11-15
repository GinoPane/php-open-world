<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace GinoPane\PhpOpenWorld\Assertions\Interfaces;

/**
 * Interface AssertionInterface
 *
 * Provides methods for making exception-based assertions.
 *
 * @package GinoPane\PhpOpenWorld\Assertions\Interfaces
 */
interface AssertionInterface
{

    /**
     * Assertion for a single item.
     *
     * @param $item
     * @return void
     *
     * @throws \Exception
     */
    public function assertSingle($item);

    /**
     * Assertion for multiple items.
     *
     * @param array $items
     * @return void
     *
     * @throws \Exception
     */
    public function assertMultiple(array $items = []);
}
