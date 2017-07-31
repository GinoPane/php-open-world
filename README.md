# php-open-world

## Build Tools

### Build CLDR data

```Basic usage: 
php build.php [options]

It will try to download the latest CLDR sources defined by version used. Do not manually change the version, 
except you really need it for anything.

Options:
    --cldr-version - override CLDR version
    --post-clean - try to clean temporary directory after build;
    --debug - generate readable json files;
    --all-locales - generate the full list of available locales; by default the most popular languages are processed;
    --help - display this help.
```


```php build/data.php --debug --cldr-version=31-d02```

> Please note, that SVN is required to download new data from CLDR servers


### Build documentation

 ```php build/docs.php```

## Other Tools

### Code sniffer tool:

 ```php vendor/squizlabs/php_codesniffer/scripts/phpcs -s --report-full=phpcs.txt --standard=PSR2 src/```

### Code auto-fixer:

 ```php vendor/squizlabs/php_codesniffer/scripts/phpcbf --standard=PSR2 src/```    
 
### PhpUnit:

 ```php vendor/phpunit/phpunit/phpunit -c build/phpunit.xml```
 
## Useful Links

[Standard country or area codes for statistical use (M49)](https://unstats.un.org/unsd/methodology/m49/)
 
 
