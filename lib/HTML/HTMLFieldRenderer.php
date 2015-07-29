<?php
/*
 * Created on 04.09.2006
 *
 * Author: Mats
 *
 * File: HTMLFieldRenderer.php
 *
 */
class HTMLFieldRenderer
{
	public function __construct()
	{
	}

	public function renderOptionFields($dataArray, $selected)
	{
		$returnvalue=NULL;
		foreach ($dataArray as $line)
		{
			$returnvalue.="<option ";
			if ((iconv("ISO-8859-1","UTF-8",html_entity_decode($line)))==($selected))
				$returnvalue.='selected selected="selected"';
			$returnvalue.='>'.$line."</option>";

		}
		return $returnvalue;
	}

	public function renderRadio($selectedID, $maxcount, $valueName)
	{
		$returnvalue=array();
		for ($counter=1; $counter<=$maxcount;$counter++)
		{
			if ($selectedID==$counter)
				$returnvalue[$valueName."_".$counter]='checked checked="checked"';
			else
				$returnvalue[$valueName."_".$counter]='';
		}
		return $returnvalue;
	}

	public function renderCheckBox($checked)
	{
		if ($checked)
			return 'checked checked="checked"';
		else
			return '';
	}

	public function renderOptionFieldsWithID($dataArray, $selectedID)
	{
		$returnvalue=NULL;
		if (empty($dataArray))
			return "";
		foreach ($dataArray as $line)
		{
			$returnvalue.='<option value="'.$line['ID'].'" ';
			if ($line['ID']==($selectedID))
				$returnvalue.='selected selected="selected"';
			$returnvalue.='>'.$line['data']."</option>";

		}
		return $returnvalue;
	}

        public function renderOptionFieldsWithOptGroup($dataArray, $selected)
        {
		$returnvalue=NULL;
		//print_r($dataArray);
                $opt=0;
		foreach ($dataArray as $line)
		{
                    if (isset($line['group']) && $line['group'])
                    {
                        if($opt>0)
                            $returnvalue.='</optgroup>';
                        $returnvalue.='<optgroup label="'.$line['data'].'">';
                        $opt++;
                    }
                    else
                    {
                        $returnvalue.="<option ";
			if ((iconv("ISO-8859-1","UTF-8",html_entity_decode($line['data'])))==($selected))
				$returnvalue.='selected selected="selected"';
			$returnvalue.='>'.$line['data']."</option>";
                    }
		}
		return $returnvalue;
        }

	public function renderOptionFieldsArray($dataArray, $selected)
	{
		$returnvalue=NULL;
		foreach ($dataArray as $line)
		{
			$returnvalue.="<option ";
			if (isset($line['onClick']))
				$returnvalue.= 'onCLick="'.$line['onClick'].'"';
			if (isset($line['onChange']))
				$returnvalue.= 'onChange="'.$line['onChange'].'"';


			if ((iconv("ISO-8859-1","UTF-8",html_entity_decode($line['name'])))==($selected))
				$returnvalue.=' selected selected="selected"';
			$returnvalue.='>'.$line['name']."</option>";

		}
		return $returnvalue;
	}


	public function renderPager($AktuellePosition, $countForEachPage, $max, $URL, $parameter)
	{
		$returnvalue='<div id="pager">';
		$AnzahlSeiten = (int)ceil($max/$countForEachPage);
                if ($countForEachPage > 1)
                {
                    for ($counter=1; $counter<=$AnzahlSeiten; $counter++)
                    {
                            if ($counter==$AktuellePosition)
                                    $returnvalue .="<span>".$counter.'</span>';
                            else
                                    $returnvalue .= '<a href="'.$URL.'?page='.$counter.'">'.$counter.'</a>';
                    }
                }
		$returnvalue.="</div>";
		return $returnvalue;
	}
}
?>
