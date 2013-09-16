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

    private $dbhost = 'www.herden.de'; // this is the MySQL IP
    private $dbuser = 'db1154036-log55'; // those are the MySQL username & password
    private $dbpass = 'log55test!';
    private $dbname = 'db1154036-logbuch';

    private $pageSize = 25;
    private $pageIndex = 0;

    private $conn = null;

    private $startIndex = null;
    private $query = '';

    private $dataConnect = null;
    private $rows = array();

    private $sort = null;
    private $direction = null;

    public function __construct()
    {
        //Create the MySQL connection
        $this->conn = mysqli_connect($this->dbhost, $this->dbuser, $this->dbpass);

        mysqli_select_db($this->conn, $this->dbname) or die (mysql_error());
        mysqli_query($this->conn,"SET NAMES 'utf-8'");
    }

    public function __destruct()
    {
        mysqli_close($this->conn);
    }

    private function setLimit()
    {
        if ($this->pageSize > 0 && $this->pageIndex >= 0) {
            $this->startIndex = $this->pageIndex * $this->pageSize;
            $this->query .= " LIMIT $this->startIndex, $this->pageSize";
        }

        return;
    }

    private function setDirection()
    {
        if($this->sort !== NULL){
            $this->query .= " order by ".$this->sort." ".$this->direction;
        }

        return;
    }

    public function setParameter(array $parameter)
    {
        foreach ( $parameter as $key => $value ) {

            if ($key == "psize")
                $this->pageSize = $value;
            elseif($key == "page")
                $this->pageIndex = $value;
            elseif(strstr($key,'sort')){
                $this->direction = $value;

                $pattern = '#(sort\()([a-z]+)#is';
                preg_match($pattern, $key, $subpattern);

                $this->sort = $subpattern[2];
            }
        }

        return $this;
    }

    private function selectAllQuery()
    {
        $this->query = "SELECT *, CONCAT(DATE_FORMAT(datum,'%d.%m.%Y'),' ',DATE_FORMAT(zeit,'%H:%i')) AS datumDeutsch FROM gitlog";

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

    public function findenDaten(){
        $this->rows = array();

        $this->selectAllQuery();
        $this->setDirection();// direction
        $this->setLimit(); // limit

        $this->queryWork();
        $this->extractData();

        return $this;
    }

    public function anzahlDatensaetze()
    {
        $this->rows = array();

        $this->queryCountRows();
        $this->queryWork();
        $this->extractData();

        return $this;
    }
}

$search = new logSearch();

$rows = $search->setParameter($_GET)->findenDaten()->getRows();
$anzahl = $search->anzahlDatensaetze()->getRows();
$totalCount = $anzahl[0]['anzahl'];

//Serializing data to JSON array and sending it back
header("Content-type: application/json");
$response = array("totalCount" => $totalCount, "records" => $rows);
echo json_encode($response);

?>