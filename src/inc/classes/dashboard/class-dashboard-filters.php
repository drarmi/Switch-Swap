<?php

namespace Omnis\src\inc\classes\dashboard;

use DateTime;

class Dashboard_Filters
{
	public const FILTER_MONTH = 'month';
	public const FILTER_QUARTER= 'quarter';
	public const FILTER_YEAR = 'year';
	public const FILTER_DATE_RANGE = 'date_range';

	public const ORDER_TYPE_SHOP = 'shop';
	public const ORDER_TYPE_AUCTION = 'auction';
	public const ORDER_TYPE_BOOKING = 'booking';

	public ?DateTime $dateStart;
	public ?DateTime $dateEnd;
	public ?DateTime $dateStartPrev;
	public ?DateTime $dateEndPrev;

	public function __construct(?DateTime $dateStart, ?DateTime $dateEnd, ?DateTime $dateStartPrev, ?DateTime $dateEndPrev)
	{
		$this->dateStart = $dateStart;
		$this->dateEnd = $dateEnd;
		$this->dateStartPrev = $dateStartPrev;
		$this->dateEndPrev = $dateEndPrev;
	}

}

