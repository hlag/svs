<?php
class CopyPermissionFromParent
{
    public function __construct($id)
    {
        $article = AGDO::getInstance()->GetAll("SELECT * FROM ".AGDO::getInstance()->getDBConnector()->getParentIDTable()." WHERE article_id = ".$id);
        $permissions = AGDO::getInstance()->GetAll("SELECT * FROM ".AGDO::getInstance()->getDBConnector()->getPrefix()."permission WHERE article_id = ".$article[0]['parent_id']);
        //AGDO::get
        foreach ($permissions as $permission)
        {
            $insertArray=array();
            $insertArray['role_id']=$permission['role_id'];
            $insertArray['article_id']=$id;
            $insertArray['permission']=$permission['permission'];
            AGDO::getInstance()->AutoExecute(AGDO::getInstance()->getDBConnector()->getPrefix()."permission", $insertArray, 'INSERT');
        }
    }

}
?>