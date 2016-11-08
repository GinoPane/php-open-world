# php-open-world

Code sniffer tool:

 php vendor/squizlabs/php_codesniffer/scripts/phpcs -s --report-full=phpcs.txt --standard=PSR2 src/

Code auto-fixer:

 php vendor/squizlabs/php_codesniffer/scripts/phpcbf --standard=PSR2 src/        
 
PhpUnit:

 php vendor/phpunit/phpunit/phpunit -c build/phpunit.xml