<?php
/*
 * Created on 08.11.2005
 *
 * Author: Mats
 *
 *	Version 1.2
 *
 * File: DBConnector.php
 *
 *
 *  changed 4.4.07 KB
 *  function getDBMS zugefügt, um Unabh�ngigkeit von der Datenbank zu erzeugen
 *  Klassenvariable $DBMS eingefügt
 *
 *	Changed 10.04.2006 MG
 *	DBConnector berücksichtigt DBPrefix in .htconfig
 */

/**
 * Wrapperclass for the DatabaseConnectionData Host Password etc
 *
 * The Idea is to create different Profiles (Testserver, Customer)
 *
 *
 */
class DBConnector
{
    private $host;
    private $user;
    private $passwd;
    private $dbname;
    private $articleTable;
    private $descriptionTable;
    private $parentIDTable;
    private $archiveTable;
    private $locktable;
    private $DBMS;
    private $prefix;

    public function __construct()
    {
        /*
        $this->host='db523.1und1.de';
        $this->user='dbo155195156';
        $this->passwd='NHEhYC9g';
        $this->dbname='db155195156';
        */
        $conf = Configuration::getInstance();
        if (Server::isLocalServer()) {
            $this->host = $conf->get('Database', 'LocalHost');
            $this->user = $conf->get('Database', 'LocalUser');
            $this->passwd = $conf->get('Database', 'LocalPasswd');
            $this->dbname = $conf->get('Database', 'LocalDB');
            $this->DBMS = $conf->get('Database', 'LocalDBMS');
            $this->locktable = $conf->get('Database', 'LocalLockTable');
        } else {
            $this->host = $conf->get('Database', 'RemoteHost');
            $this->user = $conf->get('Database', 'RemoteUser');
            $this->passwd = $conf->get('Database', 'RemotePasswd');
            $this->dbname = $conf->get('Database', 'RemoteDB');
            $this->DBMS = $conf->get('Database', 'RemoteDBMS');
            $this->locktable = $conf->get('Database', 'RemoteLockTable');
        }

        //$this->dbname='avaris_19_9_06';

        //Data for the Article-Tables
        $config = Configuration::getInstance(); //10.04.2007 MG
        $this->prefix = $config->get('Database', 'DBPrefix'); //10.04.2007 MG
        $this->articleTable = $this->prefix . 'articles'; //10.04.2007 MG
        $this->descriptionTable = $this->prefix . 'article_descriptions'; //10.04.2007 MG
        $this->parentIDTable = $this->prefix . 'article_to_parent_ids'; //10.04.2007 MG
        $this->archiveTable = $this->prefix . 'article_archive';

    }

    public function getDBMS()
    {
        return $this->DBMS;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function getParentIDTable()
    {
        return $this->parentIDTable;
    }

    public function getArchiveTable()
    {
        return $this->archiveTable;
    }

    public function getLockTable()
    {
        return $this->prefix . $this->locktable;
    }

    public function getDescriptionTable()
    {
        return $this->descriptionTable;
    }

    public function getArticleTable()
    {
        return $this->articleTable;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getPasswd()
    {
        return $this->passwd;
    }

    public function getDBName()
    {
        return $this->dbname;
    }

}

?>
