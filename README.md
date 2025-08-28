# WebSlinger Stored Procedure Bundle

A reusable factory for executing stored procedures on MSSQL databases using PDO and SqlSrv.

## Installation

Install the package via Composer:

```bash
composer require web-slinger/stored-procedure-bundle
```

## Configuration

1. **Register the bundle** in your `config/bundles.php`:

```php
<?php

return [
    // ... other bundles
    WebSlinger\StoredProcedureFactory\WebSlingerStoredProcedureBundle::class => ['all' => true],
];
```

2. **Run the setup command** to create configuration files:

```bash
php bin/console web-slinger:setup
```

This will:
- Create `config/packages/web_slinger.yaml` with the bundle configuration
- Add environment variables to your `.env` file

3. **Configure your database connection** by updating the environment variables in your `.env` file:

```bash
# Configure your SQL Server connection
WEB_SLINGER_SP_HOST=your-database-server
WEB_SLINGER_SP_USERNAME=your-username
WEB_SLINGER_SP_PASSWORD=your-password
```

### Alternative Manual Setup

If you prefer to set up manually, create `config/packages/web_slinger.yaml`:

```yaml
# WebSlinger Stored Procedure Bundle Configuration
webslinger:
    stored_procedure:
        hostname: '%env(WEB_SLINGER_SP_HOST)%'
        username: '%env(WEB_SLINGER_SP_USERNAME)%'
        password: '%env(WEB_SLINGER_SP_PASSWORD)%'
```

And add the environment variables to your `.env` file:

```bash
###> web-slinger/stored-procedure-bundle ###
# Configure your SQL Server connection
WEB_SLINGER_SP_HOST=your-database-server
WEB_SLINGER_SP_USERNAME=your-username
WEB_SLINGER_SP_PASSWORD=your-password
###< web-slinger/stored-procedure-bundle ###
```

## Usage

Inject the `StoredProcedureFactory` service into your controllers or services:

```php
<?php

namespace App\Controller;

use WebSlinger\StoredProcedureFactory\StoredProcedureFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MyController extends AbstractController
{
    public function __construct(
        private StoredProcedureFactory $storedProcedureFactory
    ) {}

    public function someAction(): Response
    {
        $results = $this->storedProcedureFactory->runProcedure(
            'MyStoredProcedure',
            ['param1' => 'value1', 'param2' => 'value2'],
            'MyDatabase'
        );

        // Process results...
        return $this->json($results);
    }
}
```

## Method Parameters

The `runProcedure` method accepts the following parameters:

- `$procedure` (string): Name of the stored procedure
- `$params` (array): Associative array of parameters (default: `[]`)
- `$database` (string): Database name (default: `'Storeroom'`)
- `$useSqlSrv` (bool): Use SqlSrv instead of PDO (default: `false`)
- `$returnDebugMessage` (bool): Return debug messages on error (default: `false`)
- `$serverOverride` (?string): Override the configured hostname (default: `null`)

## Requirements

- PHP >= 8.1
- ext-pdo
- Symfony >= 5.4
- SQL Server database

### Optional Extensions

- ext-sqlsrv: For native SQL Server support (when using `$useSqlSrv = true`)

## License

MIT