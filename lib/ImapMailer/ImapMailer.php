<?php
require_once(PATH . "lib/Conf/Configuration.php");
require_once(PATH . "lib/Logging/Logger.php");
require_once(PATH . "lib/Server/Server.php");


/*
 * Created on 25.6.2013
 *
 * File: ImapMailer.php
 *
 * Author: hlag
 */
class ImapMailer
{
    private static $instance;
    private static $inbox;
    private static $user;
    private static $passwd;
    private static $resource;


    /**
     *
     * @returns ImapMailer
     * @return ImapMailer
     */
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new ImapMailer();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $conf = Configuration::getInstance();
        $this->inbox = $conf->get('Mail', 'Inbox');
        $this->user = $conf->get('Mail', 'User');
        $this->passwd = $conf->get('Mail', 'Password');
    }

    public function getMails()
    {
        $this->open();

        $MC = imap_check($this->resource);
        $result = imap_fetch_overview($this->resource, "1:{$MC->Nmsgs}", 0);

        $mail = array();
        for ($x = 0; $x < $MC->Nmsgs; $x++)
        {
            $mail[$x] = imap_header($this->resource, $x + 1);
            $mail[$x]->body = utf8_encode(imap_body($this->resource, $x + 1));
        }
        $this->close();
        return $mail;

    }

    public function open()
    {
        $this->resource = imap_open($this->inbox, $this->user, $this->passwd);
    }

    public function close()
    {
        imap_close($this->resource);
    }

    public function markOneMailToDelete($mailID)
    {
        imap_delete($this->resource, $mailID);

    }
}

?>
