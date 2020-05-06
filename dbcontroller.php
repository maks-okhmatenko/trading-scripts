<?php
class DBController
{
    private $conn = "";
    public $url = "http://85.17.172.72:3306/";
    private $host = "85.17.172.72";
    private $user = "admin";
    private $password = "Hello123";
    private $database = "integration_live";

    public function __construct()
    {
        $conn = $this->connectDB();
        if (!empty($conn)) {
            $this->conn = $conn;
        }
    }

    public function escape($str) {
        return mysqli_escape_string($this->conn, $str);
    }

    public function connectDB()
    {
        $conn = mysqli_connect($this->host, $this->user, $this->password, $this->database);
        return $conn;
    }

    public function executeQuery($query)
    {
        $conn = $this->connectDB();
        $result = mysqli_query($conn, $query);
        if (!$result) {
            //check for duplicate entry
            if ($conn->errno == 1062) {
                return false;
            } else {
                trigger_error(mysqli_error($conn), E_USER_NOTICE);

            }
        }

        $affectedRows = mysqli_affected_rows($conn);
        return $affectedRows;

    }

    public function executeQueryLastId($query)
    {
        $conn = $this->connectDB();
        $result = mysqli_query($conn, $query);
        if (!$result) {
            //check for duplicate entry
            if ($conn->errno == 1062) {
                return false;
            } else {
                trigger_error(mysqli_error($conn), E_USER_NOTICE);

            }
        }

        $last_id = mysqli_insert_id($conn);
        return $last_id;

    }

    public function executeSelectQuery($query)
    {
        $result = mysqli_query($this->conn, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        if (!empty($resultset)) {
            return $resultset;
        }

    }
}
