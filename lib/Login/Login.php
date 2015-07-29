<?php

class Login
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
        $this->userTable = 'musiker';
        $this->tryLogin();
    }

    /**
     *
     * @return Login
     */
    public static function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new Login();
        }
        return self::$instance;
    }

    private function tryLogin()
    {
        $request = Request::getInstance()->getRequests();
        $this->isLoggedIn=false;
        if(isset($request['Logout']) && $request['Logout']=='Logout')
        {
            $this->logout();
        }
        else
        {
            if (isset($request['username']) && (isset($request['passwort']) || isset($request['passwort_crypt'])))
            {
                $UserQuery = "SELECT * FROM ".$this->userTable." WHERE username = '".trim($request['username'])."' AND userActive = 1";
                $result = AGDO::getInstance()->GetAll($UserQuery);
                if (!empty($result))
                {
                    if (isset($request['passwort_crypt']))
                    {
                        if ($request['passwort_crypt'] == $result[0]['passwort'])
                        {
                            $this->isLoggedIn=true;
                        }
                    }
                    if (isset($request['passwort']))
                    {

			if (md5($request['passwort']) == $result[0]['passwort'])
                        {
                           $this->isLoggedIn=true;
                           Request::getInstance()->addVariable('passwort_crypt',$result[0]['passwort'],'SESSION');
                           Request::getInstance()->addVariable('username',$result[0]['username'],'SESSION');
                        }

                    }
                    if ($this->isLoggedIn())
                    {
                        $this->name = $result[0]['musiker_nachname'];
                        $this->vorname = $result[0]['musiker_vorname'];
                        $this->userID = $result[0]['musiker_id'];
                        //echo "loggedIn";
                    }
                }
            }
        }
    }

    public function reset()
    {
        $this->tryLogin();
    }

    public function needLogin()
    {
        return true;
    }

    public function getUser()
    {
        $user = AGDO::getInstance()->GetAll("SELECT * FROM ".$this->userTable." WHERE betreuer_id = ".$this->getUserID());
        return $user[0];
    }

    public function getUserID()
    {
        return $this->userID;
    }

    public function isLoggedIn()
    {
        return $this->isLoggedIn;
    }

    public function logout()
    {
        $this->isLoggedIn=false;
	unset($_SESSION);
        session_destroy();
    }
}
?>
