<?php


/* ============================================================================
 * EXAMPLE USAGE AND DEMONSTRATIONS
 * ============================================================================ */

use App\Libraries\DatabaseSP\DatabaseSP;
use App\Libraries\DatabaseSP\Driver;
use App\Libraries\DatabaseSP\FetchMode;
use App\Libraries\DatabaseSp\DB;
use App\Libraries\DatabaseSP\DatabaseSPException;


class ExampleUsageDatabaseSP
{
    public int $id;
    public string $name;
    public string $email;
    public ?string $phone;


    /**
     * Example 1: MySQL Connection and Simple SP Calls
     */
    static function exampleMySqlUsage(): void
    {
        // Configuration for MySQL
        $mysqlConfig = [
            'driver'   => Driver::MYSQL,
            'host'     => 'localhost',
            'port'     => 3306,
            'database' => 'pdam_database',
            'username' => 'root',
            'password' => 'secret',
            'charset'  => 'utf8mb4',
        ];

        // Create database instance
        $db = new DatabaseSP($mysqlConfig);

        // Example 1: Simple SP call without parameters
        $results = $db->call('sp_get_all_customers')->fetchAll();
        print_r($results);

        // Example 2: SP call with parameters (equivalent to: CALL sp_closing(1, 5))
        $pdamId = 1;
        $ofId = 5;
        $results = $db->call('sp_closing', [
            'pdam_id' => $pdamId,
            'of_id'   => $ofId,
        ])->fetchAll();
        print_r($results);

        // Example 3: Using raw query with positional parameters
        $sql = "CALL sp_closing(?, ?)";
        $results = $db->query($sql, [$pdamId, $ofId])->fetchAll();
        print_r($results);

        // Example 4: Fetch single row
        $customer = $db->call('sp_get_customer_by_id', [
            'customer_id' => 123,
        ])->fetchOne();
        print_r($customer);

        // Example 5: Different fetch modes
        $results = $db->call('sp_get_products')->fetchAll(FetchMode::OBJ);
        foreach ($results as $product) {
            echo $product->name . "\n";
        }

        // Example 6: Execute SP that modifies data
        $affectedRows = $db->execute('sp_update_customer_status', [
            'customer_id' => 123,
            'status'      => 'active',
        ]);
        echo "Updated {$affectedRows} rows\n";

        // Example 7: Using transactions
        $db->transaction(function (DatabaseSP $db) {
            $db->call('sp_create_order', [
                'customer_id' => 123,
                'total'       => 500000,
            ]);

            $db->call('sp_update_inventory', [
                'product_id' => 456,
                'quantity'   => -1,
            ]);
        });
    }

    /**
     * Example 2: SQL Server Connection and SP Calls
     */
    static function exampleSqlServerUsage(): void
    {
        // Configuration for SQL Server
        $sqlServerConfig = [
            'driver'   => Driver::SQLSERVER,
            'host'     => '192.168.1.100',
            'port'     => 1433,
            'database' => 'akuntansi_db',
            'username' => 'sa',
            'password' => 'YourSecurePassword',
            'schema'   => 'akuntansi', // Default schema
        ];

        $db = new DatabaseSP($sqlServerConfig);

        // Example 1: Simple SP call
        // Generates: SET NOCOUNT ON; EXEC [akuntansi].[sp_get_journals]
        $journals = $db->call('sp_get_journals')->fetchAll();
        print_r($journals);

        // Example 2: SP with multiple parameters (like your original example)
        // Generates: SET NOCOUNT ON; EXEC [akuntansi].[transjurnal_dhhd_upsert_mjournal_v2] 
        //            @vid = :param0, @vmjo_date = :param1, ...
        $result = $db->call('transjurnal_dhhd_upsert_mjournal_v2', [
            'vid'           => 'JRN-2024-001',
            'vmjo_date'     => '2024-01-15',
            'vmjo_desc'     => 'Monthly closing journal',
            'vmjo_amount'   => 15000000.00,
            'vcreated_by'   => 'admin',
        ])->fetchOne();
        print_r($result);

        // Example 3: Raw query (original style)
        $sql = "SET NOCOUNT ON; EXEC [akuntansi].transjurnal_dhhd_upsert_mjournal_v2 
            @vid = :vid, @vmjo_date = :vmjo_date, @vmjo_desc = :vmjo_desc";
        $result = $db->query($sql, [
            ':vid'       => 'JRN-2024-002',
            ':vmjo_date' => '2024-01-16',
            ':vmjo_desc' => 'Daily transaction',
        ])->fetchOne();
        print_r($result);

        // Example 4: SP with different schema
        $result = $db->call('sp_get_user', ['user_id' => 1], 'dbo')->fetchOne();
        print_r($result);

        // Example 5: Multiple result sets
        $resultSets = $db->call('sp_get_dashboard_data', [
            'user_id' => 1,
        ])->fetchAllResultSets();

        $summaryData = $resultSets[0] ?? [];
        $detailData = $resultSets[1] ?? [];
        $chartData = $resultSets[2] ?? [];

        // Example 6: Using query builder for complex scenarios
        $builder = $db->builder();
        $query = $builder
            ->procedure('sp_complex_report', 'reporting')
            ->param('start_date', '2024-01-01')
            ->param('end_date', '2024-12-31')
            ->param('include_details', true, PDO::PARAM_BOOL)
            ->param('department_id', null, PDO::PARAM_NULL)
            ->noCount(true)
            ->build();

        // Execute the built query
        $db->query($query['sql'], array_column($query['bindings'], 'value'));
        $report = $db->fetchAll();
        print_r($report);
    }

