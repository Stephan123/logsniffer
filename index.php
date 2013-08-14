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
 * Create Table
 * CREATE TABLE `gitlog` (
 *  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 *  `hash` varchar(255) NOT NULL,
 *  `email` varchar(100) NOT NULL,
 *  `name` varchar(100) NOT NULL,
 *  `datum` varchar(100) NOT NULL,
 *  `view` varchar(100) NOT NULL,
 *  `beschreibung` text NOT NULL,
 *  PRIMARY KEY (`id`)
 * ) ENGINE=MyISAM DEFAULT CHARSET=utf8
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

    protected $host = 'localhost';
    protected $datenbank = 'test';
    protected $tabelle = 'gitlog';
    protected $user = 'test';
    protected $password = 'test';

    protected $connect = null;
    protected $command = null;
    protected $history = array();

    public function __construct()
    {
        date_default_timezone_set('Europe/London');
        $this->connect = new mysqli($this->host, $this->user, $this->password, $this->datenbank);
    }

    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    public function work()
    {
        if(empty($this->command)){
            echo 'Kommando fehlt!';

            exit();
        }

        $this->execute();

        return $this;
    }

    private function execute()
    {
        $output = array();
        chdir($this->dir);
        exec('"C:\Program Files (x86)\Git\bin\git.exe" ' .$this->command, $output);
        $this->output = $output; cfbcdgd
    }

    public function sichten()
    {
        $eintragen = array();

        foreach($this->output as $line){
            echo $line."<br>\n";
            // $this->eintragenDatenbank($i);
        }

        return $this;
    }

    public function eintragenDatenbank($i){

        echo '<hr>';

        return;
    }
}

$command = 'log --all --pretty=format:"%H # %ce # %cn # %ci # %s # %b"';

$gitLogbuch = new gitlog();
$gitLogbuch->setCommand($command)->work()->sichten();