# SQLite3 class
This class exists to quickly and easily execute perpared queries using the PDO wrapper for an SQLite3 database.

## Before Use

Create the database file. It should have an extension of `*.db`. The directory and the file itself should be fully writeable. 




## Basic Use
Instantiate the object. The only parameter is an array containing database credentials. The default values are variables defined in an include file, but you can overwrite them for each instance. A try/catch block is used to set up the connection and will throw an error if the database cannot connect.
```
define('DBPATH', '../app/database/');
define('DB', 'chinook.db');


$attr = array(
    'dbpath' => DBPATH,
    'db' => DB
);

$db = new db($attr);
```

The `run()` method is the main method used. It determines the type of query to execute based on the first word of the query and stores values within class properties which can be accessesd by the user.






#### Simple Query

There are (2) parameters. The first, and only required, parameter is the query. Simple queries can be executed like this.

```
$query = 'SELECT * FROM thetable ORDER BY id';

$db->run($query);
```



#### Prepared Statement

To include user input, prepared statements are required. The `data` parameter contains the data to bind to the `?` placeholders.

```
$query = 'SELECT * FROM thetable WHERE id = ? OR name = ?';

$data = array(3, 'Nathan');

$db->run($query, $data);

```



#### Results

The results from `SELECT` queries are stored in a multidimensional array called `results`. Each record is an associative array. This can be accessed with a `foreach()` loop.

```
foreach($db->results as $row) {
    echo $row['fieldname'];
}
```


Depending upon the type of query executed, there are several values stored in class properties.

* `$db->nrows` - number of rows returned
* `$db->naffected` - number of rows affected
* `$db->lastID` - last ID inserted









## Creating a CSV File

There is also a method to create a CSV file from the record set. To do this, you must first execute the query by calling the `run()` method. Rather than step through the results array, you have the option of calling the `createCSV()` method.

The method takes one parameter, which is a filename, including extension. 

```
$db->run($query, $data);

$db->createCSV('myfilename.csv');
```






## Debugging

There are two methods used for debugging. The `showResults()` method shows a summary array for the query. The `showObject()` method shows the entire PDO object. These methods can be called at any point in the script.

