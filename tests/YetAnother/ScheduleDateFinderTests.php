<?php
	namespace YetAnother;
	
	use Exception;
	use PHPUnit\Framework\TestCase;
	
	class ScheduleDateFinderTests extends TestCase
	{
		protected function setUp(): void
		{
			parent::setUp();
			ScheduleFinder::$defaultExcludedHolidays = [];
		}
		
		/**
		 * @throws Exception
		 */
		function testEveryCalendarDayAndWeekdaysFindsDate()
		{
			/** @var ScheduleAlgorithm $algorithm */
			foreach([ ScheduleAlgorithm::NextPreferredWorkday, ScheduleAlgorithm::NextStandardWorkday ] as $algorithm)
			{
				$schedule = new ScheduleFinder(
					standardWorkdays: Weekday::MondayToFriday,
					algorithm: $algorithm);
				
				foreach([
					'2024-07-01' => '2024-07-01',
					'2024-07-02' => '2024-07-02',
					'2024-07-03' => '2024-07-03',
					'2024-07-04' => '2024-07-04',
					'2024-07-05' => '2024-07-05',
					'2024-07-06' => '2024-07-08',
					'2024-07-07' => '2024-07-08',
					'2024-07-08' => '2024-07-08',
				] as $from=>$expected)
 					$this->assertEquals($expected, $schedule->nextAsString($from), "$algorithm->name, from: $from, expected: $expected");
				
				$schedule = new ScheduleFinder(
					standardWorkdays: Weekday::MondayToFriday,
					excludedDates: [ '2024-07-03' ],
					algorithm:     $algorithm);
				
				foreach([
					'2024-07-01' => '2024-07-01',
					'2024-07-02' => '2024-07-02',
					'2024-07-03' => '2024-07-04',
					'2024-07-04' => '2024-07-04',
					'2024-07-05' => '2024-07-05',
					'2024-07-06' => '2024-07-08',
					'2024-07-07' => '2024-07-08',
					'2024-07-08' => '2024-07-08',
				] as $from=>$expected)
					$this->assertEquals($expected, $schedule->nextAsString($from), $algorithm->name);
			}
		}
		
		/**
		 * @throws Exception
		 */
		function testEveryCalendarDayAndSelectWeekdaysFindsDate()
		{
			foreach([ ScheduleAlgorithm::NextPreferredWorkday, ScheduleAlgorithm::NextStandardWorkday ] as $algorithm)
			{
				$schedule = new ScheduleFinder(
					standardWorkdays: Weekday::MondayWednesdayFriday,
					algorithm: $algorithm);
				
				foreach([
					'2024-07-01' => '2024-07-01',
					'2024-07-02' => '2024-07-03',
					'2024-07-03' => '2024-07-03',
					'2024-07-04' => '2024-07-05',
					'2024-07-05' => '2024-07-05',
					'2024-07-06' => '2024-07-08',
					'2024-07-07' => '2024-07-08',
					'2024-07-08' => '2024-07-08',
				] as $from=>$expected)
					$this->assertEquals($expected, $schedule->nextAsString($from), $algorithm->name);
				
				$schedule = new ScheduleFinder(
					standardWorkdays: Weekday::MondayWednesdayFriday,
					excludedDates: [
						'2024-07-03',
						'2024-07-08',
					],
					algorithm:     $algorithm);
				
				foreach([
					'2024-07-01' => '2024-07-01',
					'2024-07-02' => '2024-07-05',
					'2024-07-03' => '2024-07-05',
					'2024-07-04' => '2024-07-05',
					'2024-07-05' => '2024-07-05',
					'2024-07-06' => '2024-07-10',
					'2024-07-07' => '2024-07-10',
					'2024-07-08' => '2024-07-10',
				] as $from=>$expected)
					$this->assertEquals($expected, $schedule->nextAsString($from), $algorithm->name);
			}
		}
		
		/**
		 * @throws Exception
		 */
		function testEveryCalendarDayAndMondayFindsDate()
		{
			foreach([ ScheduleAlgorithm::NextPreferredWorkday, ScheduleAlgorithm::NextStandardWorkday ] as $algorithm)
			{
				$schedule = new ScheduleFinder(
					standardWorkdays: [ Weekday::Monday ],
					algorithm: $algorithm);
				
				foreach([
					'2024-07-01' => '2024-07-01',
					'2024-07-02' => '2024-07-08',
					'2024-07-03' => '2024-07-08',
					'2024-07-04' => '2024-07-08',
					'2024-07-05' => '2024-07-08',
					'2024-07-06' => '2024-07-08',
					'2024-07-07' => '2024-07-08',
					'2024-07-08' => '2024-07-08',
					'2024-07-09' => '2024-07-15',
				] as $from=>$expected)
					$this->assertEquals($expected, $schedule->nextAsString($from), $algorithm->name);
			}
		}
		
		/**
		 * @throws Exception
		 */
		function testSpecificCalendarDaysFindsNextDate()
		{
			$schedule = new ScheduleFinder(
				preferredCalendar: ScheduleFinder::createPreferredCalendar([ 5, 15, 25 ]));
			
				foreach([
					'2024-07-05' => [
						'2024-06-26',
						'2024-06-27',
						'2024-06-28',
						'2024-06-29',
						'2024-06-30',
						'2024-07-01',
						'2024-07-02',
						'2024-07-03',
						'2024-07-04',
						'2024-07-05',
					],
					'2024-07-15' => [
						'2024-07-06',
						'2024-07-07',
						'2024-07-08',
						'2024-07-09',
						'2024-07-10',
						'2024-07-11',
						'2024-07-12',
						'2024-07-13',
						'2024-07-14',
						'2024-07-15',
					],
					'2024-07-25' => [
						'2024-07-16',
						'2024-07-17',
						'2024-07-18',
						'2024-07-19',
						'2024-07-20',
						'2024-07-21',
						'2024-07-22',
						'2024-07-23',
						'2024-07-24',
						'2024-07-25',
					],
				] as $expected=>$froms)
				{
					foreach($froms as $from)
						$this->assertEquals($expected, $schedule->nextAsString($from), "expected: $expected, from: $from");
				}
		}
		
		/**
		 * @throws Exception
		 */
		function testSpecificCalendarDaysInAugustFindsNextPreferredDate()
		{
			$schedule = new ScheduleDesigner()
				->preferCalendarDaysInSpecificMonths(days: [ 5, 15, 25 ])
				->availableMondayToFriday()
				->create();
			
			$this->assertCount(5, $schedule->standardWorkdays);
			
				foreach([
					'2024-08-05' => [
						'2024-07-26',
						'2024-07-27',
						'2024-07-28',
						'2024-07-29',
						'2024-07-30',
						'2024-08-01',
						'2024-08-02',
						'2024-08-03',
						'2024-08-04',
						'2024-08-05',
					],
					'2024-08-15' => [
						'2024-08-06',
						'2024-08-07',
						'2024-08-08',
						'2024-08-09',
						'2024-08-10',
						'2024-08-11',
						'2024-08-12',
						'2024-08-13',
						'2024-08-14',
						'2024-08-15',
					],
					'2024-08-23' => [
						'2024-08-16',
						'2024-08-17',
						'2024-08-18',
						'2024-08-19',
						'2024-08-20',
						'2024-08-21',
						'2024-08-22',
						'2024-08-23',
					],
					'2024-08-26' => [
						'2024-08-24',
						'2024-08-25',
					],
				] as $expected=>$froms)
				{
					foreach($froms as $from)
						$this->assertEquals($expected, $schedule->nextAsString($from), $from);
				}
		}
		
		/**
		 * @throws Exception
		 */
		function testSpecificCalendarDaysInAugustFindsPreviousDate()
		{
			$schedule = new ScheduleFinder(
				standardWorkdays: [ Weekday::Monday, Weekday::Tuesday, Weekday::Wednesday ],
				preferredCalendar: ScheduleFinder::createPreferredCalendar([ 7, 15, 25 ]));
			
				foreach([
					'2024-07-03' => [
						'2024-06-26',
						'2024-06-27',
						'2024-06-28',
						'2024-06-29',
						'2024-06-30',
						'2024-07-01',
						'2024-07-02',
						'2024-07-03',
					],
					'2024-07-08' => [
						'2024-07-04',
						'2024-07-05',
						'2024-07-06',
						'2024-07-07',
					],
					'2024-07-15' => [
						'2024-07-08',
						'2024-07-09',
						'2024-07-10',
						'2024-07-11',
						'2024-07-12',
						'2024-07-13',
						'2024-07-14',
						'2024-07-15',
					],
					'2024-07-24' => [
						'2024-07-16',
						'2024-07-17',
						'2024-07-18',
						'2024-07-19',
						'2024-07-20',
						'2024-07-21',
						'2024-07-22',
						'2024-07-23',
						'2024-07-24',
					],
					'2024-07-29' => [
						'2024-07-25',
					],
				] as $expected=>$froms)
				{
					foreach($froms as $from)
						$this->assertEquals($expected, $schedule->nextAsString($from), $from);
				}
		}
		
		/**
		 * @throws Exception
		 */
		function testSpecificCalendarDaysInAugustFindsPreviousDateWithEarlierNotBefore()
		{
			$schedule = new ScheduleFinder(
				standardWorkdays: [ Weekday::Monday, Weekday::Wednesday ],
				preferredCalendar: ScheduleFinder::createPreferredCalendar([ 7, 15, 25 ]));
			
				foreach([
					'2024-07-03' => [
						'2024-07-04',
						'2024-07-05',
						'2024-07-06',
						'2024-07-07',
					],
					'2024-07-15' => [
						'2024-07-08',
						'2024-07-09',
						'2024-07-10',
						'2024-07-11',
						'2024-07-12',
						'2024-07-13',
						'2024-07-14',
						'2024-07-15',
					],
				] as $expected=>$froms)
				{
					foreach($froms as $from)
						$this->assertEquals($expected, $schedule->nextAsString($from, '2024-07-03'), $from);
				}
		}
		
		/**
		 * @throws Exception
		 */
		function testSpecificClosestWorkdayScheduleWorks()
		{
			$schedule = new ScheduleFinder(
				standardWorkdays:  [Weekday::Monday, Weekday::Wednesday, Weekday::Friday],
				preferredCalendar: ScheduleFinder::createPreferredCalendar([5, 15, 25]),
				excludedDates:     ['2024-06-17'],
				algorithm:         ScheduleAlgorithm::ClosestStandardWorkday);
			
			/**
			 * '2024-06-05' Tuesday because 5th is a Wednesday so the next
			 * available calendar date.
			 */
			$this->assertEquals('2024-06-05', $schedule->nextAsString(from: '2024-06-04'));
			
			/**
			 * '2024-06-14' Friday because 15th is a Saturday so the "closest workday"
			 * (not before 6th June) is the Friday before the 15th.
			 */
			$this->assertEquals('2024-06-14', $schedule->nextAsString(from: '2024-06-06'));
			
			/**
			 * '2024-06-19' Wednesday because the 17th (Monday) is a holiday. The 15th is
			 * a Saturday so the next available workday is the Wednesday after the 17th.
			 */
			$this->assertEquals('2024-06-19', $schedule->nextAsString(from: '2024-06-15'));
			
			/**
			 * '2024-06-24' Monday because the 25th (Tuesday) is not a workday so the closest
			 * workday (not before 17th June) is the Monday before the 25th.
			 */
			$this->assertEquals('2024-06-24', $schedule->nextAsString(from: '2024-06-17'));
			
			/**
			 * '2024-06-26' Wednesday because the 25th (Tuesday) is not a workday
			 * so the closest workday (not before 25th June) is the Wednesday after the 25th.
			 */
			$this->assertEquals('2024-06-26', $schedule->nextAsString(from: '2024-06-25'));
		}
		
		/**
		 * @throws Exception
		 */
		function testPreferredWorkdaysFallsBackToAvailableWorkdaysDueToPublicHoliday()
		{
			$schedule = new ScheduleDesigner()
				->availableMondayToFriday()
				->preferFridays()
				->useAlgorithm(ScheduleAlgorithm::NextPreferredThenClosestStandardWorkday)
				->excludeDate('2024-07-05')
				->create();
			
			foreach([
				[ '2024-07-04', '2024-07-01', null ],
				[ '2024-07-04', '2024-07-02', null ],
				[ '2024-07-04', '2024-07-03', null ],
				[ '2024-07-04', '2024-07-04', null ],
				[ '2024-07-04', '2024-07-05', '2024-07-01' ], // not before is earlier which allows for the Thursday 4th
				[ '2024-07-08', '2024-07-05', null ],
				[ '2024-07-12', '2024-07-06', null ],
				[ '2024-07-12', '2024-07-07', null ],
				[ '2024-07-12', '2024-07-08', null ],
				[ '2024-07-12', '2024-07-09', null ],
				[ '2024-07-12', '2024-07-10', null ],
				[ '2024-07-12', '2024-07-11', null ],
				[ '2024-07-12', '2024-07-12', null ],
			] as $item)
			{
				$expected = $item[0];
				$from = $item[1];
				$notBefore = $item[2] ?? $from;
				$this->assertEquals($expected, $schedule->nextAsString($from, $notBefore), "from: $from, notBefore: $notBefore");
			}
		}
		
		/**
		 * @throws Exception
		 */
		function testOnlyPreferredCalendarIsUsedToFindDatesWithAlgorithm()
		{
			$schedule = new ScheduleDesigner()
				->preferDaysInFebruary([ 15 ])
				->preferDaysInApril([ 5 ])
				->availableAllWeek()
				->useAlgorithm(ScheduleAlgorithm::OnlyPreferredDates)
				->excludeDate('2026-02-15')
				->create();
			
			foreach([
				'2025-02-15' => '2024-07-01',
				'2025-04-05' => '2025-02-16',
				'2026-04-05' => '2025-07-01',
			] as $expected=>$from)
			{
				$this->assertEquals($expected, $schedule->nextAsString(from: $from), $from);
			}
		}
		
		/**
		 * @throws Exception
		 */
		function testFindClosestPreferredWorkdayBeforeUsingAlgorithm()
		{
			$schedule = new ScheduleDesigner()
				->preferCalendarDaysInSpecificMonths([ 15 ])
				->preferSpecificWeekdays(Weekday::MondayWednesdayFriday)
				->availableMondayToFriday()
				->findingClosestPreferredThenClosestStandardWorkday()
				->create();
			
			$notBefore = '2024-07-01';
			$expected = '2024-07-15';
			$dates = [
				'2024-07-01',
				'2024-07-05',
				'2024-07-08',
				'2024-07-14',
				'2024-07-15',
				'2024-07-16',
			];
			
			foreach($dates as $date)
				$this->assertEquals($expected, $schedule->findClosestPreferredDate($date, $notBefore)->toDateString(), $date);
			
			$notBefore = '2024-07-16';
			$expected = '2024-08-15';
			$dates = [
				'2024-07-16',
				'2024-08-05',
			];
			
			foreach($dates as $date)
				$this->assertEquals($expected, $schedule->findClosestPreferredDate($date, $notBefore)->toDateString(), $date);
		}
		
		/**
		 * @throws Exception
		 */
		function testFindClosestAvailableDate()
		{
			$designer = new ScheduleDesigner()
				->preferCalendarDaysInSpecificMonths([15])
				->preferSpecificWeekdays(Weekday::MondayWednesdayFriday)
				->availableMondayToFriday()
				->findingClosestPreferredThenClosestStandardWorkday();
			
			$schedule = $designer->create();
			
			$this->assertEquals('2024-07-15', $schedule->closest('2024-07-18', '2024-07-01')->toDateString());
			$this->assertEquals('2024-07-15', $schedule->closest('2024-08-14', '2024-07-01')->toDateString());
			$this->assertEquals('2024-08-15', $schedule->closest('2024-08-14', '2024-07-16')->toDateString());
			$this->assertEquals('2024-08-15', $schedule->closest('2024-09-14', '2024-07-16')->toDateString());
			
			$designer->excludeDate('2024-08-15');
			$schedule = $designer->create();
			
			$this->assertEquals('2024-07-15', $schedule->closest('2024-07-18', '2024-07-01')->toDateString());
			$this->assertEquals('2024-07-15', $schedule->closest('2024-08-14', '2024-07-01')->toDateString());
			$this->assertEquals('2024-08-14', $schedule->closest('2024-08-14', '2024-07-16')->toDateString());
			$this->assertEquals('2024-08-14', $schedule->closest('2024-09-14', '2024-07-16')->toDateString());
			$this->assertEquals('2024-08-16', $schedule->closest('2024-09-14', '2024-08-15')->toDateString());
			$this->assertEquals('2024-09-13', $schedule->closest('2024-09-14', '2024-08-16')->toDateString());
			$this->assertEquals('2024-09-16', $schedule->closest('2024-09-14', '2024-09-14')->toDateString());
			
			$schedule = new ScheduleDesigner()
				->availableMondayToFriday()
				->preferSpecificWeekdays([ Weekday::Tuesday, Weekday::Friday ])
				->create();
			
			$this->assertEquals('2024-07-09', $schedule->closest('2024-07-11', '2024-07-01')->toDateString());
			
			$schedule = new ScheduleDesigner()
				->availableMondayToFriday()
				->preferSpecificWeekdays([ Weekday::Tuesday, Weekday::Friday ])
				->excludeDate('2024-07-09')
				->create();
			
			$this->assertEquals('2024-07-08', $schedule->closest('2024-07-11', '2024-07-01')->toDateString());
		}
	}
