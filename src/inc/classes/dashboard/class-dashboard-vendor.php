<?php

namespace Omnis\src\inc\classes\dashboard;

use WeDevs\Dokan\Vendor\Vendor;

class Dashboard_Vendor {
	private ?Vendor $dokanVendor = null;

	public function __construct()
	{
		$this->initVendor();
	}

	private function initVendor(): void
	{
		$vendorId = function_exists("get_current_user_id") ? get_current_user_id() : 0;

		if ($vendorId && function_exists("dokan")) {
			$this->dokanVendor = dokan()->vendor->get($vendorId);
		}
	}

	private function getProductsTypeFilter(string $orderType): string
	{
		switch ($orderType) {
			case Dashboard_Filters::ORDER_TYPE_SHOP:
				$orderTypeSql = "AND p.post_type = 'product'";
				break;
			case Dashboard_Filters::ORDER_TYPE_AUCTION:
				$orderTypeSql = "AND p.post_type = 'auction'";
				break;
			case Dashboard_Filters::ORDER_TYPE_BOOKING:
				$orderTypeSql = "AND p.post_type = 'wc_booking'";
				break;
			default:
				$orderTypeSql = "";
				break;
		}
		return $orderTypeSql;
	}

	public function getOrders(Dashboard_Filters $filters, string $orderType): array
	{
		global $wpdb;

		$productTypeSql = $this->getProductsTypeFilter($orderType);

		$result_total = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT SUM(od.order_total) AS earnings_total, COUNT(od.order_id) AS orders_count_total 
				FROM {$wpdb->prefix}dokan_orders od 
				INNER JOIN {$wpdb->prefix}wc_orders o ON od.order_id = o.id 
				INNER JOIN {$wpdb->prefix}wc_order_product_lookup pl ON pl.order_id = o.id
				INNER JOIN {$wpdb->prefix}posts p ON pl.product_id = p.ID
                WHERE od.seller_id = %d 
                $productTypeSql 
                AND DATE(o.date_created_gmt) BETWEEN %s AND %s ",
				$this->dokanVendor->get_id(),
				$filters->dateStart->format('Y-m-d'),
				$filters->dateEnd->format('Y-m-d')
			)
		);

		$result_orders = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT SUM(od.order_total) AS earnings, COUNT(od.order_id) AS orders_count, DATE(o.date_created_gmt) AS date 
				FROM {$wpdb->prefix}dokan_orders od 
				INNER JOIN {$wpdb->prefix}wc_orders o ON od.order_id = o.id 
				INNER JOIN {$wpdb->prefix}wc_order_product_lookup pl ON pl.order_id = o.id
				INNER JOIN {$wpdb->prefix}posts p ON pl.product_id = p.ID
                WHERE od.seller_id = %d 
                $productTypeSql 
                AND DATE(o.date_created_gmt) BETWEEN %s AND %s 
              	GROUP BY DATE(o.date_created_gmt) 
              	ORDER BY DATE(o.date_created_gmt)",
				$this->dokanVendor->get_id(),
				$filters->dateStart->format('Y-m-d'),
				$filters->dateEnd->format('Y-m-d')
			)
		);

		$orders_data = [];
		if (isset($result_orders)) {
			foreach ( $result_orders as $result ) {
				$orders_data[ $result->date ] = [

					'earnings'     => (float) $result->earnings,
					'orders_count' => (int) $result->orders_count
				];
			}
		}


		return [
			'earnings_total' => isset($result_total) ? (float) $result_total->earnings_total : 0,
			'orders_count_total'  => isset($result_total) ? (int) $result_total->orders_count_total : 0,
			'orders_data' => $orders_data ?? []
		];
	}

	public function getRating(Dashboard_Filters $filters): array
	{
		global $wpdb;

		$result_average = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT AVG(cm.meta_value) AS average 
				FROM $wpdb->posts p 
	            INNER JOIN $wpdb->comments wc ON p.ID = wc.comment_post_ID 
	            LEFT JOIN $wpdb->commentmeta cm ON cm.comment_id = wc.comment_ID 
	            WHERE p.post_author = %d AND p.post_type = 'product' AND p.post_status = 'publish' 
	            AND ( cm.meta_key = 'rating' OR cm.meta_key IS NULL) AND wc.comment_approved = 1 
	            AND DATE(wc.comment_date) BETWEEN %s AND %s 
	            ORDER BY wc.comment_post_ID",
				$this->dokanVendor->get_id(),
				$filters->dateStart->format('Y-m-d'),
				$filters->dateEnd->format('Y-m-d')
			)
		);

		$result_ratings = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT COUNT(cm.meta_id) AS count, cm.meta_value AS rating 
				FROM $wpdb->posts p 
				INNER JOIN $wpdb->comments wc ON p.ID = wc.comment_post_ID 
				LEFT JOIN $wpdb->commentmeta cm ON cm.comment_id = wc.comment_ID 
				WHERE p.post_author = %d AND p.post_type = 'product' AND p.post_status = 'publish' 
				AND ( cm.meta_key = 'rating' OR cm.meta_key IS NULL) AND wc.comment_approved = 1 
				AND DATE(wc.comment_date) BETWEEN %s AND %s 
				GROUP BY cm.meta_value 
				ORDER BY cm.meta_value",
				$this->dokanVendor->get_id(),
				$filters->dateStart->format('Y-m-d'),
				$filters->dateEnd->format('Y-m-d')
			)
		);

		$ratings_data = [];
		if (isset($result_ratings)) {
			foreach ( $result_ratings as $result ) {
				$ratings_data[ $result->rating ] = (int) $result->count;
			}
		}

		return [
			'average_rating' => isset($result_average) ? (float) $result_average : 0,
			'ratings_data' => $ratings_data ?? []
		];
	}


	public function getEarnings(Dashboard_Filters $filters): array
	{
		global $wpdb;

		$status = dokan_withdraw_get_active_order_status_in_comma();
		$debit_balance = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT SUM(debit) AS earnings 
                    FROM {$wpdb->prefix}dokan_vendor_balance 
                    WHERE vendor_id = %d 
                  	AND DATE(balance_date) BETWEEN %s AND %s 
                    AND status IN ($status) 
                    AND trn_type = 'dokan_orders'",
					$this->dokanVendor->get_id(),
					$filters->dateStart->format('Y-m-d'),
					$filters->dateEnd->format('Y-m-d')
			)
		);

		$credit_balance = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT SUM(credit) AS earnings 
                    FROM {$wpdb->prefix}dokan_vendor_balance 
                    WHERE vendor_id = %d 
                    AND DATE(balance_date) BETWEEN %s AND %s 
                    AND trn_type = %s 
                    AND status = %s",
				$this->dokanVendor->get_id(),
				$filters->dateStart->format('Y-m-d'),
				$filters->dateEnd->format('Y-m-d'),
				'dokan_refund',
				'approved'
			)
		);

		$currentBalance = (float) ( $debit_balance - $credit_balance );
		$prevBalance = null;

		if ($filters->dateStartPrev !== null) {
			$debit_balance_prev = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT SUM(debit) AS earnings 
                    FROM {$wpdb->prefix}dokan_vendor_balance 
                    WHERE vendor_id = %d 
                  	AND DATE(balance_date) BETWEEN %s AND %s 
                    AND status IN ($status) 
                    AND trn_type = 'dokan_orders'",
					$this->dokanVendor->get_id(),
					$filters->dateStartPrev->format( 'Y-m-d' ),
					$filters->dateEndPrev->format( 'Y-m-d' )
				)
			);

			$credit_balance_prev = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT SUM(credit) AS earnings 
                    FROM {$wpdb->prefix}dokan_vendor_balance 
                    WHERE vendor_id = %d 
                    AND DATE(balance_date) BETWEEN %s AND %s 
                    AND trn_type = %s 
                    AND status = %s",
					$this->dokanVendor->get_id(),
					$filters->dateStartPrev->format( 'Y-m-d' ),
					$filters->dateEndPrev->format( 'Y-m-d' ),
					'dokan_refund',
					'approved'
				)
			);

			$prevBalance = $debit_balance_prev ? (float) ( $debit_balance_prev - $credit_balance_prev ) : null;
		}

		return [
			"current_balance" => $currentBalance,
			"prev_balance" => $prevBalance ?? null
		];
	}

	public function getTopBookedProducts(Dashboard_Filters $filters): array
	{
		global $wpdb;
	
		if (!$filters->dateStart instanceof \DateTime || !$filters->dateEnd instanceof \DateTime) {
			return [];
		}
	
		$productTypeSql = $this->getProductsTypeFilter(Dashboard_Filters::ORDER_TYPE_BOOKING);
		$result_products = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT COUNT(pl.product_id) AS product_count, p.post_name AS product_name, pm.meta_value AS product_image 
				FROM {$wpdb->prefix}dokan_orders od 
				INNER JOIN {$wpdb->prefix}wc_orders o ON od.order_id = o.id 
				INNER JOIN {$wpdb->prefix}wc_order_product_lookup pl ON pl.order_id = o.id 
				INNER JOIN {$wpdb->prefix}posts p ON pl.product_id = p.ID 
				INNER JOIN {$wpdb->prefix}postmeta pm ON p.ID = pm.post_id 
				WHERE od.seller_id = %d 
				AND DATE(o.date_created_gmt) BETWEEN %s AND %s 
				$productTypeSql 
				AND pm.meta_key = '_thumbnail_id' 
				GROUP BY pl.product_id 
				ORDER BY product_count DESC 
				LIMIT 5",
				$this->dokanVendor->get_id(),
				$filters->dateStart->format('Y-m-d'),
				$filters->dateEnd->format('Y-m-d')
			)
		);
	
		$products = [];
		if (!empty($result_products)) {
			foreach ($result_products as $product) {
				$products[] = [
					'product_name'         => $product->product_name,
					'product_image'        => $product->product_image,
					'product_rent_date'    => '',
					'product_auction_date' => '',
					'product_auction_price'=> 0,
					'product_count'        => (int) $product->product_count
				];
			}
		}
	
		return $products;
	}

	public function getTopCustomers(Dashboard_Filters $filters):array
	{
		global $wpdb;

		$result_customers = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT COUNT(od.order_id) AS orders_count, DATE(o.date_created_gmt) AS date, u.user_nicename AS customer_name, um.meta_value AS profile_picture
				FROM {$wpdb->prefix}dokan_orders od 
				INNER JOIN {$wpdb->prefix}wc_orders o ON od.order_id = o.id 
				INNER JOIN {$wpdb->prefix}users u ON o.customer_id = u.ID
				INNER JOIN {$wpdb->prefix}usermeta um ON u.ID = um.user_id
                WHERE od.seller_id = %d 
                AND DATE(o.date_created_gmt) BETWEEN %s AND %s 
              	AND od.order_status = 'wc-completed' 
                AND um.meta_key = 'profile_picture'
              	GROUP BY u.user_nicename, DATE(o.date_created_gmt)
              	ORDER BY DATE(o.date_created_gmt)
              	LIMIT 5",
				$this->dokanVendor->get_id(),
				$filters->dateStart->format('Y-m-d'),
				$filters->dateEnd->format('Y-m-d')
			)
		);

		$customers = [];
		if (isset($result_customers)& count($result_customers) === 5) {
			foreach ( $result_customers as $customer ) {
				$customers[] = [
					'customer_name'   => $customer->customer_name,
					'profile_picture' => $customer->profile_picture,
					'orders_count'    => (int) $customer->orders_count,
					'last_order_date' => $customer->date
				];
			}
		}

		return $customers;
	}

}