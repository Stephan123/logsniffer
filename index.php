<?php
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

    protected $connect = null;
    protected $command = null;
    protected $history = array();

    public function __construct()
    {
        ini_set('max_execution_time', 60);

        date_default_timezone_set('Europe/London');
        $this->connect = new mysqli($this->host, $this->user, $this->password, $this->datenbank);
        mysqli_query($this->connect, "SET NAMES utf8");
    }

    public function loeschenTabelle()
    {
        $sql = "truncate table gitlog";
        mysqli_query($this->connect, $sql);

        return $this;
    }

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
        $this->output = $output;

        return;
    }

    public function steuerungEintragenDatenbank()
    {
        if(empty($this->command)){
            echo 'Kommando fehlt!';

            exit();
        }

        $this->ermittelnOutput();

        $i = 1;
        foreach($this->output as $line){
            // $i = $this->darstellenDatensatz($i, $line);
            $this->eintragenDatenbank($i, $line);
        }
		
		$datum = date("d.m.Y H:i:s");

        if($i < 2)
            echo "Datum: ".$datum." ".'Daten des Log übernommen !';
        else
            echo "Datum: ".$datum." ".'Während der Datenübernahme traten Fehler auf ! <hr>';

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

    public function eintragenDatenbank(&$i, $line){
        $insert = explode("#", $line);

        if(!is_array($insert) or count($insert) < 3)
            return;

        $insert[8] = $this->korrrekturZeit($insert[3]);
        $insert[3] = $this->korrekturDatum($insert[3]);
        $ort = $this->korrekturOrt($insert[4]);
        $insert[6] = $insert[5];
        $insert[7] = $insert[4];
        $insert[4] = $ort[0];
        $insert[5] = $ort[1];

        $kontrolle = ksort($insert);


        $keys = array(
           0 => 'hash',
           1 => 'email',
           2 => 'name',
           3 => 'datum',
           4 => 'module',
           5 => 'view',
           6 => 'beschreibung',
           7 => 'betreff',
           8 => 'zeit'
        );

        $query = "insert into gitlog (";

        foreach($keys as $key => $spalte){
            $query .= $spalte.",";
        }

        $query = substr($query,0,-1);
        $query .= ") values (";

        foreach($insert as $key => $values){
            $values = str_replace('"','\'',$values);
            $query .= '"'.$values.'",';
        }

        $query = substr($query, 0, -1);
        $query .= ")";

        $kontrolle = mysqli_query($this->connect, $query);

        if(empty($kontrolle)){
            echo $i.'. Fehler: '.$query.'<hr>';

            $i++;
        }

        return;
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

$command = 'log --all --pretty=format:"%H#%ce#%cn#%ci#%s#%b"';
// $command = 'log -1 --pretty=format:"%H#%ce#%cn#%ci#%s#%b"';

$gitLogbuch = new gitlog();
$gitLogbuch->setCommand($command)->loeschenTabelle()->steuerungEintragenDatenbank();
// $gitLogbuch->setCommand($command)->steuerungEintragenDatenbank();