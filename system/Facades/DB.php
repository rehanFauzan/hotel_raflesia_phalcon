<?php
namespace Core\Facades;

/**
 * Class DB
 * @package Core\Facades
 * 
 * 
 * @method static int affectedRows()
 * Returns the number of affected rows by the latest INSERT/UPDATE/DELETE
 * executed in the database system
 *
 * ```php
 * DB::execute(
 *     "DELETE FROM robots"
 * );
 *
 * echo DB::affectedRows(), " were deleted";
 * ```
 *
 * @method static bool begin(bool $nesting = true)
 * Starts a transaction in the connection
 *
 * @method static bool commit(bool $nesting = true)
 * Commits the active transaction in the connection
 *
 * @method static bool close()
 * Closes the active connection returning success. Phalcon automatically
 * closes and destroys active connections when the request ends
 *
 * @method static bool connect(array $descriptor = null)
 * This method is automatically called in \Phalcon\Db\Adapter\Pdo
 * constructor.
 *
 * @method static array convertBoundParams(string $sql, array $params = array())
 * Converts bound parameters such as :name: or ?1 into PDO bind params ?
 *
 * ```php
 * print_r(
 *     DB::convertBoundParams(
 *         "SELECT FROM robots WHERE name = :name:",
 *         [
 *             "Bender",
 *         ]
 *     )
 * );
 * ```
 *
 * @method static string escapeString(string $str)
 * Escapes a value to avoid SQL injections according to the active charset
 * in the connection
 *
 * ```php
 * $escapedStr = DB::escapeString("some dangerous value");
 * ```
 *
 * @method static bool execute(string $sqlStatement, $bindParams = null, $bindTypes = null)
 * Sends SQL statements to the database server returning the success state.
 * Use this method only when the SQL statement sent to the server doesn't
 * return any rows
 *
 * ```php
 * // Inserting data
 * $success = DB::execute(
 *     "INSERT INTO robots VALUES (1, 'Astro Boy')"
 * );
 *
 * $success = DB::execute(
 *     "INSERT INTO robots VALUES (?, ?)",
 *     [
 *         1,
 *         "Astro Boy",
 *     ]
 * );
 * ```
 *
 * @method static \PDOStatement executePrepared(\PDOStatement $statement, array $placeholders, $dataTypes)
 * Executes a prepared statement binding. This function uses integer indexes
 * starting from zero
 *
 * ```php
 * use Phalcon\Db\Column;
 *
 * $statement = DB::prepare(
 *     "SELECT FROM robots WHERE name = :name"
 * );
 *
 * $result = DB::executePrepared(
 *     $statement,
 *     [
 *         "name" => "Voltron",
 *     ],
 *     [
 *         "name" => Column::BIND_PARAM_INT,
 *     ]
 * );
 * ```
 *
 * @method static mixed getErrorInfo()
 * Return the error info, if any
 *
 * @method static \PDO getInternalHandler()
 * Return internal PDO handler
 *
 * @method static int getTransactionLevel()
 * Returns the current transaction nesting level
 *
 * @method static bool isUnderTransaction()
 * Checks whether the connection is under a transaction
 *
 * ```php
 * DB::begin();
 *
 * // true
 * var_dump(
 *     DB::isUnderTransaction()
 * );
 * ```
 *
 * @method static int|bool lastInsertId($sequenceName = null)
 * Returns the insert id for the auto_increment/serial column inserted in
 * the latest executed SQL statement
 *
 * ```php
 * // Inserting a new robot
 * $success = DB::insert(
 *     "robots",
 *     [
 *         "Astro Boy",
 *         1952,
 *     ],
 *     [
 *         "name",
 *         "year",
 *     ]
 * );
 *
 * // Getting the generated id
 * $id = DB::lastInsertId();
 * ```
 *
 * @method static \PDOStatement prepare(string $sqlStatement)
 * Returns a PDO prepared statement to be executed with 'executePrepared'
 *
 * ```php
 * use Phalcon\Db\Column;
 *
 * $statement = DB::prepare(
 *     "SELECT FROM robots WHERE name = :name"
 * );
 *
 * $result = DB::executePrepared(
 *     $statement,
 *     [
 *         "name" => "Voltron",
 *     ],
 *     [
 *         "name" => Column::BIND_PARAM_INT,
 *     ]
 * );
 * ```
 *
 * @method static bool|\Phalcon\Db\ResultInterface query(string $sqlStatement, $bindParams = null, $bindTypes = null)
 *
 * Sends SQL statements to the database server returning the success state.
 * Use this method only when the SQL statement sent to the server is
 * returning rows
 *
 * ```php
 * // Querying data
 * $resultset = DB::query(
 *     "SELECT FROM robots WHERE type = 'mechanical'"
 * );
 *
 * $resultset = DB::query(
 *     "SELECT FROM robots WHERE type = ?",
 *     [
 *         "mechanical",
 *     ]
 * );
 * ```
 *
 * @method static bool rollback(bool $nesting = true)
 * Rollbacks the active transaction in the connection
 */
class DB extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'db';
    }
}