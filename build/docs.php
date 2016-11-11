<?php

/**
 * Script for updating of OpenWorld's documentation.
 *
 * There's no need to split the script into many files, that's why everything what it needs is placed in the same file.
 *
 * Initially Based on Punic's update-docs script (https://github.com/punic/punic)
 *
 * @author Sergey <Gino Pane> Karavay
 */

require 'shared/functions.php';

define('ROOT_DIR', dirname(__DIR__));
define('SOURCE_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'src');
define('DESTINATION_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'docs');
define('PHPDOC_PATH', ROOT_DIR . DIRECTORY_SEPARATOR .
    'vendor' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'phpdoc');

try {
    echo "Initializing... \n";

    cleanUpDestinationDirectory();
    createDirectory(DESTINATION_DIR);

    echo "Creating doc files... \n";

    $output = array();
    exec(
        escapeshellarg(PHPDOC_PATH) .
        " -d " . escapeshellarg(SOURCE_DIR) .
        " -t " . escapeshellarg(DESTINATION_DIR) .
        " --template=\"responsive-twig\"" .
        " --title=\"Php Open World\"",
        $output,
        $rc
    );

    if ($rc !== 0) {
        throw new Exception("PhpDocumentor failed:\n" . trim(implode("\n", $output)));
    }

    echo "Done.\n";

    exit(0);
} catch (Exception $x) {
    echo $x->getMessage(), "\n";

    exit(1);
}