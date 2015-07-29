<?php

class Musiker
{
    private $id;
    private  static $namen = array('r' => 'Ruth', 'b' => 'Bela', 'k' => 'Klaus', 'p' => 'Playback', 't' => 'Tyros');
    private static $statiIcons = array(0 => 'fa-minus', 1=>'fa-minus', 2 => '',3=>'', 4=>'fa-thumbs-down',  5 => 'fa-thumbs-up', 10=>10);
    private static $statiClasses = array(0 => 'text-muted', 1=>'text-muted', 2 => 'fa-thumbs-down',3=>4, 4=>'text-danger',  5 => 'text-success', 10=>10);
    private $name;
    private $status;
    private $class;
    private $icon;
    private $arrangement;

    public function __construct($kuerzel)
    {
        $this->id = $kuerzel;
        $this->name = self::$namen[$kuerzel];
    }

    public function setMusiker($data)
    {
        $this->status = $data[$this->id];
        $this->arrangement = str_replace("\n",'<br>',$data['arr_' . $this->id]);
        $this->icon =  self::$statiIcons[$this->status];
        $this->class =  self::$statiClasses[$this->status];
    }
    public function getArrangement()
    {
        return $this->arrangement ;
    }
    public function getStatusIcon()
    {
        return $this->icon ;
    }
    public function getStatus()
    {
        return $this->status ;
    }
    public function getStatusClass()
    {
        return $this->class ;
    }
    public function isAllowed()
    {
        if($this->status == 4)
            return false;
        else
            return true;
    }
}