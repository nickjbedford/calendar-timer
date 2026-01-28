<?php
	namespace YetAnother;
	
	use Exception;
	use PHPUnit\Framework\TestCase;
	
	class WeekDayTimerTests extends TestCase
	{
		/**
		 * @throws Exception
		 */
		public function testWeekDayTimerScheduledDatesAreCorrect()
		{
			$schedule = new WeekDayTimer([ Weekday::Monday, Weekday::Thursday, Weekday::Saturday ]);
			$dates = [
				'2026-01-05', // Monday
				'2026-01-08', // Thursday
				'2026-01-10', // Saturday
				'2026-01-12', // Monday
				'2026-01-15', // Thursday
				'2026-01-17', // Saturday
			];
			
			foreach($dates as $date)
				$this->assertTrue($schedule->isScheduleDate($date));
		}
		
		/**
		 * @throws Exception
		 */
		public function testWeekDayTimerNonScheduledDatesAreCorrect()
		{
			$schedule = new WeekDayTimer([ Weekday::Monday, Weekday::Thursday, Weekday::Saturday ]);
			$dates = [
				'2026-01-06', // Tuesday
				'2026-01-07', // Wednesday
				'2026-01-09', // Friday
				'2026-01-11', // Sunday
				'2026-01-13', // Tuesday
				'2026-01-14', // Wednesday
				'2026-01-16', // Friday
			];
			
			foreach($dates as $date)
				$this->assertFalse($schedule->isScheduleDate($date));
		}
		
		/**
		 * @throws Exception
		 */
		public function testWeekDayTimerNextIntervalDatesAreCorrect()
		{
			$schedule = new WeekDayTimer([ Weekday::Monday, Weekday::Thursday, Weekday::Saturday ]);
			$dates = [
				'2026-01-05', // Monday
				'2026-01-08', // Thursday
				'2026-01-10', // Saturday
				'2026-01-12', // Monday
				'2026-01-15', // Thursday
				'2026-01-17', // Saturday
			];
			
			foreach($dates as $offset=>$date)
				$this->assertEquals($date, $schedule->getNextDate($offset, $dates[0]));
		}
		
		/**
		 * @throws Exception
		 */
		public function testWeekDayTimerPreviousIntervalDatesAreCorrect()
		{
			$schedule = new WeekDayTimer([ Weekday::Monday, Weekday::Thursday, Weekday::Saturday ]);
			$dates = [
				'2026-01-03', // Saturday
				'2026-01-01', // Thursday
				'2025-12-29', // Monday
				'2025-12-27', // Saturday
				'2025-12-25', // Thursday
				'2025-12-22', // Monday
				'2025-12-20', // Monday
			];
			
			foreach($dates as $offset=>$date)
				$this->assertEquals($date, $schedule->getNextDate(-1 - $offset, '2026-01-05'));
		}
	}
