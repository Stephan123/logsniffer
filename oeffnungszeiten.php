<?php
$host = 'localhost';
$datenbank = 'test';
$user = 'test';
$password = 'test';

$connect = new mysqli($host, $user, $password, $datenbank);
mysqli_query($connect, "SET NAMES utf8");

$query = "select * from test where programmdetails_id is null";
mysqli_query($connect, $query);

if($result = mysqli_query($connect, $query)){
    $i = 1;
    while($row = mysqli_fetch_assoc($result)){
        echo $i." : ".$row['id']."<br>";
        echo "<hr>";

        // Wochentage
        for($j=1; $j < 8; $j++){
            $query = "insert into tbl_programmdetails_oeffnungszeiten (programmdetails_id, von, bis, wochentag, geschaeftstag)";
            $query .= " VALUES(".$row['id'].", '00:00:00', '23:59:00', ".$j.", 2)";

            mysqli_query($connect, $query);
        }

        $i++;
    }
}