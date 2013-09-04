<?php
$pageSize = 0;
$pageIndex = -1;

foreach ( $_GET as $key => $value ) {
    // translate paging params to the SQL query
   if ($key == "psize") {
        $pageSize = $value;
   }
   else if ($key == "page")
   {
        $pageIndex = $value;
    }
}


$dbhost = '127.0.0.1'; // this is the MySQL IP
$dbuser = 'test'; // those are the MySQL username & password
$dbpass = 'test';

//Create the MySQL connection
$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die(mysql_error());
$dbname = 'test';

mysql_select_db($dbname) or die (mysql_error());

$query = "select * from gitlog";

//paging (LIMIT)
if ($pageSize > 0 && $pageIndex >= 0) {
    $startIndex = $pageIndex * $pageSize;
    $query = $query . " LIMIT $startIndex, $pageSize";
}

$orderBy = " ORDER BY ";

$multiSort = false;
$multiFilter = false;

// this will go in the "foreach" statement to handle sorting
if (strpos($key, "sort(") >= 0 && strpos($key, "sort(") !== false) {

    if ($multiSort) {
       $orderBy = $orderBy . ", ";
    }

    $orderBy = $orderBy . substr(substr($key, 0, strlen($key)-1), strpos($key, "(") + 1) . " " . $value;
    $multiSort = true;
}

//this will go right before adding the paging parameters to the query
if ($orderBy != " ORDER BY ") {
     // sorting (ORDER BY)
     $query = $query . $orderBy;
}


$data = mysql_query($query) or die(mysql_error());

//Select all data from Country
$recQ = "SELECT COUNT(*) from gitlog";

$recCount = mysql_query($recQ) or die (mysql_error());

$rows = array();
while($r = mysql_fetch_assoc($data)) {
     $rows[] = $r;
}

//Serializing data to JSON array and sending it back
header("Content-type: application/json");
$arr = mysql_fetch_array($recCount);
$response = array("totalCount" => $arr[0], "records" => $rows);
echo json_encode($response);

mysql_close($conn);



