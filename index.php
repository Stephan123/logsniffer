<?php


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
        exec('"C:\Program Files (x86)\Git\bin\git.exe" ' ."log", $output);
        $this->output = $output;
    }

    public function sichten()
    {
        foreach($this->output as $line){
            if(strpos($line, 'commit')===0){
                if(!empty($commit)){
                    array_push($this->history, $commit);
                    unset($commit);

                    $commit = array();
                    $commit['message'] = ' ';
                }
                $commit['hash']   = substr($line, strlen('commit'));
            }
            else if(strpos($line, 'Author')===0){
        	    $commit['author'] = substr($line, strlen('Author:'));
            }
            else if(strpos($line, 'Date')===0){
        	    $commit['date']   = substr($line, strlen('Date:'));
            }
            else{
                if(empty($line))
                    continue;

        	    $commit['message']  .= $line;
            }
        }

        return $this;
    }

    public function kontrolle()
    {
        $test = 123;
    }
}

$gitLogbuch = new gitlog();
$gitLogbuch->setCommand('log')->work()->sichten()->kontrolle();