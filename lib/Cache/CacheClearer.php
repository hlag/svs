<?php
class CacheClearer
{
    public function __construct()
    {
        $this->clearCache();
    }
    
    public function clearCache()
    {
        $eintrag=array();
        if ($handle = opendir('../cache'))
        { 
            while (false !== ($file = readdir($handle)))
            {
                if ($file != "." && $file != "..")
                {
                        if(is_dir($file))
                            $eintrag['dir'][]= $file;
                           else
                            $eintrag['file'][]= $file;
                }
            }
            if(isset($eintrag['file']) && count($eintrag['file']) != 0)
            {
                      foreach($eintrag['file'] AS $file)
                        unlink('../cache/'.$file);
            }
            closedir($handle);
          }
    }
}
?>
