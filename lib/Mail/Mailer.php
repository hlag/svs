<?php

require_once(PATH . "lib/Conf/Configuration.php");
require_once(PATH . "lib/Logging/Logger.php");
require_once(PATH . "lib/Server/Server.php");


/*
 * Created on 19.04.2007
 *
 * v1.3
 *
 * changed 01.10.2008 JA
 *   charset von außen setzbar.
 *
 * changed 08.10.2008 JA
 *   charset wird vom Content-Type header mitgeliefert
 *
*  changed 18.05.2011 MG
 *   Date in Mail eingef�gt
 * changed 25.10.2013
 *	Date in Mail eingefügt. War nicht drin
 *	Message-id eingefügt
 *	header "to" eingefügt
 *	Möglichkeit für mehrere Attachments eingefügt
 *
 */

require_once('Mail.php');
require_once("Mail/mime.php");

class Mailer
{
    private $host;
    private $port;
    private $username;
    private $password;
    private $from;
    private $auth;
    private $charset;
    private $attachement;
    static $mailer;

    public function __construct()
    {
        $this->stoppuhr = time() + microtime();

        $this->setCharset('ISO-8859-1');
        $conf = Configuration::getInstance();
        $this->host = $conf->get('Mail', 'Host');
        $this->username = $conf->get('Mail', 'User');
        $this->password = $conf->get('Mail', 'Password');
        $this->from = $conf->get('Mail', 'From');
        $this->auth = true; //$conf->get('Mail','SMTPAuth');
        $this->port = 25;
        $this->attachement = null;
        if (!isset(self::$mailer))
        {
            //Logger::getInstance()->Log('Mailer initialisiert',LOG_ABLAUF);
            self::$mailer = Mail::factory('smtp', array('host' => $this->host, 'port' => $this->port, 'auth' => $this->auth, 'username' => $this->username, 'password' => $this->password, 'persist' => true, 'pipelining' => true));
        }

    }


    function sendMail($to, $subject, $mailText, $from = "", $type = "", $prozess = 1)
    {
        Logger::getInstance()->Log($to, LOG_ABLAUF);

        $message = new Mail_mime("\n");
        // watt isn dat hier für ne funktion? die macht mit sicherheit alles, aber keinen html-Body
        $message->setHTMLBody($mailText);
        if ($this->attachement != null)
        {
            foreach ($this->attachement AS $attachment)
                $messageatt = $message->addAttachment($attachment);
            //Logger::getInstance()->Log($messageatt,LOG_ABLAUF);
            $this->attachement = null;
        }

        if (empty($from))
            $header['From'] = $this->from;
        else
            $header['From'] = $from;
        $header['Subject'] = $subject;
        $header['To'] = $to;
        $header['Date'] = date("D, d M Y H:i:s O");
        $header['Message-ID'] = '<' . time() . '.' . $prozess . '@' . $this->host . '>';


        $messBody = $message->get(array("text_encoding" => "quoted-printable"));
        if ($type == 'html')
            $messBody = '<html><body>' . $messBody . '</body></html>';

        $header2 = $message->headers($header);

        $error_obj = self::$mailer->send($to, $header2, $messBody);


        if (is_object($error_obj))
        {
            //Logger::getInstance()->Log($message, LOG_ABLAUF);
            $errorString = ob_get_contents();
            file_put_contents(PATH . 'mail.error.log', $errorString);
            return false;
        }
        else
        {


            //z('email was send successfully!');
            return true;
        }
    }

    public function setHost($host)
    {
        $this->host = $host;
    }

    public function setPort($port = 25)
    {
        $this->port = $port;
    }

    public function setAuth($auth)
    {
        $this->auth = $auth;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setCharset($charset) // JA 01.10.2008
    {
        $this->charset = $charset;
    }

    public function addAttachement($attachement)
    {
        $this->attachement[] = $attachement;
        //Logger::getInstance()->Log("atta: ".$this->attachement, LOG_ABLAUF);
    }


}

?>
