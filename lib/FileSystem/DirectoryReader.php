<?php
/*
 * Created on 26.04.2005
 *
 * by Matthias G�nther
 *
 * Version: 2.2
 *
 *	Changed 09.04.2007 MG
 *	Mitrendern der "._" -Dateien
 *
 *
 */

class DirectoryReader
{
	private $invalidExtensions;
	private $invalidFilenames;

	function __construct()
	{

	}

	public function getDirectoryListing($Dir, $dirs=false)
	{
		$returnvalue=array();
                if (!is_dir($Dir))
                    return $returnvalue;
		//$handle=opendir($Dir);
		if ($handle = opendir($Dir))
		{
			while (false !== ($file = readdir($handle)))
   			{
                            if (is_dir($Dir.$file) && !$dirs)
                            {
                            }
                            else
                            {
                                if ($file!="." && $file!=".." && substr($file,0, 1)!='_' && substr($file,0, 1)!='.' && $this->getDirs($Dir.$file, $dirs) && $this->checkFileNames($file) && $this->checkFileExtensions($file)
   					&&  substr($file,0,2)!="._" )   //09.04.2007 MG
   				{
                                    $returnvalue[]=$file;
   				}
                            }
   			}
		}

   		closedir($handle);
   		sort($returnvalue, SORT_STRING);
   		return $returnvalue;
	}

        private function getDirs($file, $dirs)
        {
            if ($dirs)
            {
                if (is_dir($file))
                    return true;
            }
            else
            {
                if (is_dir($file) && substr($file, 0, 1)!='.')
                    return false;
                else
                    return true;
            }
        }

	private function checkFileNames($filename)
	{
            if (!empty($this->invalidFilenames))
            {
                    foreach($this->invalidFilenames as $fileNames)
                    {
                        if ($fileNames==$filename)
                            {
                                    return false;
                            }
                    }
            }
            return true;
	}

	private function checkFileExtensions($filename)
	{
		if (!empty($this->invalidExtensions))
		{
			foreach($this->invalidExtensions as $extension)
			{
				if ($extension==$filename)
					return false;
			}
		}
		return true;
	}

	public function setInvalidExtensions($invalidExtensions)
	{
		$this->invalidExtensions=$invalidExtensions;
	}

	public function setInvalidFilenames($invalidFilenames)
	{
		$this->invalidFilenames=$invalidFilenames;
	}
}
?>