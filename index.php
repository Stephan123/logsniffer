<?php

set_time_limit(120);

/**
 * schreibt die Logdatei eines Git Log in eine Tabelle
 *
 * %H 'commit hash'
 * %n 'new line'
 * %ce 'committer email'
 * %cn 'committer name'
 * %ci 'committer date' , ISO 8601
 * %s 'subject'
 * %b 'body'
 *
 *

CREATE TABLE `gitlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `datum` date NOT NULL,
  `zeit` time NOT NULL,
  `module` varchar(100) NOT NULL,
  `view` varchar(100) NOT NULL,
  `beschreibung` text NOT NULL,
  `betreff` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `Volltext` (`beschreibung`)
) ENGINE=MyISAM AUTO_INCREMENT=555 DEFAULT CHARSET=utf8

 *
 *
 * @author Stephan.Krauss
 * @date 14.00.2013
 * @file index.php
 */
class gitlog
{
    protected $dir = "c:/xampp/htdocs/hob/";
    protected $git = "C:/Program Files (x86)/Git/bin/git.exe";

    protected $output = array();

    protected $host = 'www.herden.de';
    protected $datenbank = 'db1154036-logbuch';
    protected $tabelle = 'gitlog';
    protected $user = 'db1154036-log55';
    protected $password = 'log55test!';

//    protected $host = 'localhost';
//    protected $datenbank = 'test';
//    protected $tabelle = 'gitlog';
//    protected $user = 'test';
//    protected $password = 'test';

    protected $connect = null;
    protected $command = null;
    protected $history = array();

    protected $zaehler = 0;

    public function __construct()
    {
        ini_set('max_execution_time', 60);

        date_default_timezone_set('Europe/London');
        $this->connect = new mysqli($this->host, $this->user, $this->password, $this->datenbank);
        mysqli_query($this->connect, "SET NAMES utf8");
    }

    /**
     * @return gitlog
     */
    public function loeschenTabelle()
    {
        $sql = "truncate table gitlog";
        mysqli_query($this->connect, $sql);

        return $this;
    }

    /**
     * @param $command
     * @return gitlog
     */
    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    private function ermittelnOutput()
    {
        $output = array();
        chdir($this->dir);
        exec('"C:\Program Files (x86)\Git\bin\git.exe" ' .$this->command, $output);

        foreach($output as $key => $value){
            if(empty($value))
                unset($output[$key]);
        }

        $outputClean = array_merge($output);

        $this->output = $outputClean;

        return;
    }

    /**
     * @return gitlog
     */
    public function steuerungEintragenDatenbank()
    {
        if(empty($this->command)){
            echo 'Kommando fehlt!';

            exit();
        }

        // list Inhalt Logdatei in CSV Datei
        $this->ermittelnOutput();

        for($i=1;  $i<= count($this->output);  $i++){
            $this->eintragenDatenbank($this->output[$i]);
            echo $i.'<hr>';
        }

		$datum = date("d.m.Y H:i:s");

        echo "<hr>Datum: ".$datum." ".'Daten des Log übernommen !';

        return $this;
    }

    private function darstellenDatensatz(&$i, $line)
    {
        if(empty($line)){
            echo "<br>";

            return;
        }


        echo $i." : ".$line."<br>";

        return $i;
    }

    /**
     * @param $line
     * @return gitlog
     */
    public function eintragenDatenbank($line){
        $teile = explode(";", $line);

        if(count($teile) < 3)
            return;

        $ort = $this->korrekturOrt($teile[4]);

        $insert = array(
            'hash' => $teile[0],
            'email' => $teile[1],
            'name' => $teile[2],
            'datum' => $this->korrekturDatum($teile[3]),
            'zeit' => $this->korrrekturZeit($teile[3]),
            'module' => $ort[0],
            'view' => $ort[1],
            'beschreibung' => $teile[5],
            'betreff' => $teile[4]
        );

        $fields = "";
        $values = "";

        foreach($insert as $field => $value){
            $fields .= $field.",";

            $values .= '"'.htmlspecialchars($value).'",';
        }

        $fields = substr($fields, 0, -1);
        $values = substr($values, 0, -1);

        $query = "insert into gitlog (";
        $query .= $fields.") ";
        $query .= "values(";
        $query .= $values.")";

        mysqli_query($this->connect, $query);

        return $this;
    }

    private function korrekturDatum($datum)
    {
        $datum = trim($datum);
        $datum = substr($datum, 0, -15);

        return $datum;
    }

    private function korrrekturZeit($datum)
    {
        $zeit = substr($datum, 11, -6);

        return $zeit;
    }

    private function korrekturOrt($ablage)
    {
        $ort = array();
        $ablageTeile = explode("/",$ablage);

        $i = 0;
        foreach($ablageTeile as $value){
            if($i > 1)
                break;

            if(empty($value))
                continue;

            $ort[$i] = $value;
            $i++;
        }

        if(!isset($ort[0]))
            $ort[0] = $ablage;
        if(!isset($ort[1]))
            $ort[1] = $ablage;

        return $ort;
    }
}

$command = 'log --all --pretty=format:"%H;%ce;%cn;%ci;%s;%b"';
// $command = 'log -1 --pretty=format:"%H;%ce;%cn;%ci;%s;%b"';

$gitLogbuch = new gitlog();
 $gitLogbuch
    ->setCommand($command)
    ->loeschenTabelle()
    ->steuerungEintragenDatenbank();
	
// $gitLogbuch
//  ->setCommand($command)
//  ->steuerungEintragenDatenbank();