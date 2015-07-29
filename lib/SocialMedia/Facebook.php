<?php
class Facebook
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

    public function update($dataArray)
    {
        /*if (isset($dataArray['facebook_article_url']))
            $dataArray['facebook_article_url']=1;
        else
            $dataArray['facebook_article_url']=0;*/
		if (isset($dataArray['show_send']))
            $dataArray['show_send']=1;
        else
            $dataArray['show_send']=0;
        if (isset($dataArray['show_faces']))
            $dataArray['show_faces']=1;
        else
            $dataArray['show_faces']=0;
        AGDO::getInstance()->AutoExecute('social_media', $dataArray, 'UPDATE','social_media_id = 1' );
        $this->init();
    }

    public function generateScript($articleurl)
    {
        $returnvalue= '<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/de_DE/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));</script>';
		$returnvalue.='<div  class="fb-like"  ';
        if ($articleurl!='/')
            $returnvalue.='data-href="http://'.$_SERVER['HTTP_HOST'].'/'.$articleurl.'.html" ';
        else
            $returnvalue.='data-href="'.$_SERVER['HTTP_HOST'].'" ';
        $returnvalue.='data-layout="'.$this->dataArray[0]['facebook_layout_style'].'" ';
        $returnvalue.='data-show-faces="'.($this->dataArray[0]['show_faces']?'true':'false').'" ';
        $returnvalue.='data-width="'.$this->dataArray[0]['width'].'" ';
        $returnvalue.='data-send="'.($this->dataArray[0]['show_send']?'true':'false').'" ';
        $returnvalue.='data-font="'.$this->dataArray[0]['font'].'" ';
        $returnvalue.='data-colorscheme="'.$this->dataArray[0]['color_scheme'].'" ';
        $returnvalue.="></div>";
        return $returnvalue;
    }

    public function generateMetaData($article_id)
    {

        $article= AGDO::getInstance()->GetAll("SELECT * FROM ".AGDO::getInstance()->getDBConnector()->getDescriptionTable()." WHERE article_id = ".$article_id);
        $suchmuster="/\< *[img][^\>]*[src] *= *[\"\']{0,1}([^\"\'\ >]*)/i";
        //print_r()
        //preg_match_all($pattern, $subject, $matchesarray)
        if (empty($article[0]['facebook_title']))
            $article[0]['facebook_title']=$article[0]['article_title'];
        $returnvalue='<meta property="og:title" content="'.$article[0]['facebook_title'].'" />'."\r\n";
        $returnvalue.='<meta property="og:type" content="'.$article[0]['facebook_type'].'" />'."\r\n";
        if ($article[0]['article_url']=='/')
            $returnvalue.='<meta property="og:url" content="http://'.$_SERVER['HTTP_HOST'].'" />'."\r\n";
        else
            $returnvalue.='<meta property="og:url" content="http://'.$_SERVER['HTTP_HOST']."/".$article[0]['article_url'].'.html" />'."\r\n";
        preg_match($suchmuster, $article[0]['article_content'], $treffer);
        //print_r($treffer);
        $image = "";
        if (!empty($article[0]['facebook_image']))
        {
            $image=$article[0]['facebook_image'];
        }
        else
        {
            if (!empty($treffer))
            {
                
                $image = str_replace('&quot;', '', $treffer[1]);
            }
            else
            {

                $image = $this->getdefaultImage();
            }
        }
        $returnvalue.='<meta property="og:image" content="http://'.$_SERVER['HTTP_HOST'].$image.'" />'."\r\n";
        $returnvalue.='<meta property="og:site_name" content="'.$this->dataArray[0]['facebook_site_name'].'" />'."\r\n";
        $returnvalue.='<meta property="fb:admins" content="'.$this->dataArray[0]['facebook_admin_code'].'" />'."\r\n";
        if (!empty($article[0]['facebook_description']))
            $returnvalue.='<meta property="og:description" content="'.$article[0]['facebook_description'].'" />'."\r\n";
        return $returnvalue;
    }

    public function getdefaultImage()
    {
        return "/files/images/facebook/".$this->dataArray[0]['facebook_default_image'];
    }

    public function getOptions($selected)
    {
        if (empty($selected))
            $selected="article";
       
        $optionArray=array();
        $optionArray = array_merge($this->getOptionActivities(), $this->getOptionBusinesses());
        $optionArray = array_merge($optionArray, $this->getOptionGroups());
        $optionArray = array_merge($optionArray, $this->getOptionOrganizations());
        $optionArray = array_merge($optionArray, $this->getOptionPeople());
        $optionArray = array_merge($optionArray, $this->getOptionPlaces());
        $optionArray = array_merge($optionArray, $this->getOptionProductsAndEntertainment());
        $optionArray = array_merge($optionArray, $this->getOptionWebsites());
        $htmlRenderer = new HTMLFieldRenderer();
        return $htmlRenderer->renderOptionFieldsWithOptGroup($optionArray, $selected);
    }

    private function getOptionBusinesses()
    {
        $optionValues['data']='Businesses';
        $optionValues['group']=true;
        $optionArray[]=$optionValues;
        $optionValues['data']='bar';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='company';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='cafe';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='hotel';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='restaurant';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        return $optionArray;
    }

    private function getOptionOrganizations()
    {
        $optionValues['data']='Organizations';
        $optionValues['group']=true;
        $optionArray[]=$optionValues;
        $optionValues['data']='band';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='government';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='non_profit';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='school';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='university';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        return $optionArray;
    }

    private function getOptionPeople()
    {
        $optionValues['data']='People';
        $optionValues['group']=true;
        $optionArray[]=$optionValues;
        $optionValues['data']='actor';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='athlete';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='author';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='director';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='musician';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='politician';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='public_figure';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        return $optionArray;
    }

    private function getOptionProductsAndEntertainment()
    {
        $optionValues['data']='Products and Entertainment';
        $optionValues['group']=true;
        $optionArray[]=$optionValues;
        $optionValues['data']='album';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='book';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='drink';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='food';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='game';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='product';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='song';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='movie';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='tv_show';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        return $optionArray;
    }

    private function getOptionPlaces()
    {
        $optionValues['data']='Places';
        $optionValues['group']=true;
        $optionArray[]=$optionValues;
        $optionValues['data']='city';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='country';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='landmark';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='state_province';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        return $optionArray;
    }

    private function getOptionWebsites()
    {
        $optionValues['data']='Websites';
        $optionValues['group']=true;
        $optionArray[]=$optionValues;
        $optionValues['data']='blog';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='website';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='article';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        return $optionArray;
    }
    
    private function getOptionGroups()
    {
        $optionValues['data']='Groups';
        $optionValues['group']=true;
        $optionArray[]=$optionValues;
        $optionValues['data']='cause';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='sports_league';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='sports_team';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionArray[]=$optionValues;
        return $optionArray;
    }

    private function getOptionActivities()
    {
        $optionArray=array();
        $optionValues['data']='Activities';
        $optionValues['group']=true;
        $optionArray[]=$optionValues;
        $optionValues['data']='activity';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        $optionValues['data']='sport';
        $optionValues['group']=false;
        $optionArray[]=$optionValues;
        return $optionArray;
    }
}
?>
