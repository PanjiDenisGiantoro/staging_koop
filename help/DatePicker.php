<?php

//Class Kalendar
class Kalendar {
    var $Year; 
    var $defaultYear;
    var $LeapYear;
    var $Month;
    var $DefaultMonth;
    var $DayCount;
    var $FirstDayOfMonth;
    var $LongMonth;
    var $FormatDate;
    
        function Kalendar(){
            $defaultYear = date("Y");
            $DefaultMonth = date("m");
            
            $this->Year=$defaultYear;
            $this->Month=$DefaultMonth;
        }
    
        //Year      
        function setYear($intNewYear){
            $this->Year = $intNewYear;
        }
    
        function getYear(){
            return $this->Year;
        }
        
        //Leap Year - Return true or 1 is Leap Year
        function getLeapYear(){
            $intBaki = $this->Year % 4;
            if($intBaki == 0){
                return 1; 
            }else{
                return 0;
            }
        }
        
        //Month
        function setMonth($intMonth){
            $this->Month=$intMonth;
        }
        
        function getMonth(){
            return $this->Month;
        }
        
        //Count how many days in a month
        function getDaysInMonth(){
            switch($this->getMonth()){
                case 1:
                    $this->DayCount = 31;                   
                    break;
                case 2:
                    if($this->getLeapYear() == 0){
                        $this->DayCount = 28;                   
                    }else{
                        $this->DayCount = 29;                   
                    }
                    break;
                case 3:
                    $this->DayCount = 31;                   
                    break;
                case 4:
                    $this->DayCount = 30;                   
                    break;
                case 5:
                    $this->DayCount = 31;                   
                    break;
                case 6:
                    $this->DayCount = 30;                   
                    break;
                case 7:
                    $this->DayCount = 31;                   
                    break;
                case 8:
                    $this->DayCount = 31;                   
                    break;
                case 9:
                    $this->DayCount = 30;                   
                    break;
                case 10:
                    $this->DayCount = 31;                   
                    break;
                case 11:
                    $this->DayCount = 30;                   
                    break;
                case 12:
                    $this->DayCount = 31;                   
                    break;
            }
            return $this->DayCount;     
        }
        
        //Function to get the first day of the month
        function getFirstDayOfMonth(){
            $strDate = date("w", mktime(0,0,0,$this->Month,1,$this->Year));
            return $this->FirstDayOfMonth=$strDate;
        }
        
        function getLongMonth(){
        	switch($this->getMonth()){
	        	case 1:
	        		return $this->LongMonth="JANUARI";
	        		break;
	        	case 2:
	        		return $this->LongMonth="FEBRUARI";
	        		break;
	        	case 3:
	        		return $this->LongMonth="MAC";
	        		break;
	        	case 4:
	        		return $this->LongMonth="APRIL";
	        		break;
	        	case 5:
	        		return $this->LongMonth="MEI";
	        		break;
	        	case 6:
	        		return $this->LongMonth="JUN";
	        		break;
	        	case 7:
	        		return $this->LongMonth="JULAI";
	        		break;
	        	case 8:
	        		return $this->LongMonth="OGOS";
	        		break;
	        	case 9:
	        		return $this->LongMonth="SEPTEMBER";
	        		break;
	        	case 10:
	        		return $this->LongMonth="OKTOBER";
	        		break;
	        	case 11:
	        		return $this->LongMonth="NOVEMBER";
	        		break;
	        	case 12:
	        		return $this->LongMonth="DISEMBER";
	        		break;
	        	}
        }
        
}

//Function Format Date
function FormatDate($intDD,$intMM,$intYY,$strFormat){
        	switch ($strFormat){
        		case "DD/MM/YYYY" :
        			if($intDD < 10){
        				$intDD = "0$intDD"; 
        			}
        			if($intMM < 10){
        				$intMM = "0$intMM"; 
        			}
	        		$dtmDate = "$intDD/$intMM/$intYY";
					return $this->FormatDate=$dtmDate;
					break;
				case "DD.MM.YYYY" :
        			if($intDD < 10){
        				$intDD = "0$intDD"; 
        			}
        			if($intMM < 10){
        				$intMM = "0$intMM"; 
        			}
	        		$dtmDate = "$intDD.$intMM.$intYY";
					return $this->FormatDate=$dtmDate;
					break;
        	}
        }


//Main()
//Create object Calendar
$objKalendar = new Kalendar();

if (!empty($_GET['prevYear'])){
	$objKalendar->setYear($_GET['prevYear']);//Set current year to prev year
}elseif (!empty($_GET['nextYear'])){
	$objKalendar->setYear($_GET['nextYear']);//Set current year to next year
}elseif(!empty($_GET['curYear'])){
	$objKalendar->setYear(($_GET['curYear']));
}else{	
	$objKalendar->setYear(date("Y"));//Set default year
}


if(!empty($_GET['prevMonth'])){
	$objKalendar->setMonth(($_GET['prevMonth']));//Set current month to prev month
}elseif(!empty($_GET['nextMonth'])){
	$objKalendar->setMonth(($_GET['nextMonth']));//Set current month to next month
}else{	
	$objKalendar->setMonth(date("m")); //Set default month
}


//Variables for calendar
$intDays = 1; //Start to end day of the month
$intAllDays = 0; //All days (35 days)

$intDayInMonth = $objKalendar->getDaysInMonth();//Get days in month (28-31)
$intStartDayOfMonth = $objKalendar->getFirstDayOfMonth();//Get start day of the month (0-6)
$intYear = $objKalendar->getYear();//Get year
$strLongMonth = $objKalendar->getLongMonth();//Get Long Month eg: September
$intMonth = $objKalendar->getMonth();
//$gdtmDate = $objKalendar->getFormatDate(2,1,2002,"DD/MM/YYYY");
//print "Format date : $gdtmDate";

$d=date("d")-0;
$m=date("m")-0;
$y=date("Y");
$Today = FormatDate($d,$m,$y,'DD/MM/YYYY');
?>
