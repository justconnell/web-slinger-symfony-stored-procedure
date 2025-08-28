# WebSlinger Stored Procedure Bundle

A reusable factory for executing stored procedures on MSSQL databases using PDO and SqlSrv.

## Installation

Install the package via Composer:

```bash
composer require web-slinger/stored-procedure-bundle
```

**Note**: The installation process will automatically:
- Create `config/packages/web_slinger.yaml` with the bundle configuration
- Add environment variables to your `.env` or `.env.local` file
- Register the bundle (if using Symfony Flex)

## Configuration

Add the bundle to your `config/bundles.php`:

```php
<?php

return [
    // ... other bundles
    WebSlinger\StoredProcedureFactory\WebSlingerStoredProcedureBundle::class => ['all' => true],
];
```

Configure the database connection in `config/packages/web_slinger.yaml`:

```yaml
web_slinger:
    stored_procedure:
        hostname: 'your-database-server'
        username: 'your-username'
        password: 'your-password'
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