<?php
	/** @noinspection PhpUnused */
	
	namespace YetAnother;
	
	use DateInterval;
	use Exception;
	
	/**
	 * Represents a calendar scheduler based on an interval measured
	 * in days. This can be used to calculate future or past schedule
	 * dates based on a reference date.
	 * @package YetAnother
	 */
	class DayTimer extends CalendarTimer
	{
		/**
		 * Initialises a new day-based scheduler.
		 * @param string $referenceDate A reference date to calculate schedule dates.
		 * @param int $interval The number of days for the interval.
		 * @throws Exception
		 */
		public function __construct(string $referenceDate, int $interval = 7)
		{
			parent::__construct($referenceDate, $interval, 'days');
		}
		
		/**
		 * @inheritDoc
		 */
		protected function calculateIntervalsUntilNext(DateInterval $difference): int
		{
			$days = $difference->days;
			[$intervals, $remainder] = self::modulus($days, $this->interval);
			
			if ($difference->invert === 0) // $difference is in the future
				return $intervals + ($remainder > 0 ? 1 : 0);
			
			// $difference is in the past
			return -$intervals;
		}
	}
