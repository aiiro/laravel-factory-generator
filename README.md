<h1 align="center">Generate Laravel Factory Templates</h1>

Make Laravel factory file with the column names of the table.

### Installing
``` shell
composer require --dev aiiro/laravel-factory-generator
```

If you are using Laravel 5.5 or higher, the package will be automatically registered.

### Configuration
Optionally, you can publish the config file by running this command.
``` shell
php artisan vendor:publish --provider="Aiiro\Factory\FactoryGeneratorServiceProvider"
```
And then, you can find `config\factory-generator.php`.
``` php
<?php

return [
    
    /**
     * Set the namespace of the model.
     */
    'namespace' => [
        'model' => 'App',
    ],

    /**
     * List of the columns that will not appear in the factory.
     */
    'ignored_columns' => [
        'id',
    ],
];
```

### Usage
After installing and Configuration, you can generate the factory file by running the following command.

Please pass the table name to `generate:factory` command as the argument.

**NOTE**
This command connects to the database to retrieve the columns from table, so make sure that the database is configured.
``` shell
php artisan generate:factory some_samples
```

## License
This project is released under MIT License. See [MIT License](LICENSE)
 for the detail.
