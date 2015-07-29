<?php
/**
 * Letzte Änderung von: $LastChangedBy: proggen $
 * Revision           : $LastChangedRevision: 153 $
 * Letzte Änderung am : $LastChangedDate: 2009-04-22 17:50:42 +0200 (Mi, 22 Apr 2009) $
 *
 */
class MYSQLDateConverter
{
	public static function convertMySQLDateToUnixTimeStamp($date)
    {
         if (empty($date) || is_array($date))
            return "";
        $dateArray = explode('-',$date);
        if (count($dateArray)!=3)
            return null;
        $newdate=mktime(0,0,0,$dateArray[1], $dateArray[2],$dateArray[0]);
        return $newdate;
    }
    
    public static function convertMysqlDate($date, $language=1)
	{
        if (empty($date) || is_array($date))
            return "";
        $dateArray = explode('-',$date);
		$newdate="";
		switch ($language)
		{
			case 1:
				$newdate = $dateArray[2].".".$dateArray[1].".".$dateArray[0];
		}
		return $newdate;
	}
    
    public static function convertDate($date,$language=1)
    {
        if (empty($date))
            return "";
        switch ($language)
        {
            case 1:
                $dateArray = explode('.',$date);
                $newdate = $dateArray[2]."-".$dateArray[1]."-".$dateArray[0];
                break;
        }
        return $newdate;
    }
}
?>