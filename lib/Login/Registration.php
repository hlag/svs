<?php

require_once PATH.'lib/Mail/Mailer.php';

class Registration
{
    protected static $instance = null;
    private $isLoggedIn = false;
    private $name = null;
    private $vorname = null;
    private $userID = null;
    private $objekte = null;
    private $groups = null;
    private $adressenID = null;
    private $globale_adresse = null;
    private $userTable = null;

    private function __construct()
    {
        if (!session_id())
            session_start();
        $this->userTable = AGDO::getInstance()->getDBConnector()->getPrefix()."user";
	Request::getInstance()->getPostRequests();
	$this->dataArray = array();
	$this->dataArray['ag_username_error'] = '';
	$this->dataArray['ag_passwort_error'] = '';
	$this->dataArray['errormessage'] = '';
    }

    public static function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new Registration();
        }
        return self::$instance;
    }

    public function manageRegistration()
    {

	$this->dataArray['ag_username'] = Request::getInstance()->getPostRequests('ag_username');
	$data['logout'] = '';
	if(Request::getInstance()->getPostRequests('registrieren') == 'registrieren') // Schritt eins
	{

	    $isNewUser = !$this->usernameExists();
	    $passwortIsCorrect = $this->passwordsAreCorrect();
	    if($isNewUser && $passwortIsCorrect)
	    {


		$hash = md5(time());
		$this->saveNewRegistration($hash);
		$this->sendConfirmationMail($hash);
		$data['content'] = $this->parseTemplate($this->dataArray, 'registration_success_template.html');


	    }
	    else
	    {
		$data['content'] = $this->parseTemplate($this->dataArray, 'registration_template.html');
	    }
	}
	elseif(false != Request::getInstance()->getGetRequests('registrieren'))			// schritt zwei, Bestätigung
	{

	    $this->activateUser();
	    $data['content'] = '<p>Sie haben Ihre Registrierung erfolgreich bestätigt und können sich nun anmelden.';
	}
	else
	{
	    $data['content'] = $this->parseTemplate($this->dataArray, 'registration_template.html');

	}
	return $data;
    }

    private function usernameExists()
    {
	if(true == Request::getInstance()->getPostRequests('ag_username'))
	{



	    Request::getInstance()->addVariable('ag_username', trim(Request::getInstance()->getPostRequests('ag_username')), 'POST');

	    $sql = "SELECT * FROM ".$this->userTable." WHERE username = '".Request::getInstance()->getPostRequests('ag_username')."'";
	    $testIfExists = AGDO::getInstance()->GetAll($sql);
	    if(isset($testIfExists[0]) && Request::getInstance()->getPostRequests('ag_username') == $testIfExists[0]['username'])
	    {
		$this->dataArray['errormessage'] .= '<p class="errorMsg">Der Username existiert schon. Haben Sie Ihr <a href="index.php?lostPassswort=1">Passwort vergessen</a>?</p>';
		return true;
	    }
	    else
	    {

		return false;
	    }
	}
	else
	{
	    $this->dataArray['errormessage'] .= '<p class="errorMsg">Die Email-Adresse darf nicht leer sein.</p>';
	    return true;
	}


    }
    private function passwordsAreCorrect()
    {

	$isCorrect = true;

	if(Request::getInstance()->getPostRequests('ag_passwort') != Request::getInstance()->getPostRequests('ag_passwort_2'))
	{
	    $this->dataArray['errormessage'] .= '<p class="errorMsg">Die Passwörter stimmen nicht überein</p>';
	    $isCorrect = false;
	}
	if(strlen(Request::getInstance()->getPostRequests('ag_passwort')) < 4)
	{
	    $this->dataArray['errormessage'] .= '<p class="errorMsg">Das Passwort muss aus mindestens 8 Buchstaben bestehen.</p>';
	    $isCorrect = false;
	}
	return $isCorrect;

    }
    private function saveNewRegistration($hash)
    {
	$sql = "INSERT INTO ".$this->userTable." SET "
	    ." username = '".Request::getInstance()->getPostRequests('ag_username')."', "
	    ." passwort = '".md5(Request::getInstance()->getPostRequests('ag_passwort'))."', "
	    ." hash = '".$hash."', "
	    ." registrierungsdatum = '".date("Y-m-d")."', "
	    ." ip = '".$_SERVER['REMOTE_ADDR']."' ";
	AGDO::getInstance()->Execute($sql);
    }
    private function sendConfirmationMail($hash)
    {
	$mail = new Mailer();
	$mail->setHost('wp381.webpack.hosteurope.de');
	$mail->setUsername('wp11164385-noreply');
	$mail->setPassword('Mg!ht+opJ');
	$mail->setAuth(true);
	$subject = 'Der Bestätigungslink zu Ihrer Registrierung bei MolBiomed der Uni Bonn';
	$to = Request::getInstance()->getPostRequests('ag_username');
	$mailText = 'Herzlichen Dank für Ihr Interesse an unserem Studienprogramm und Ihre Registrierung! Zur Aktivierung Ihrer Anmeldung klicken Sie bitte auf folgenden Link:'."\n\n";

	if(LOCAL)
	    $mailText .= "http://www.limes.lan/index.php?registrieren=".$hash;
	else
	    $mailText .= "http://www.molbiomed-bewerbung.de/index.php?registrieren=".$hash;

	$mailText .= "\n\n";
	$mailText .="Datenschutzerklärung\n\n";
	$mailText .="Die Rheinische Friedrich-Wilhelms-Universität Bonn legt großen Wert auf den Schutz Ihrer personenbezogenen Daten. Die Verarbeitung dieser Daten erfolgt durch das Koordinationsbüro der Universität Bonn im Rahmen der gesetzlichen Bestimmungen des Landesdatenschutzgesetzes NRW. Die im Online-Formular abgefragten personenbezogenen Daten werden ausschließlich zum Zweck der Abwicklung des Bewerbungsverfahrens erhoben, gespeichert und genutzt. Eine Übermittlung der Daten an andere Stellen innerhalb der Universität erfolgt im Rahmen der gesetzlichen Bestimmungen ebenfalls nur, soweit dies zur Abwicklung des Bewerbungsverfahrens erforderlich ist.\n\n";
	$mailText .="Mit freundlichen Grüßen, \n\n";
	$mailText .="Ihr Team \nder Molekularen Biomedizin\nder Rheinischen Friedrich-Wilhelms Universität Bonn\n\n";

	$mailText .= "____________________________\n\nProf. Dr. Thorsten Lang\nLIMES Institute\nMembrane Biochemistry\nCarl-Troll-Straße 31\n53115 Bonn \n";



	$from = 'noreply@molbiomed-bewerbung.de';
	$mail->sendMail($to, $subject, utf8_decode($mailText), $from);

    }
    private function activateUser()
    {

	$sql = "SELECT * FROM user WHERE hash = '".Request::getInstance()->getGetRequests('registrieren')."' ";
	$user = AGDO::getInstance()->getAll($sql);

	$sql = "UPDATE ".$this->userTable." SET userActive = 1 WHERE hash = '".Request::getInstance()->getGetRequests('registrieren')."' ";
	AGDO::getInstance()->Execute($sql);




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
