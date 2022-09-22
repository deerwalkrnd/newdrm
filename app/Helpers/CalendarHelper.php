<?php 
namespace App\Helpers;
class CalendarHelper extends NepaliCalendarHelper{
	private $nep_month_day;
	public function __construct(){
		// dd("here");
		$this->getLastDayOfMonth();
	}
	public function getLastDayOfMonth($year = null , $month = null)
	{
		$this->nep_month_day = $this->getNepaliMonthDay();
		// dd($this->nep_month_day);
		
		if($year != null && $month != null)
		{
			return $this->nep_month_day[$year][$month - 1];
		}
	}
}

 ?>

 