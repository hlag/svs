<?php
class MailBoxConnector
{
    private $MailBoxHandle;
    
    public function __construct($mailbox, $username, $password, $service)
    {
        $mailboxString="{".$mailbox.":110".(!empty($service)?"/".$service:"")."/tls/novalidate-cert}INBOX";
        //echo $mailboxString;
        $this->MailBoxHandle = imap_open($mailboxString,$username,$password);
        //echo $message_count = imap_num_msg($this->MailBoxHandle);
       
    } 
    
    public function getMails()
    {
        /*echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head><body>';*/
        $message_count = imap_num_msg($this->MailBoxHandle);
        for ($counter = 1; $counter <= $message_count; $counter++)
        {
            // echo "<h1>Headers in INBOX</h1>\n";
             $headers = imap_headerinfo($this->MailBoxHandle,$counter);
             $structure = imap_fetchstructure($this->MailBoxHandle,$counter);
             $ttype = $structure->type;
             $tcode = $structure->encoding;
             $body =  imap_body($this->MailBoxHandle,$counter,1);
             
             if ( $tcode == 3 ) 
             { 
                 $body = base64_decode($body);
                 $value= $body;
                 if (isset($structure->parameters[0]->attribute) && $structure->parameters[0]->attribute=='CHARSET');
                 {
                     if ($structure->parameters[0]->value=='iso-8859-1')
                     {
                         $body = str_replace("\r\n", "<br />",iconv("ISO-8859-1", "UTF-8",$body)); 
                     }
                 }
             }
             if ( $tcode == 4 ) 
             { 
                 $body = quoted_printable_decode($body); 
                 $body = str_replace("\r\n", "<br />",iconv("ISO-8859-1", "UTF-8",$body));
             }
            
           // echo "<br />".$body;
        
        }  
        imap_close($this->MailBoxHandle);
    }
}
?>