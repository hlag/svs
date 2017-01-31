<?php

class TimestampConverter
{
    private static $instance;
    private $monatsArray;
    private $tagesArray;

    /**
     *
     * @returns TimestampConverter
     * @return TimestampConverter
     */

    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new TimestampConverter();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->monatsArray = array(1 => 'Januar', 2 => 'Februar', 3 => 'März', 4 => 'April', 5 => 'Mai', 6 => 'Juni', 7 => 'Juli', 8 => 'August', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Dezember');
        $this->tagesArray = array(0 => 'So', 1 => 'Mo', 2 => 'Di', 3 => 'Mi', 4 => 'Do', 5 => 'Fr', 6 => 'Sa', 7 => 'So');
    }

    public function convertSQLtoUnix($sql_date)
    {
        $temp = explode('-', $sql_date);
        return mktime(12, 0, 0, $temp[1], $temp[2], $temp[0]);
    }

    public function convertSQLtoLesbar($sql_date)
    {
        $temp = explode('-', $sql_date);
        if (count($temp) == 3)
            return trim($temp[2]) . '.' . trim($temp[1]) . '.' . trim($temp[0]);
        return '';
    }

    public function convertFullToShort($sql_date)
    {
        return (int)substr($sql_date, 8, 2) . '.' . (int)substr($sql_date, 5, 2) . '. - ' . substr($sql_date, 11, 5);
    }

    public function convertAbrechnungsmonatToLesbar($shortDateString)
    {
        return $this->monatsArray[substr($shortDateString, 4, 2) / 1] . ' ' . substr($shortDateString, 0, 4);
    }

    public function getFromSql($sql_date, $par)
    {
        $temp = $this->isValidSqlDate($sql_date);
        switch ($par)
        {
            case 'Y':
                return $temp[1];
                break;
            case 'm':
                return $temp[2];
                break;
            case 'd':
                return $temp[3];
                break;
            default:
                echo '<p>Timestampconverter function getFromSql : <strong>ungültiger Parameter ' . $par . '</strong></p>';


        }

    }

    public function isValidSqlDate($testString)
    {
        if (preg_match('/([1|2][0-9]{3})-([0|1][0-9])-([0|1|2|3][0-9])/', $testString, $match))
        {
            return $match;
        }
        else
        {
            echo '<p>Timestampconverter function isValidSqlDate : <strong>ungültiges Datumsformat</strong></p>';
            return false;
        }
    }

    public function getWeekday($sql_date)
    {
        $timestamp = $this->convertSQLtoUnix($sql_date);
        $dayArray = array('So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa');
        return $dayArray[date("w", $timestamp)];
    }

    public function convertLesbarToSQL($datum)
    {
        if (preg_match('/[0-9]*\.[0-9]*\.[0-9]/', $datum))
        {
            $temp = explode('.', $datum);
            if ($temp[2] < 1000)
                $temp[2] += 2000;
            return trim($temp[2]) . '-' . str_pad(trim($temp[1]),2, 0, STR_PAD_LEFT) . '-' . str_pad(trim($temp[0]),2, 0, STR_PAD_LEFT);
        }
        else
            return '0000-00-00';

    }

    public function ControlIfBirthday($event_datum, $geburtsdatum)
    {

        if (substr($geburtsdatum, 5, 5) == substr($event_datum, 5, 5))
            return true;
        else
            return false;
    }

    public function getBahnDate($sql)
    {
        return $this->getWeekday($sql) . ', ' . date("j.n.", $this->convertSQLtoUnix($sql));
    }

    public function generateAlter($event_datum, $geburtsdatum)
    {
        $tse = $this->convertSQLtoUnix($event_datum);
        $tsg = $this->convertSQLtoUnix($geburtsdatum);
        return date("Y", $tse - $tsg) - 1970;

    }

    public function getMonat($sql_date)
    {
        $temp = explode('-', $sql_date);
        return $this->monatsArray[(int)$temp[1]];
    }

    public function getKalenderwoche($sql_date)
    {
        $timestamp = $this->convertSQLtoUnix($sql_date);
        return date("W", $timestamp);
    }

    public function generateAbrechnungsmonatOffsetted($offset, $shortDatum = false)
    {
        if ($shortDatum)
            $unix = mktime(12, 0, 0, substr($shortDatum, 4, 2) + $offset, 1, substr($shortDatum, 0, 4));
        else
            $unix = mktime(12, 0, 0, date("m") + $offset, 1, date("Y"));
        return date("Ym", $unix);
    }


