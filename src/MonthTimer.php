<?php
	/** @noinspection PhpUnused */
	
	namespace YetAnother;
	
	use DateInterval;
	use Exception;
	
	/**
	 * Represents a calendar scheduler based on an interval measured
	 * in months. This can be used to calculate future or past schedule
	 * dates based on a reference date.
	 * @package YetAnother
	 */
	class MonthTimer extends CalendarTimer
	{
		/**
		 * Initialises a new month-based scheduler.
		 * @param string $referenceDate A reference date to calculate schedule dates.
		 * @param int $interval The number of days for the interval.
		 * @throws Exception
		 */
		public function __construct(string $referenceDate, int $interval = 1)
		{
			parent::__construct($referenceDate, $interval, 'months');
		}
		
		/**
		 * @inheritDoc
		 */
		protected function calculateIntervalsUntilNext(DateInterval $difference): int
		{
			$months = $difference->y * 12 + $difference->m;
			[$intervals, $remainder] = self::modulus($months, $this->interval);
			
			if ($difference->invert === 0) // $difference is in the future
			{
				if ($remainder === 0 && $difference->d > 0)
					$intervals++;
				return $intervals + ($remainder > 0 ? 1 : 0);
			}
			
			// $difference is in the past
			return -$intervals;
		}
	}
