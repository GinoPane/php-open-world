<?php
/**
 * PHP OpenWorld
 *
 * @author: Sergey <Gino Pane> Karavay
 */

namespace OpenWorld\Exceptions;

use OpenWorld\Exceptions\AbstractClasses\ExceptionAbstract;

/**
 * Class FileNotValidException
 *
 * An exception raised when a data file was not valid.
 *
 * @package OpenWorld\Exceptions
 */
class FileNotValidException extends ExceptionAbstract
{
    /**
     * The path to the invalid file
     *
     * @var string
     */
    protected $filePath;

    /**
     * Initializes the instance.
     *
     * @param string $filePath The path to the invalid file
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;

        $message = "Specified file is not valid: '$filePath'";

        parent::__construct($message);
    }

    /**
     * Retrieves the path to the unreadable file.
     *
     * @return string
     */
    public function getFilePath() : string
    {
        return $this->filePath;
    }
}
