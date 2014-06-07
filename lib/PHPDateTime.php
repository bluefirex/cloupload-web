<?php
	class PHPDateTime {
		/*
		 *	MODE-DESCRIPTION
		 *	================
		 *
		 *	SECONDS = x seconds
		 *	MINUTES = x minutes
		 * 	HOURS = x hours
		 *	DAYS = x days
		 *	WEEKS = x weeks
		 *	MONTHS = x weeks
		 *	YEARS = x years
		 *	ALL = xy xm xd
		 *
		 *	CHANGING
		 *	depending on time difference, one of the above (except ALL)
		 *
		 *	AGO
		 *	CHANGING with "ago"
		 *
		 *	AGO_TRADITIONAL
		 *	like AGO but with time, i.e.	4 months ago, 13:37
		 */

		const MODE_SECONDS = 1;
		const MODE_MINUTES = 2;
		const MODE_HOURS = 3;
		const MODE_DAYS = 4;
		const MODE_WEEKS = 5;
		const MODE_MONTHS = 6;
		const MODE_YEARS = 7;
		const MODE_ALL = 42;
		const MODE_CHANGING = 43;
		const MODE_AGO_TRADITIONAL = 44;
		const MODE_AGO = 45;

		const SEC_YEAR = 31556926;
		const SEC_MONTH = 2592000;
		const SEC_WEEK = 604800;
		const SEC_DAY = 86400;
		const SEC_HOUR = 3600;
		const SEC_MINUTE = 60;

		const ARR_YEARS = 0;
		const ARR_MONTHS = 1;
		const ARR_WEEKS = 2;
		const ARR_DAYS = 3;
		const ARR_HOURS = 4;
		const ARR_MINUTES = 5;
		const ARR_SECONDS = 6;

		public static function getDifference($date1, $date2, $useExtensions = false) {
			$date1 = (int)$date1;
			$date2 = (int)$date2;

			if (!$useExtensions) {
				$date1 = strtotime(date("d.m.Y", $date1));
				$date2 = strtotime(date("d.m.Y", $date2));
			}

			$diff = ($date2 - $date1);
			if ($diff < 0) $diff *= -1;

			$years = floor($diff / self::SEC_YEAR);
			$months = floor($diff / self::SEC_MONTH);
			$weeks = floor($diff / self::SEC_WEEK);
			$days = floor($diff / self::SEC_DAY);
			$hours = floor($diff / self::SEC_HOUR);
			$minutes = floor($diff / self::SEC_MINUTE);

			return array($years, $months, $weeks, $days, $hours, $minutes, (int)$diff);
		}

		public static function getStringDifference($date1, $date2, $mode = self::MODE_DAYS, $useExtensions = false) {
			$dateInfos = (array) self::getDifference($date1, $date2, $useExtensions);

			list($years, $months, $weeks, $days, $hours, $mins, $secs) = $dateInfos;

			switch ($mode) {
				case self::MODE_SECONDS:
					if ($secs == 1) {
						return '1 second';
					} else {
						return sprintf('%d seconds', $secs);
					}

					break;

				case self::MODE_MINUTES:
					if ($mins == 1) {
						return '1 minute';
					} else {
						return sprintf('%d minutes', $mins);
					}

					break;

				case self::MODE_HOURS:
					if ($hours == 1) {
						return '1 hour';
					} else {
						return sprintf('%d hours', $hours);
					}

					break;

				case self::MODE_DAYS:
					if ($days == 1) {
						return '1 day';
					} else {
						return sprintf('%d days', $days);
					}

					break;

				case self::MODE_WEEKS:
					if ($weeks == 1) {
						return '1 week';
					} else {
						return sprintf('%d weeks', $weeks);
					}

					break;

				case self::MODE_MONTHS:
					if ($months == 1) {
						return '1 month';
					} else {
						return sprintf('%d months', $months);
					}

					break;

				case self::MODE_YEARS:
					if ($years == 1) {
						return '1 year';
					} else {
						return sprintf('%d years', $years);
					}

					break;

				case self::MODE_CHANGING:
					if ($years > 0) {
						return $years == 1 ? '1 year' : sprintf('%d years', $years);
					}

					if ($months > 0) {
						return $months == 1 ? '1 month' : sprintf('%d months', $months);
					}

					if ($weeks > 0) {
						return $weeks == 1 ? '1 week' : sprintf('%d weeks', $weeks);
					}

					if ($days > 0) {
						return $days == 1 ? '1 day' : sprintf('%d days', $days);
					}

					if ($hours > 0) {
						return $hours == 1 ? '1 hour' : sprintf('%d hours', $hours);
					}

					if ($mins > 0) {
						return $mins == 1 ? '1 minute' : sprintf('%d minutes', $mins);
					}

					return $secs == 1 ? '1 second' : sprintf('%d seconds', $secs);
					break;

				case self::MODE_AGO:
					if ($days == 0) {
						return 'Today';
					} else if ($days == 1) {
						return 'Yesterday';
					}

					return sprintf('%s ago', self::getStringDifference($date1, $date2, self::MODE_CHANGING, $useExtensions));
					break;

				case self::MODE_AGO_TRADITIONAL:
					if ($days == 0) {
						return sprintf('Today, %s', date('H:i', $date2));
					} else if ($days == 1) {
						return sprintf('Yesterday, %s', date('H:i', $date2));
					} else {
						return self::getStringDifference($date1, $date2, self::MODE_AGO, $useExtensions) . ', ' . date('H:i', $date2);
					}

					break;

				default:
					// Get ALL the differences! \o/
					$s = array();

					if ($years = intval((floor($secs / self::SEC_YEAR)))) {
						$s[] = $years . 'y';
						$secs %= self::SEC_YEAR;
					}

					if ($months = intval((floor($secs / self::SEC_MONTH)))) {
						$s[] = $months . 'm';
						$secs %= self::SEC_MONTH;
					}

					if ($days = intval((floor($secs / self::SEC_DAY)))) {
						$s[] = $days . 'd';
						$secs %= self::SEC_DAY;
					}

					return implode(' ', $s);
			}
		}
	}
?>