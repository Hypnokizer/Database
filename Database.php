<?php 

namespace App\Controllers;

use Exception;
use PDOException;
use PDO;


class Database {

    protected $lastID;
    protected $naffected;
    protected $nrows;
    protected $pdo;
    protected $status;


    public function __CONSTRUCT($attr = array()) {
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
     * create CSV export from associative array results of a query
     * use a file extension in the name
     */
    public function createCSV($filename) {
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








    public function run($query, $params = NULL) {
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






    public function showObject() {
        echo '<pre>';
        print_r($this);
        echo '</pre>';
    }





    public function showResults() {
        echo '<pre>';
        echo $this->nrows . ' results:';
        print_r($this->results);
        echo '</pre>';
    }



} // end class 


?>