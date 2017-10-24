<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Data\AbstractClasses;

use OpenWorld\Data\Interfaces\SourceLoaderResultInterface;
use OpenWorld\Exceptions\InvalidContentException;
use OpenWorld\Exceptions\NotImplementedException;
use stdClass;

/**
 * Class SourceLoaderResultAbstract
 *
 * @package OpenWorld\Data\AbstractClasses
 */
abstract class SourceLoaderResultAbstract implements SourceLoaderResultInterface
{

    /**
     * Stored raw content
     *
     * @var
     */
    protected $content;

    /**
     * Get result data as string
     *
     * @inheritdoc
     *
     * @throws NotImplementedException
     */
    public function asString(): string
    {
        throw new NotImplementedException(__FUNCTION__); // @codeCoverageIgnore
    }

    /**
     * Get result data as array
     *
     * @inheritdoc
     *
     * @throws NotImplementedException
     */
    public function asArray(): array
    {
        throw new NotImplementedException(__FUNCTION__); // @codeCoverageIgnore
    }

    /**
     * Get result data as object
     *
     * @return mixed
     *
     * @throws NotImplementedException
     */
    public function asObject()
    {
        throw new NotImplementedException(__FUNCTION__); // @codeCoverageIgnore
    }

    /**
     * Set result content
     *
     * @param $content
     * @return mixed
     */
    public function setContent($content)
    {
        $this->assert($content);

        $this->content = $content;
    }

    /**
     * Get result's content
     *
     * @inheritdoc
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Checks whether content is valid for the result
     *
     * @param $content
     * @return bool
     */
    abstract public function isValid($content): bool;

    /**
     * Makes sure that $content is valid for this SourceLoaderResultAbstract instance
     *
     * @param $content
     *
     * @throws InvalidContentException
     */
    protected function assert($content)
    {
        if (!$this->isValid($content)) {
            throw new InvalidContentException(); // @codeCoverageIgnore
        }
    }
}
