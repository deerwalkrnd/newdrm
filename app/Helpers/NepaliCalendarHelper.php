<?php 
namespace App\Helpers;
class NepaliCalendarHelper{
	private $nepali_date = ['year'=>'','month'=>'','day'=>''];
	private $english_date = ['year'=>'','month'=>'','day'=>''];

	private $min_eng_year = 2011;
	private $max_eng_year = 2022;

	private $min_nep_year = 2067;
	private $max_nep_year = 2079;

	public $status = false;

	private $eng_month_day = array(
		'01' => 31,
		'02' => 28,
		'03' => 31,
		'04' => 30,
		'05' => 31,
		'06' => 30,
		'07' => 31,
		'08' => 31,
		'09' => 30,
		'10' => 31,
		'11' => 30,
		'12' => 31
	);

	private $eng_days = array(31,28,31,30,31,30,31,31,30,31,30,31);
	private $eng_days_leap = array(31,28,31,30,31,30,31,31,30,31,30,31);

	private $nep_month_day = array(
		'2067' => array(31,31,32,31,31,31,30,29,30,29,30,30),
		'2068' => array(31,31,32,32,31,30,30,29,30,29,30,30),
		'2069' => array(31,32,31,32,31,30,30,30,29,29,30,31),
		'2070' => array(31,31,31,32,31,31,29,30,30,29,30,30),
		'2071' => array(31,31,32,31,31,31,30,29,30,29,30,30),//
		'2072' => array(31,32,31,32,31,30,30,29,30,29,30,30),
		'2073' => array(31,32,31,32,31,30,30,30,29,29,30,31),
		'2074' => array(31,31,31,32,31,31,30,29,30,29,30,30),
		'2075' => array(31,31,32,31,31,31,30,29,30,29,30,30),
		'2076' => array(31,32,31,32,31,30,30,30,29,29,30,30),
		'2077' => array(31,32,31,32,31,30,30,30,29,30,29,31),
		'2078' => array(31,31,31,32,31,31,30,29,30,29,30,30),
		'2079' => array(31,31,32,31,31,31,30,29,30,29,30,30),
		'2080' => array(31,32,31,32,31,30,30,30,29,29,30,30),
        '2081' => array(31,31,32,32,31,30,30,30,29,30,30,30),
        '2082' => array(30,32,31,32,31,30,30,30,29,30,30,30),
        '2083' => array(31,31,32,31,31,30,30,30,29,30,30,30),
        '2084' => array(31,31,32,31,31,30,30,30,29,30,30,30),
        '2085' => array(31,32,31,32,30,31,30,30,29,30,30,30),
        '2086' => array(30,32,31,32,31,30,30,30,29,30,30,30),
        '2087' => array(31,31,32,31,31,31,30,30,29,30,30,30),
        '2088' => array(30,31,32,32,30,31,30,30,29,30,30,30),
        '2089' => array(30,32,31,32,31,30,30,30,29,30,30,30),
        '2090' => array(30,32,31,31,30,30,30,30,29,30,30,30),//not sure (hamropatro doesns't have and had to take some other ref and self analyze some data)

	);

	private $nep_eng = array(
		'2067' => array('2010','04','14'),
		'2068' => array('2011','04','14'),
		'2069' => array('2012','04','13'),
		'2070' => array('2013','04','14'),
		'2071' => array('2014','04','14'),
		'2072' => array('2015','04','14'),
		'2073' => array('2016','04','13'),
		'2074' => array('2017','04','14'),
		'2075' => array('2018','04','14'),
		'2076' => array('2019','04','14'),
		'2077' => array('2020','04','13'),
		'2078' => array('2021','04','14'),
		'2079' => array('2022','04','14'),		
		'2080' => array('2023','04','14'),		
        '2081' => array('2024','04','13'),
        '2082' => array('2025','04','14'),
        '2083' => array('2026','04','14'),
        '2084' => array('2027','04','14'),
        '2085' => array('2028','04','13'),
        '2086' => array('2029','04','14'),
        '2087' => array('2030','04','14'),
        '2088' => array('2031','04','15'),
        '2089' => array('2032','04','14'),
        '2090' => array('2033','04','14'),
	);


	//date format('yyyy-mm-dd')
	// $type = 0 // nepali date
	// $type = 1 // english date
	public function __construct($date,$type = 0){
		$date = explode('-', $date);
		if(count($date) == 3)
			if($type == 0){
				if($this->is_valid_nep_date($date)){
					// return true;
					$this->status = true;
					$this->set_nepali_date($date);
					$this->set_english_date($this->to_english($date));
					return true;
				}
			}elseif($type == 1){
				// echo "Here";
				if($this->is_valid_eng_date($date)){
					$this->status = true;
					$this->set_english_date($date);
					$this->set_nepali_date($this->to_nepali($date));
					return true;
				}
			}
		throw new Exception('Invalid Date');
	}

