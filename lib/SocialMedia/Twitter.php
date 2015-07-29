<?php
class Twitter
{
    private $dataArray;

    public function  __construct()
    {
        $this->init();
    }

    private function init()
    {
        $this->dataArray=AGDO::getInstance()->GetAll('SELECT * FROM social_media');
    }

    public function generateScript($article_url, $language, $article_id)
    {
        $article = AGDO::getInstance()->GetAll("SELECT * FROM ".AGDO::getInstance()->getDBConnector()->getDescriptionTable()." WHERE article_id = ".$article_id);
        $text="";
        if (!empty($article[0]['twitter_text']))
        {
            $text = 'data-text="'.$article[0]['twitter_text'].'"';
        }
	if ($article[0]['article_url']=="/")
		$article_url="";
	else
		$article_url =$article[0]['article_url'].".html"; 
	
        $url = $_SERVER['HTTP_HOST']."/".$article_url;
        return '<a href="http://twitter.com/share" class="twitter-share-button" data-lang="'.$language.'" data-url="http://'.$url.'" '.$text.' data-count="'.$this->dataArray[0]['twitter_ausrichtung']
                .'">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
    }
}
?>
