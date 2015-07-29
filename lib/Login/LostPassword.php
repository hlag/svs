<?php
require_once PATH.'lib/Mail/Mailer.php';
require_once PATH.'lib/Mail/Mailer.php';


class LostPassword
{
    protected static $instance = null;
    private $data = array();
    public function __construct()
    {
	$this->data['content'] = '';
	$this->data['logout'] = '';
    }
    public static function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new LostPassword();
        }
        return self::$instance;
    }
    public function managePassword()
    {
	if(Request::getInstance()->getPostRequests('ag_username'))
	{
	    if(false != ($id = $this->userExists(Request::getInstance()->getPostRequests('ag_username'))))
	    {
		$newPassword = $this->generatePasswort();
		$this->setResetPasswort($newPassword, $id);
		$hash = md5(time());
		$this->sendnewPassord($newPassword, $hash);
		AGDO::getInstance()->Execute("UPDATE user SET hash = '".$hash."' WHERE user_id = '".$id."'");
		$this->data['content'] = $this->parseTemplate(array(), 'lostPasswort_bestaetigung.html');

	    }
	    else
		$this->data['content'] = '<p>Diese Email-Adresse ist uns nicht bekannt. Bitte <a href="index.php">registrieren Sie sich</a>.</p>';
	}
	elseif(Request::getInstance()->getGetRequests('lostPassswort') != 1) // Bestätigungslink
	{
	    $userData = $this->getUserAndActivate();
	    $this->data['content'] = $this->parseTemplate($userData, 'lostPasswort_erfolg.html');
	    $this->data['content'] .= $this->parseTemplate(array(), 'login_content_template.html');
	}
	else
	{
	    $this->data['content'] = $this->parseTemplate(array(), 'lostPasswort_template.html');
	}
	return $this->data;
    }
    private function userExists($ag_username)
    {
	$sql = "SELECT * FROM user WHERE username = '".$ag_username."'";
	$res = AGDO::getInstance()->GetAll($sql);
	if( isset($res[0])  && $ag_username == $res[0]['username'])
	    return $res[0]['user_id'];
	else
	    return false;

    }
    private function getUserAndActivate()
    {
	$sql = "SELECT * FROM user WHERE hash = '".Request::getInstance()->getGetRequests('lostPassswort')."'";
	$user = AGDO::getInstance()->GetAll($sql);
	$this->activateNewPasswort($user[0]['newPassword'], $user[0]['user_id']);
	$user[0]['anredeFloskel'] = 'Sehr geehrte/r User';
	return $user[0];
    }




    private function setResetPasswort($newPassword, $id)
    {
	$sql = "UPDATE user SET newPassword = '".md5($newPassword)."' WHERE user_id = '".$id."'";
	AGDO::getInstance()->Execute($sql);
    }

    private function sendnewPassord($password, $hash)
    {
	$mail = new Mailer();
	$mail->setHost('wp381.webpack.hosteurope.de');
	$mail->setUsername('wp11164385-noreply');
	$mail->setPassword('Mg!ht+opJ');
	$mail->setAuth(true);
	$subject = 'Ihr neues Passwort für Ihre Bewerbung bei MolBiomed der Uni Bonn';
	$to = Request::getInstance()->getPostRequests('ag_username');
	$mailText = 'Sehr geehrter Bewerber,'."\n\n";
	$mailText .= 'Ihr neues Passwort lautet: '.$password."\n\n";
	$mailText .= 'bitte klicken Sie auf folgenden Link, um es zu aktivieren'."\n\n";

	if(LOCAL)
	    $mailText .= "http://www.limes.lan/index.php?lostPassswort=".$hash;
	else
	    $mailText .= "http://www.molbiomed-bewerbung.de/index.php?lostPassswort=".$hash;

	$mailText .= "\n\n____________________________\n\nProf. Dr. Thorsten Lang\nLIMES Institute\nMembrane Biochemistry\nCarl-Troll-Straße 31\n53115 Bonn \n";

	$from = 'noreply@molbiomed-bewerbung.de';
	$mail->sendMail($to, $subject, $mailText, $from);
    }
    private function activateNewPasswort($newPasswortMD5, $user_id)
    {
	$sql = "UPDATE user SET passwort = '".$newPasswortMD5."', newPassword = '', hash = '', userActive = 1 WHERE user_id = '".$user_id."'";
	AGDO::getInstance()->Execute($sql);
    }
    private function generatePasswort()
    {
	$possibleCharacters = array('a','b','c','d','e','f','g','h','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','1','2','3','4','5','6','7','8','9','0','!','%');
	$length = count($possibleCharacters) -1;
	$newPasswort = '';
	for($x=0;$x<8;$x++)
	{
	    $rand = rand(0,$length);
	    $rand2 = rand(0,1);

	    if($rand < 30)
	    {
		if($rand2 == 0)
		    $newChar = strtoupper ($possibleCharacters[$rand]);
		else
		    $newChar = $possibleCharacters[$rand];
	    }
	    else
		$newChar = $possibleCharacters[$rand];
	    $newPasswort .= $newChar;

	}
	return $newPasswort;

    }




    private function parseTemplate($data, $template = 'main_Template.html')
    {
	$fileContent = file_get_contents("Frontend/limes/".$template);
	foreach($data AS $key => $value)
	    $fileContent = str_replace('{'.$key.'}', $value, $fileContent);
	return $fileContent;
    }

}
?>
