<?php
/**
 * Functions shared between build scripts
 */

/**
 * Enable/Disable supporting output of some functions
 */
$disableOutput = false;

/**
 * Set flag of disabled output
 */
function disableOutput()
{
    global $disableOutput;

    $disableOutput = true;
}

/**
 * Set flag of enabled output
 */
function enableOutput()
{
    global $disableOutput;

    $disableOutput = false;
}

/**
 * Check output flag
 *
 * @return bool
 */
function outputEnabled()
{
    global $disableOutput;

    return $disableOutput == false;
}

/**
 * Custom error handler
 *
 * @param $errorNumber
 * @param $errorString
 * @param $errorFile
 * @param $errorLine
 * @throws Exception
 */
function handleError($errorNumber, $errorString, $errorFile, $errorLine)
{
    if ($errorNumber == E_NOTICE || $errorNumber == E_WARNING) {
        throw new Exception("$errorString in $errorFile @ line $errorLine \n", $errorNumber);
    }
}

/**
 * Check existence, create directory and handle any possible error
 *
 * @param string $directory
 * @return bool
 * @throws Exception
 */
function createDirectory($directory = "")
{
    if (outputEnabled()) {
        echo "Creating \"$directory\" folder... ";
    }

    if (!is_dir($directory)) {
        if (mkdir($directory, 0777, false) === false) {
            throw new Exception("Failed to create \"$directory\"\n");
        }
    }

    if (outputEnabled()) {
        echo "Done.\n";
    }

    return true;
}

/**
 * Delete object specified by its path from the filesystem
 *
 * @param $path
 * @throws Exception
 */
function deleteFromFilesystem($path)
{
    if (is_file($path)) {
        if (unlink($path) === false) {
            throw new Exception("Failed to delete file $path");
        }
    } else {
        $contents = scandir($path);
        if ($contents === false) {
            throw new Exception("Failed to retrieve the file list of $path");
        }
        foreach (array_diff($contents, ['.', '..']) as $item) {
            deleteFromFilesystem($path . DIRECTORY_SEPARATOR . $item);
        }
        if (rmdir($path) === false) {
            throw new Exception("Failed to delete directory $path");
        }
    }
}

/**
 * Clean-up destination directory before build
 */
function cleanUpDestinationDirectory()
{
    if (is_dir(DESTINATION_DIR)) {
        echo "Cleanup old general data folder... ";
        deleteFromFilesystem(DESTINATION_DIR);
        echo "Done.\n";
    }
}

set_error_handler('handleError');