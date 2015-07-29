<?php
require_once(PATH."lib/Mail/MailBoxConnector.php");

class Pop3Connector
{
    private $mailBoxConnector;
    
    public function __construct($mailbox, $username, $password)
    {
        $this->mailBoxConnector = new MailBoxConnector($mailbox, $username, $password, "pop3" );       
    }  
    
    public function getMails()
    {
        $this->mailBoxConnector->getMails();
    }
}
?>