    /**
     * Example 3: Using the Static Factory Helper
     */
    static function exampleStaticFactory(): void
    {
        // Register connections
        DB::addConnection([
            'driver'   => Driver::MYSQL,
            'host'     => 'localhost',
            'database' => 'main_db',
            'username' => 'root',
            'password' => 'secret',
        ], 'mysql');

        DB::addConnection([
            'driver'   => Driver::SQLSERVER,
            'host'     => '192.168.1.100',
            'database' => 'accounting_db',
            'username' => 'sa',
            'password' => 'password',
            'schema'   => 'akuntansi',
        ], 'sqlserver');

        // Use MySQL connection
        $customers = DB::connection('mysql')
            ->call('sp_get_customers')
            ->fetchAll();

        // Use SQL Server connection
        $journals = DB::connection('sqlserver')
            ->call('sp_get_journals', ['year' => 2024])
            ->fetchAll();

        // Set default and use static methods
        DB::setDefaultConnection('mysql');
        $results = DB::call('sp_simple_procedure')->fetchAll();
    }

    /**
     * Example 4: Error Handling
     */
    static function exampleErrorHandling(): void
    {
        $config = [
            'driver'   => Driver::MYSQL,
            'host'     => 'localhost',
            'database' => 'test_db',
            'username' => 'root',
            'password' => 'secret',
        ];

        $db = new DatabaseSP($config);

        try {
            $results = $db->call('sp_nonexistent_procedure', [
                'param1' => 'value1',
            ])->fetchAll();
        } catch (DatabaseSPException $e) {
            echo "Error: " . $e->getMessage() . "\n";
            echo "SQL State: " . $e->getSqlState() . "\n";
            echo "Error Info: " . print_r($e->getErrorInfo(), true) . "\n";

            // Log the error
            error_log("Database SP Error: " . $e->getMessage());
        }
    }

    /**
     * Example 5: Query Logging for Debugging
     */
    static function exampleQueryLogging(): void
    {
        $config = [
            'driver'   => Driver::MYSQL,
            'host'     => 'localhost',
            'database' => 'test_db',
            'username' => 'root',
            'password' => 'secret',
        ];

        $db = new DatabaseSP($config);
        $db->enableQueryLog();

        // Execute some queries
        $db->call('sp_procedure1', ['id' => 1])->fetchAll();
        $db->call('sp_procedure2', ['name' => 'test'])->fetchAll();

        // Get the query log
        $log = $db->getQueryLog();
        foreach ($log as $entry) {
            echo "SQL: {$entry['sql']}\n";
            echo "Time: {$entry['time']}s\n";
            echo "---\n";
        }

        $db->disableQueryLog();
        $db->clearQueryLog();
    }


    static function exampleFetchAsClass(): void
    {
        $config = [
            'driver'   => Driver::MYSQL,
            'host'     => 'localhost',
            'database' => 'test_db',
            'username' => 'root',
            'password' => 'secret',
        ];

        $db = new DatabaseSP($config);

        // Fetch all results as Customer objects
        $customers = $db->call('sp_get_customers')
            ->fetchAll(FetchMode::CLASS_TYPE, ExampleCustomer::class);

        foreach ($customers as $customer) {
            echo "Customer: {$customer->name} ({$customer->email})\n";
        }

        // Fetch single result as Customer object
        $customer = $db->call('sp_get_customer_by_id', ['id' => 123])
            ->fetchOne(FetchMode::CLASS_TYPE, ExampleCustomer::class);

        if ($customer) {
            echo "Found: {$customer->name}\n";
        }
    }


    // Usage of AccountingService
    function demonstrateAccountingService(): void
    {
        // For MySQL PDAM system
        $mysqlDb = new DatabaseSP([
            'driver'   => Driver::MYSQL,
            'host'     => 'localhost',
            'database' => 'pdam_db',
            'username' => 'root',
            'password' => 'secret',
        ]);

        // For SQL Server Accounting system
        $sqlServerDb = new DatabaseSP([
            'driver'   => Driver::SQLSERVER,
            'host'     => '192.168.1.100',
            'database' => 'accounting_db',
            'username' => 'sa',
            'password' => 'password',
            'schema'   => 'akuntansi',
        ]);

        // MySQL usage
        $mysqlService = new ExampleAccounting($mysqlDb);
        $closingData = $mysqlService->getClosingData(1, 5);
        print_r($closingData);

        // SQL Server usage
        $sqlServerService = new ExampleAccounting($sqlServerDb);
        $result = $sqlServerService->upsertJournalEntry([
            'id'          => 'JRN-2024-001',
            'date'        => '2024-01-15',
            'description' => 'Monthly closing',
            'amount'      => 15000000,
            'type'        => 'CLOSING',
            'created_by'  => 'admin',
        ]);
        print_r($result);
    }
}