	private function set_nepali_date($date)
	{
		$this->nepali_date['year'] = $date[0];
		$this->nepali_date['month'] = sprintf("%02d", $date[1]);
		$this->nepali_date['day'] = sprintf("%02d", $date[2]);
	}

	private function set_english_date($date)
	{
		$this->english_date['year'] = $date[0];
		$this->english_date['month'] = sprintf("%02d", $date[1]);
		$this->english_date['day'] = sprintf("%02d", $date[2]);	
	}

	private function is_valid_eng_date($date)
	{
		// return true;
		$year = $date[0];
		$month = (int)$date[1];
		$day = $date[2];

		if($this->is_valid_eng_year($year))
			if($this->is_valid_month($month))
				if($this->is_valid_eng_day($date))
					return true;

		return false;
	}

	private function is_valid_nep_date($date)
	{
		$year = $date[0];
		$month = (int)$date[1];
		$day = $date[2];

		if($this->is_valid_nep_year($year))
			if($this->is_valid_month($month))
				if($this->is_valid_nep_day($date))
					return true;

		return false;
	}

	private function is_valid_eng_year($year)
	{
		//english year valid between min and max english year
		if($year >= $this->min_eng_year && $year <= $this->max_eng_year)
			return true;
		else
			return false;
	}

	private function is_valid_nep_year($year)
	{
		//english year valid between min and max english year
		if($year >= $this->min_nep_year && $year <= $this->max_nep_year)
			return true;
		else
			return false;
	}

	private function is_valid_month($month)
	{
		if($month >= 1 && $month <= 12)
			return true;
		else
			return false;
	}

	private function is_valid_eng_day($date)
	{
		$year = $date[0];
		$month = $date[1];
		$day = $date[2];
		$is_leap_year = $year % 4 == 0; 
		
		if($day >= 1 and $day <= $this->eng_month_day[$month])
			return true;
		elseif($is_leap_year and $month == '02' and $day == 29)
			return true;
		else
			return false;
	}

	private function is_valid_nep_day($date)
	{
		$year = $date[0];
		$month = (int)$date[1];
		$day = (int)$date[2];
		
		if($day >= 1 and $day <= $this->nep_month_day[$year][$month-1])
			return true;
		else
			return false;
	
	}

	public function in_bs()
	{
		return implode('-',$this->nepali_date);
	}

	public function in_ad()
	{
		return implode('-',$this->english_date);
	}

	public function compare($date1,$date2)
	{
		if((int)$date1[0] > (int)$date2[0])
			return -1;
		elseif((int)$date1[0] < (int)$date2[0])
			return 1;
		elseif((int)$date1[1] > (int)$date2[1])
			return -1;
		elseif((int)$date1[1] < (int)$date2[1])
			return 1;
		elseif((int)$date1[2] > (int)$date2[2])
			return -1;
		elseif((int)$date1[2] < (int)$date2[2])
			return 1;
		else
			return 0;
	}

	private function to_nepali($date)
	{
		$nepali_year = $date[0] + 57;
		$base_date = $this->nep_eng[$nepali_year];
		if($this->compare($base_date,$date) == -1)
		{
			$nepali_year--;
			$base_date = $this->nep_eng[$nepali_year]; 
		}

		$timeDiff = strtotime(implode('-',$date)) - strtotime(implode('-',$base_date));
		$days = (int)($timeDiff / 86400) + 1;

		$nepali_month = 1;

		foreach($this->nep_month_day[$nepali_year] as $mth)
		{
			if($days > $mth)
			{
				$days -= $mth;
				$nepali_month++;
			}else{
				$nepali_day = $days;
			}
		}

		return array($nepali_year, $nepali_month, $nepali_day);
	}

	private function to_english($date)
	{
		$nepali_year = $date[0];
		$nepali_month = $date[1];
		$nepali_day = $date[2];

		$base_date = $this->nep_eng[$nepali_year];

		$english_year = $base_date[0];
		$english_month = $base_date[1];
		$english_day = $base_date[2];

		$month = (int)$nepali_month - 1;
		$days = $nepali_day - 1;
		while($month != 0)
		{
			$days += $this->nep_month_day[$nepali_year][$month-1];
			$month--;
		}

		$english_day = $english_day + $days;

		while(True){
			if($english_year % 4 == 0){
				$max_day = $this->eng_days_leap[(int)$english_month - 1];
			}else{
				$max_day = $this->eng_days[(int)$english_month - 1];
			}

			if($english_day > $max_day)
			{
				$english_day -= $max_day;
				$english_month++;
				
				if($english_month > 12)
				{
					$english_month = 1;
					$english_year++; 
				}
			}else{
				break;
			}
		}

		return array($english_year, $english_month, $english_day);
	} 
}

 ?>