    public function generateAbrechnungMonat($datum)
    {
        $datum = explode('-', $datum);
        if ($datum[1] > 12)
        {
            $datum[1] = 1;
            $datum[0]++;
        }
        if ($datum[1] == 0)
        {
            $datum[1] = 12;
            $datum[0]--;
        }
        return $datum;
    }


    public function generateFirstDayOfNextMonth($jahr, $monat)
    {
        $ts = mktime(1, 1, 1, $monat + 1, 1, $jahr);
        return date("Y-m-d", $ts);
    }

    public function generateLastDayOfMonth($jahr, $monat)
    {
        $ts = mktime(1, 1, 1, $monat, 1, $jahr);

        return date("Y-m-t", $ts);
    }

    public function generateNextMonat($datum)
    {
        $jahr = substr($datum, 0, 4);
        $monat = substr($datum, 4, 2) + 1;

        if ($monat > 12)
        {
            $monat = 1;
            $jahr++;
        }

        return $jahr . str_pad($monat, 2, 0, STR_PAD_LEFT);
    }

    public function substractHours($anfang, $ende)
    {
        $anfangArr = explode(':', $anfang);
        $endeArr = explode(':', $ende);
        if (!isset($anfangArr[1]))
            $anfangArr[1] = 0;
        if (!isset($endeArr[1]))
            $endeArr[1] = 0;

        if ($anfangArr[0] > $endeArr[0])
            $endeArr[0] += 24;

        return round($endeArr[0] - $anfangArr[0] + ($endeArr[1] / 60) - ($anfangArr[1] / 60), 2);
    }

    public function secToHour($sec)
    {
        $stunden = (int)($sec / 3600);
        $sec = $sec - $stunden * 3600;
        $min = round($sec / 60);
        $min = $min < 0 ? -$min : $min;

        return $stunden . ':' . str_pad($min, 2, '0', STR_PAD_LEFT);
    }

    public function secToMinute($sec)
    {
        $stunden = (int)($sec / 3600);
        $restSec = $sec - $stunden * 3600;
        $min = (int)($restSec / 60);

        $restSec = $sec - ($stunden * 3600 + $min * 60);
        if ($stunden > 0)
            return $stunden . ':' . str_pad($min, 2, '0', STR_PAD_LEFT) . ':' . str_pad($restSec, 2, '0', STR_PAD_LEFT);
        else
            return $min . ':' . str_pad($restSec, 2, '0', STR_PAD_LEFT);

    }

    public function hMinSecToSec($zeit)
    {
        $h = $m = $s = 0;
        $arr = explode(':', $zeit);
        $count = count($arr);
        switch ($count)
        {
            case 1:
                $m = $arr[0];
                break;
            case 2:
                $m = $arr[0];
                $s = $arr[1];
                break;
            case 3:
                $h = $arr[0];
                $m = $arr[1];
                $s = $arr[2];
                break;
            default:
                return 'undefined';
                break;
        }
        return $h * 3600 + $m * 60 + $s;

    }

    public function getTimeFromDatetime($dateTime)
    {
        $temp = explode(" ", $dateTime);
        return isset($temp[1]) ? $temp[1] : false;
    }

    public function getDateFromDatetime($dateTime)
    {
        $temp = explode(" ", $dateTime);
        return $temp[0];
    }

    public function convertSQLtoLesbarMitTag($sqlDate)
    {
        $unix = $this->convertSQLtoUnix($sqlDate);
        return $this->tagesArray[date("w", $unix)] . ', ' . $this->convertSQLtoLesbar($sqlDate);
    }

    public function getDauer($anfang, $ende)
    {
        $dauer = $ende - $anfang;
        $h = (int)($dauer / 3600);
        $m = (int)(($dauer % 3600) / 60);
        return $h . ':' . $m;
    }

    public function getJahrFromSQL($sqlDate)
    {
        return substr($sqlDate, 0, 4);
    }

    public function convertDatumAnUhrzeitToUnix($pl_datum, $uhrzeitStart)
    {
        $da = explode('-',$pl_datum);
        $Y = $da[0];
        $m = $da[1];
        $d = $da[2];
        $H = 0;
        $i = 0;
        $s = 0;

        $uzArr = explode(':', $uhrzeitStart);

        $count = count($uzArr);
        switch ($count)
        {
            case 1:
                $H = $uzArr[0];
                break;
            case 2:
                $H = $uzArr[0];
                $i = $uzArr[1];
                break;
            default:
                return 'undefined';
                break;
        }
        return mktime($H, $i, $s, $m,$d,$Y);
    }

}
