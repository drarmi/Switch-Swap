<?php

namespace Omnis\src\inc\classes\dashboard;

use DateTime;
use Exception;

class Dashboard
{
    private static ?Dashboard $instance = null;


	public function __construct()
	{
		$this->setupAjaxHandlers();
	}

	public static function getInstance(): ?Dashboard
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function addWpAjaxAction(string $action): void
	{
		add_action('wp_ajax_' . $action, [$this, $action]);
		add_action('wp_ajax_nopriv_' . $action, [$this, $action]);
	}

	private function setupAjaxHandlers(): void
	{
		$this->addWpAjaxAction('getSales');
		$this->addWpAjaxAction('getBookings');
		$this->addWpAjaxAction('getAuctions');
		$this->addWpAjaxAction('getRating');
		$this->addWpAjaxAction('getEarnings');
		$this->addWpAjaxAction('getTopBookedProducts');
		$this->addWpAjaxAction('getTopCustomers');
	}

	private function getDashboardVendor(): Dashboard_Vendor
	{
		return new Dashboard_Vendor();
	}

	/**
	 * @throws Exception
	 */
private function getFilters(): Dashboard_Filters
{
	$filterType = $_POST['filter_type'];
	$dateStart = $_POST['date_start'] ?? null;
	$dateEnd = $_POST['date_end'] ?? null;

	switch ($filterType) {
		case Dashboard_Filters::FILTER_MONTH:
			$from = new DateTime('first day of this month');
			$to = new DateTime('now');
			$prevFrom = new DateTime('first day of last month');
			$prevTo = new DateTime('last day of last month');
			break;

		case Dashboard_Filters::FILTER_QUARTER:
			$currentDate = new DateTime();
			$currentMonth = (int) $currentDate->format('n');
			$currentYear = (int) $currentDate->format('Y');

			$currentQuarter = ceil($currentMonth / 3);
			$startMonth = ($currentQuarter - 1) * 3 + 1;

			$from = new DateTime("$currentYear-$startMonth-01");
			$to = new DateTime('now');

			// Previous quarter
			$prevQuarter = $currentQuarter - 1;
			if ($prevQuarter < 1) {
				$prevQuarter = 4;
				$prevYear = $currentYear - 1;
			} else {
				$prevYear = $currentYear;
			}
			$prevStartMonth = ($prevQuarter - 1) * 3 + 1;
			$prevFrom = new DateTime("$prevYear-$prevStartMonth-01");
			$prevTo = (clone $prevFrom)->modify('+2 months')->modify('last day of this month');
			break;

		case Dashboard_Filters::FILTER_YEAR:
			$currentYear = (new DateTime())->format('Y');
			$from = new DateTime("$currentYear-01-01");
			$to = new DateTime('now');

			$prevYear = $currentYear - 1;
			$prevFrom = new DateTime("$prevYear-01-01");
			$prevTo = new DateTime("$prevYear-12-31");
			break;

		case Dashboard_Filters::FILTER_DATE_RANGE:
			$from = new DateTime($dateStart);
			$to = new DateTime($dateEnd);
			$prevFrom = null;
			$prevTo = null;
			break;

		default:
			$from = null;
			$to = null;
			$prevFrom = null;
			$prevTo = null;
			break;
	}

	return new Dashboard_Filters($from, $to, $prevFrom, $prevTo);
}

	/**
	 * @throws Exception
	 */
	public function getSales(): void
    {
	    $result = $this->getDashboardVendor()->getOrders($this->getFilters(), Dashboard_Filters::ORDER_TYPE_SHOP);
		if (!empty($result)) {
			wp_send_json_success( $result );
		}
		wp_send_json_error('Error');
    }

	/**
	 * @throws Exception
	 */
	public function getBookings(): void
	{
		$result = $this->getDashboardVendor()->getOrders($this->getFilters(), Dashboard_Filters::ORDER_TYPE_BOOKING);
		if (!empty($result)) {
			wp_send_json_success( $result );
		}
		wp_send_json_error('Error');
	}

	/**
	 * @throws Exception
	 */
	public function getAuctions(): void
	{
		$result = $this->getDashboardVendor()->getOrders($this->getFilters(), Dashboard_Filters::ORDER_TYPE_AUCTION);
		if (!empty($result)) {
			wp_send_json_success( $result );
		}
		wp_send_json_error('Error');
	}

	/**
	 * @throws Exception
	 */
	public function getRating(): void
	{
		$result = $this->getDashboardVendor()->getRating($this->getFilters());
		if (!empty($result)) {
			wp_send_json_success( $result );
		}
		wp_send_json_error('Error');
	}

	/**
	 * @throws Exception
	 */
	public function getEarnings(): void
	{
		$result = $this->getDashboardVendor()->getEarnings($this->getFilters());
		if (!empty($result)) {
			wp_send_json_success( $result );
		}
		wp_send_json_error('Error');
	}

	/**
	 * @throws Exception
	 */
	public function getTopBookedProducts(): void
	{
		$result = $this->getDashboardVendor()->getTopBookedProducts($this->getFilters());
		if (!empty($result)) {
			wp_send_json_success( $result );
		}
		wp_send_json_error('Error');
	}

	/**
	 * @throws Exception
	 */
	public function getTopCustomers(): void
	{
		$result = $this->getDashboardVendor()->getTopCustomers($this->getFilters());
		if (!empty($result)) {
			wp_send_json_success( $result );
		}
		wp_send_json_error('Error');
	}

}

// Initialize the Dashboard instance
Dashboard::getInstance();