<?php
	/** @noinspection PhpUnused */
	
	namespace YetAnother;
	
	use Carbon\Carbon;
	use DateInterval;
	use Exception;
	
	/**
	 * Represents a calendar scheduler based on chosen days of the week.
	 * This can be used to calculate future or past schedule dates based on a reference date.
	 * @package YetAnother
	 */
	class WeekDayTimer extends CalendarTimer
	{
		/**
		 * Initialises a new weekday-based scheduler.
		 * @param int[] $daysOfWeek
		 * @throws Exception
		 */
		public function __construct(public array $daysOfWeek = Weekday::MondayToFriday)
		{
			parent::__construct('2000-01-01', 1, 'days');
			$this->daysOfWeek = array_map(fn($day) => intval($day),
				array_filter(array_unique($this->daysOfWeek), fn($day) => in_array($day, Weekday::All)));
		}
		
		/**
		 * @inheritDoc
		 */
		protected function calculateIntervalsUntilNext(DateInterval $difference): int
		{
			return 0;
		}
		
		/**
		 * @inheritDoc
		 */
		public function getNextDate(int $intervalOffset = 0, ?string $from = null): string
		{
			$carbon = new Carbon($from ?? date('Y-m-d'));
			$step = $intervalOffset >= 0 ? 1 : -1;
			
			if ($intervalOffset >= 0)
				$intervalOffset++;
			else if (in_array(intval($carbon->format('w')), $this->daysOfWeek))
				$intervalOffset--;
			
			$remaining = abs($intervalOffset);
			while ($remaining > 0)
			{
				$currentWeekday = intval($carbon->format('w'));
				if (in_array($currentWeekday, $this->daysOfWeek))
				{
					$remaining--;
					if ($remaining === 0)
						break;
				}
				$carbon->addDays($step);
			}
			return $carbon->format('Y-m-d');
		}
	}
