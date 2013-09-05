<?php
/**
 * ermitteln der Daten der Tabelle
 * ermitteln der Anzahl der Datensätze
 *
 * @author User
 * @date 05.09.13
 * @file search.php
 */
class logSearch{

    private $dbhost = '127.0.0.1'; // this is the MySQL IP
    private $dbuser = 'test'; // those are the MySQL username & password
    private $dbpass = 'test';
    private $dbname = 'test';

    private $pageSize = 0;
    private $pageIndex = -1;

    private $conn = null;

    private $startIndex = null;
    private $query = '';

    private $dataConnect = null;
    private $rows = array();

    public function __construct()
    {
        //Create the MySQL connection
        $this->conn = mysqli_connect($this->dbhost, $this->dbuser, $this->dbpass) or die(mysql_error());
        mysqli_select_db($this->conn, $this->dbname) or die (mysql_error());
        mysqli_query($this->conn,"SET NAMES 'utf-8'");
    }

    public function __destuct()
    {
        mysqli_close($this->conn);
    }

    public function setLimit($pageSize, $pageIndex)
    {
        //paging (LIMIT)
        if ($pageSize > 0 && $pageIndex >= 0) {
            $this->startIndex = $pageIndex * $pageSize;
            $this->query = $this->query . " LIMIT $this->startIndex, $pageSize";
        }

        return $this;
    }

    public function setGetter(array $getter)
    {
        foreach ( $getter as $key => $value ) {
            // translate paging params to the SQL query
            if ($key == "psize")
                $this->pageSize = $value;
            elseif($key == "page")
                $this->pageIndex = $value;
        }

        return $this;
    }

    private function selectAllQuery()
    {
        $this->query = "select * from gitlog";

        return $this->query;
    }

    private function queryWork()
    {
        $this->dataConnect = mysqli_query($this->conn, $this->query) or die(mysqli_error($this->conn));

        return;
    }

    private function extractData()
    {
        $rows = array();
        while($row = mysqli_fetch_assoc($this->dataConnect)){
             $this->rows[] = $row;
        }

        return;
    }

    public function getRows()
    {
        return $this->rows;
    }

    private function queryCountRows()
    {
        //Select all data from Country
        $this->query = "SELECT COUNT(*) as anzahl from gitlog";

        return;
    }

    public function steuerungFindenDerDaten(){
        $this->rows = array();

        $this->selectAllQuery();
        $this->queryWork();
        $this->extractData();

        return $this;
    }

    public function steuerungErmittelnAnzahlDaten()
    {
        $this->rows = array();

        $this->queryCountRows();
        $this->queryWork();
        $this->extractData();

        return $this;
    }

//    private function orderBy()
//    {
//        $orderBy = " ORDER BY ";
//
//        $multiSort = false;
//        $multiFilter = false;
//
//        // this will go in the "foreach" statement to handle sorting
//        if (strpos($key, "sort(") >= 0 && strpos($key, "sort(") !== false) {
//
//            if ($multiSort) {
//               $orderBy = $orderBy . ", ";
//            }
//
//            $orderBy = $orderBy . substr(substr($key, 0, strlen($key)-1), strpos($key, "(") + 1) . " " . $value;
//            $multiSort = true;
//        }
//
//        //this will go right before adding the paging parameters to the query
//        if ($orderBy != " ORDER BY ") {
//             // sorting (ORDER BY)
//             $query = $query . $orderBy;
//        }
//    }

}

$search = new logSearch();
$rows = $search->steuerungFindenDerDaten()->getRows();

$anzahl = $search->steuerungErmittelnAnzahlDaten()->getRows();
$totalCount = $anzahl[0]['anzahl'];

//Serializing data to JSON array and sending it back
header("Content-type: application/json");
$response = array("totalCount" => $totalCount, "records" => $rows);
echo json_encode($response);