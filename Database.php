<?php 

/**
 * Class to execute queries using PDO and SQLite3
 * 
 * This class exists to quickly and easily execute perpared queries using the PDO wrapper for an SQLite3 database.
 * 
 * @author Nathan Kizer <hypnokizer@gmail.com>
 * @version 7.0
 * @revision 2026-05-18 Added ability to chain methods
 */

namespace Hypnokizer;

use Exception;
use PDOException;
use PDO;

class Database {

    /**
     * Last ID assigned to an insert query.
     * @access public
     * @var int
     */
    public $lastID;

    /**
     * Number of rows affected by the query.
     * @access public
     * @var int
     */
    public $naffected;

    /**
     * Number of rows returned by the query.
     * @access public
     * @var int
     */
    public $nrows;

    /**
     * PDO object for database connection.
     * @access protected
     * @var static
     */
    protected $pdo;

    /**
     * Status of the query execution.
     * @access public
     * @var bool
     */
    public $status;


    /**
     * Creates a new instance of the database class.
     * 
     * Sets the default parameters. If the optional parameter is omitted, the default values are defined variables `DBPATH` and `DB`. 
     * 
     * @param array $attr Array containing database attributes. The default values are defined variables `DBPATH` and `DB`.
     */
    public function __CONSTRUCT(array $attr = array()) {
        $this->lastID = 0;
        $this->naffected = 0;
        $this->nrows = 0;
        $this->status = NULL;

        // set defaults
        $default = array(
            'dbpath' => DBPATH,
            'db' => DB
        );

        $attr = array_merge($default, $attr);

        $dsn = 'sqlite:' . $attr['dbpath'] . $attr['db'];
       
        // connect to database
        try {
            $this->pdo = new PDO($dsn);

            // default fetch mode is associative array
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // turn on exception error handling
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } 
        catch (PDOException $e) {
            echo $e->getMessage();
        }

    }


    /**
     * Create CSV export from associative array results of a query. Filename should include the file extension. The {@link run()} method should be called first to execute the query.
     * 
     * @param string $filename Filename of CSV file including the file extension.
     * @see run()
     */
    public function createCSV(string $filename) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $fp = fopen('php://output', 'w');

        // define headers using first result
        $headers = array_keys($this->results[0]);
        fputcsv($fp, $headers, ',', '"');

        foreach($this->results as $row) {
            fputcsv($fp, $row, ',', '"');
        }

        fclose($fp);
    }


    /**
     * Executes a query and optional parameters for prepared statements.
     * 
     * @param string $query Query string to execute. May use prepared statements.
     * @param array $params Array containing parameter values referenced in query statement.
     * @see createCSV()
     */
    public function run(string $query, array $params = NULL) {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);

            $this->lastID = $this->pdo->lastInsertId(); // find last inserted row ID
            $this->naffected = $stmt->rowCount(); // find number of rows affected by query
            $this->results = $stmt->fetchAll(); // store results
            $this->nrows = count($this->results);
            $this->status = true;
        }
        catch(Exception $e) {
            $this->status = false;
        }
    }


    /**
     * Display the entire object for debugging purposes.
     * 
     * @return string
     */
    public function showObject() {
        echo '<pre>';
        print_r($this);
        echo '</pre>';
    }


    /**
     * Display all database query results.
     * 
     * @return string
     */
    public function showResults() {
        echo '<pre>';
        echo $this->nrows . ' results:';
        print_r($this->results);
        echo '</pre>';
    }


} // end class 

?>