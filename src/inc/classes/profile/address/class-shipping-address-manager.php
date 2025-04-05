<?php

namespace Omnis\src\inc\classes\profile\address;

use JsonException;

class Shipping_Address_Manager
{
	public const SHIPPING_ADDRESS_PREFIX = 'shipping_custom_address_';
	private static ?Shipping_Address_Manager $instance = null;

	public static function getInstance(): ?Shipping_Address_Manager
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct()
	{
		$this->setupAjaxHandlers();
	}

	private function addWpAjaxAction(string $action): void
	{
		add_action('wp_ajax_' . $action, [$this, $action]);
		add_action('wp_ajax_nopriv_' . $action, [$this, $action]);
	}

	private function setupAjaxHandlers(): void
	{
		$this->addWpAjaxAction('getAllAddresses');
		$this->addWpAjaxAction('getAddress');
		$this->addWpAjaxAction('addAddress');
		$this->addWpAjaxAction('editAddress');
		$this->addWpAjaxAction('deleteAddress');
	}

	/**
	 * @throws JsonException
	 */
	public function getAllAddresses(): void
	{
		$nonce = sanitize_text_field($_POST['security']);

		if (!wp_verify_nonce($nonce, "registration_nonce")) {
			wp_send_json_error(["message" => esc_html__("Invalid request ", 'swap')]);
		}

		global $wpdb;

		$resultAddresses = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT um.meta_key, um.meta_value 
				FROM {$wpdb->usermeta} um 
				WHERE um.user_id = %d 
			  	AND um.meta_key LIKE %s
			  	ORDER BY um.umeta_id",
				get_current_user_id(),
				self::SHIPPING_ADDRESS_PREFIX . '%'
			)
		);

		$addresses = [];

		if ($resultAddresses) {
			foreach ($resultAddresses as $addressJson) {
				$addressArray = json_decode( $addressJson->meta_value, true, 512, JSON_THROW_ON_ERROR );
				$addressDto = new Address_DTO(
					$addressJson->meta_key,
					(int)$addressArray['zip'],
					$addressArray['city'],
					$addressArray['street'],
					$addressArray['entrance'],
					(int)$addressArray['floor'],
					(int)$addressArray['apartment'],
					$addressArray['phone'],
					$addressArray['comment'],
					$addressArray['isDefault']
				);
				$addresses[] = $addressDto->toArrayResponse();
			}
		}

		wp_send_json_success( $addresses );
	}

	public function getAddress(): void
	{
		$nonce = sanitize_text_field($_POST['security']);

		if (!wp_verify_nonce($nonce, "registration_nonce")) {
			wp_send_json_error(["message" => esc_html__("Invalid request ", 'swap')]);
		}

		$addressResult = get_user_meta(get_current_user_id(), sanitize_text_field($_POST['address_id']), true);

		if ($addressResult) {
			$addressArray = json_decode( $addressResult, true, 512, JSON_THROW_ON_ERROR );
			$addressDto = new Address_DTO(
				sanitize_text_field($_POST['address_id']),
				(int)$addressArray['zip'],
				$addressArray['city'],
				$addressArray['street'],
				$addressArray['entrance'],
				(int)$addressArray['floor'],
				(int)$addressArray['apartment'],
				$addressArray['phone'],
				$addressArray['comment'],
				$addressArray['isDefault']
			);
			wp_send_json_success( $addressDto->toArrayResponse() );
		} else {
			wp_send_json_error(["message" => esc_html__("Error getting address ", 'swap')]);
		}

	}

	/**
	 * @throws JsonException
	 */
	public function addAddress(): void
	{
		$nonce = sanitize_text_field($_POST['security']);

		if (!wp_verify_nonce($nonce, "registration_nonce")) {
			wp_send_json_error(["message" => esc_html__("Invalid request ", 'swap')]);
		}

		$address = new Address_DTO(
			null,
			(int)sanitize_text_field($_POST['zip']),
			sanitize_text_field($_POST['city']),
			sanitize_text_field($_POST['street']),
			sanitize_text_field($_POST['entrance']),
			(int)sanitize_text_field($_POST['floor']),
			(int)sanitize_text_field($_POST['apartment']),
			sanitize_text_field($_POST['phone']),
			sanitize_text_field($_POST['comment']),
			sanitize_text_field($_POST['isDefault']) === 'true'
		);

		global $wpdb;

		$latestAddress = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT um.meta_key 
				FROM {$wpdb->usermeta} um 
				WHERE um.user_id = %d 
			  	AND um.meta_key LIKE %s
			  	ORDER BY um.umeta_id DESC",
				get_current_user_id(),
				self::SHIPPING_ADDRESS_PREFIX . '%'
			)
		);

		if ($latestAddress) {
			preg_match('/(\d+)$/', $latestAddress->meta_key, $matches);
			$newId = isset($matches[1]) ? (int)$matches[1] + 1 : 1;
		} else {
			$newId = 1;
		}

		$address->address_id = self::SHIPPING_ADDRESS_PREFIX . $newId;

		if( add_user_meta(get_current_user_id(), $address->address_id, json_encode($address->toArrayDatabase(), JSON_THROW_ON_ERROR)) ) {
			wp_send_json_success( $address->toArrayResponse() );
		} else {
			wp_send_json_error(["message" => esc_html__("Error adding address ", 'swap')]);
		}

	}

	/**
	 * @throws JsonException
	 */
	public function editAddress(): void
	{
		$nonce = sanitize_text_field($_POST['security']);

		if (!wp_verify_nonce($nonce, "registration_nonce")) {
			wp_send_json_error(["message" => esc_html__("Invalid request ", 'swap')]);
		}

		$address = new Address_DTO(
			sanitize_text_field($_POST['address_id']),
			(int)sanitize_text_field($_POST['zip']),
			sanitize_text_field($_POST['city']),
			sanitize_text_field($_POST['street']),
			sanitize_text_field($_POST['entrance']),
			(int)sanitize_text_field($_POST['floor']),
			(int)sanitize_text_field($_POST['apartment']),
			sanitize_text_field($_POST['phone']),
			sanitize_text_field($_POST['comment']),
			sanitize_text_field($_POST['isDefault']) === 'true'
		);

		if( update_user_meta(get_current_user_id(), $address->address_id, json_encode($address->toArrayDatabase(), JSON_THROW_ON_ERROR)) ) {
			wp_send_json_success( $address->toArrayResponse() );
		} else {
			wp_send_json_error(["message" => esc_html__("Error editing address ", 'swap')]);
		}

	}

	public function deleteAddress(): void
	{
		$nonce = sanitize_text_field($_POST['security']);

		if (!wp_verify_nonce($nonce, "registration_nonce")) {
			wp_send_json_error(["message" => esc_html__("Invalid request ", 'swap')]);
		}

		if ( delete_user_meta(get_current_user_id(), sanitize_text_field($_POST['address_id'])) ) {
			wp_send_json_success();
		} else {
			wp_send_json_error(["message" => esc_html__("Error deleting address ", 'swap')]);
		}

	}

}

// Initialize the Dashboard instance
Shipping_Address_Manager::getInstance();