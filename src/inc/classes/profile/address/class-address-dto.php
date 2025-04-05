<?php

namespace Omnis\src\inc\classes\profile\address;

class Address_DTO
{
	public ?string $address_id;
	public ?int $zip;
	public ?string $city;
	public ?string $street;
	public ?string $entrance;
	public ?int $floor;
	public ?int $apartment;
	public ?string $phone;
	public ?string $comment;
	public ?bool $isDefault;

	public function __construct(
		?string $address_id,
		?int $zip,
		?string $city,
		?string $street,
		?string $entrance,
		?int $floor,
		?int $apartment,
		?string $phone,
		?string $comment,
		?bool $isDefault
	) {
		$this->address_id = $address_id;
		$this->zip = $zip;
		$this->city = $city;
		$this->street = $street;
		$this->entrance = $entrance;
		$this->floor = $floor;
		$this->apartment = $apartment;
		$this->phone = $phone;
		$this->comment = $comment;
		$this->isDefault = $isDefault;
	}

	public function toArrayResponse(): array
	{
		return [
			'address_id' => $this->address_id,
			'zip' => $this->zip,
			'city' => $this->city,
			'street' => $this->street,
			'entrance' => $this->entrance,
			'floor' => $this->floor,
			'apartment' => $this->apartment,
			'phone' => $this->phone,
			'comment' => $this->comment,
			'isDefault' => $this->isDefault,
		];
	}

	public function toArrayDatabase(): array
	{
		return [
			'zip' => $this->zip,
			'city' => $this->city,
			'street' => $this->street,
			'entrance' => $this->entrance,
			'floor' => $this->floor,
			'apartment' => $this->apartment,
			'phone' => $this->phone,
			'comment' => $this->comment,
			'isDefault' => $this->isDefault,
		];
	}

}