<?php

class controller
{
    private $data;
    private $ts;


    public function __construct()
    {
        $this->data['lokal'] = Server::isLocalServer() ? 'LOKAL ' : '';
        $this->data['navi_local_remote'] = Server::isLocalServer() ? 'local_navi' : 'navi';
    }


    public function getSite()
    {
        if (Request::getInstance()->getGetRequests('logout'))
           Login::getInstance()->logout();

        if (Login::getInstance()->isLoggedIn())
        {
            $idt = Request::getInstance()->getGetRequests('idt');

            if (!$idt)
                $idt = 'Liste';
            $this->data['navi'] = $this->getNavi();
            $this->data['content'] = $this->getContent($idt);
            $this->data['logout'] = '<p class="navbar-text"><a class="navbar-link logout" href="index.php?logout=true" aria-label="Left Align"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> Logout</a></p>';
            $this->data['user'] = '';
        }
        else
        {

            $this->data['content'] = '';
            $this->data['user'] = '';
            $this->data['logout'] = '';
            $this->data['navi'] = '';
            if (Request::getInstance()->getPostRequests('anmelden') == 'anmelden')
                $this->data['content'] .= '<p>Diese Emailadresse ist entweder nicht registriert oder noch nicht aktiviert.</p>';
            $this->data['header'] = 'Anmelden';
            $this->data['content'] .= templateParser::getInstance()->parseTemplate(array(), 'Login/login_content_template.html');

        }


        return templateParser::getInstance()->parseTemplate($this->data, 'mainTemplate.html', false, false);
    }

    private function getContent($idt)
    {
        switch ($idt)
        {

            default:
                $class = new $idt();
              return $class->getContent();


        } //

    }

    private function getNavi()
    {
        $navi = array(
            'Liste&status=all' => 'alle',
            'Liste&status=1' => 'Vorschlag',
            'Liste&status=5' => 'neu',
            'Liste&status=2' => 'nächsteProbe',
            'Liste&status=6' => 'Midis machen',

            'Liste&status=repertoire' => 'Repertoire',
            'genres' => 'Genres',
            'erschienen' => 'erschienen',
            'upload' => 'Upload',
            'playlist' => 'Playlist');
        $this->data['header'] = isset($navi[Request::getInstance()->getGetRequests('idt')]) ? $navi[Request::getInstance()->getGetRequests('idt')] : '--';
        $retval = '<ul class="nav navbar-nav">';
        foreach ($navi AS $link => $linktext)
        {

            if ($link == Request::getInstance()->getGetRequests('idt'))
                $retval .= '<li class="active"><a href="index.php?idt=' . $link . '">' . $linktext . '</a></li>';
            else
                $retval .= '<li><a href="index.php?idt=' . $link . '">' . $linktext . '</a></li>';

        }
        $retval .= '</ul>';
        return $retval;

    }
}


