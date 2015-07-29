<?php
/*
 * Created on 13.11.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class ArticleFactory
{
	 protected $ID;

    public function __construct($ID) {
        $this->ID = $ID;
    }

    abstract protected function manufactureArticle();
}
?>
