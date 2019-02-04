<?php
namespace Models;

use ApiExceptions\ApiException;
use External\HostAway\Country;
use External\HostAway\TimeZone;
use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Callback;
use Phalcon\Validation\Validator\Regex;
use Phalcon\Paginator\Factory;

class PhoneBook extends Model {

	const RECORDS_PER_PAGE = 10;

	/**
	 * @var int
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $first_name;

	/**
	 * @var string
	 */
	protected $last_name;

	/**
	 * @var string
	 */
	protected $number;

	/**
	 * @var string
	 */
	protected $country_code;

	/**
	 * @var string
	 */
	protected $timezone;

	/**
	 * @var string: datetime string in Y-m-d H:i:s format
	 */
	protected $inserted_on;

	/**
	 * @var string: datetime string in Y-m-d H:i:s format
	 */
	protected $updated_on;

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getFirstName(): string {
		return $this->first_name;
	}

	/**
	 * @param string $first_name
	 */
	public function setFirstName(string $first_name): void {
		$this->first_name = $first_name;
	}

	/**
	 * @return string
	 */
	public function getLastName(): string {
		return $this->last_name;
	}

	/**
	 * @param string $last_name
	 */
	public function setLastName(string $last_name): void {
		$this->last_name = $last_name;
	}

	/**
	 * @return string
	 */
	public function getNumber(): string {
		return $this->number;
	}

	/**
	 * @param string $number
	 */
	public function setNumber(string $number): void {
		$this->number = $number;
	}

	/**
	 * @return string
	 */
	public function getCountryCode(): string {
		return $this->country_code;
	}

	/**
	 * @param string $country_code
	 */
	public function setCountryCode(string $country_code): void {
		$this->country_code = $country_code;
	}

	/**
	 * @return string
	 */
	public function getTimezone(): string {
		return $this->timezone;
	}

	/**
	 * @param string $timezone
	 */
	public function setTimezone(string $timezone): void {
		$this->timezone = $timezone;
	}

	/**
	 * @return string: datetime string in Y-m-d H:i:s format
	 */
	public function getInsertedOn(): string {
		return $this->inserted_on;
	}

	/**
	 * @param string $inserted_on: datetime string in Y-m-d H:i:s format
	 */
	public function setInsertedOn(string $inserted_on): void {
		$this->inserted_on = $inserted_on;
	}

	/**
	 * @return string: datetime string in Y-m-d H:i:s format
	 */
	public function getUpdatedOn(): string {
		return $this->updated_on;
	}

	/**
	 * @param string $updated_on: datetime string in Y-m-d H:i:s format
	 */
	public function setUpdatedOn(string $updated_on): void {
		$this->updated_on = $updated_on;
	}

	/**
	 * initialize PhoneBook Model
	 */
	public function initialize() {
		$this->setSource("phonebook");
	}

	/**
	 * @return bool
	 */
	public function validation() {
		$validator = new Validation();

		$validator->add(
			'first_name',
			new PresenceOf(['message' => 'First Name is required'])
		);

		$validator->add(
			'number',
			new PresenceOf(['message' => 'The Phone Number is required'])
		);

		$validator->add(
			'number',
			new Regex(
				[
					'message' => 'The phone number is incorrect. Please use international format: +1 222 333-444-55-66',
					'pattern' => '/(?:(?:\(?(?:00|\+)([1-4]\d\d|[1-9]\d?)\)?)?[\-\.\ \\\\\/]?)?((?:\(?\d{1,}\)?[\-\.\ \\\\\/]?){0,})(?:[\-\.\ \\\\\/]?(?:#|ext\.?|extension|x)[\-\.\ \\\\\/]?(\d+))?/i'
				]
			)
		);

		$validator->add(
			'country_code',
			new PresenceOf(['message' => 'The Country Code is required'])
		);

		$validator->add(
			'country_code',
			new Callback(
				[
					'callback' => function(self $data) {
						$country = new Country();
						return ! empty($country->getNameByCode($data->getCountryCode()));
					},

					'message' => 'Wrong Country Code. Please fill Country Code in ISO 3166-1 standard like RU, US'
				]
			)
		);

		$validator->add(
			'timezone',
			new PresenceOf(['message' => 'The TimeZone is required'])
		);

		$validator->add(
			'timezone',
			new Callback(
				[
					'callback' => function(self $data) {
						$timeZone = new TimeZone();
						return ! empty($timeZone->getDetails($data->getTimezone()));
					},

					'message' => 'Wrong TimeZone. Please fill correct TimeZone in this format - America/Los_Angeles'
				]
			)
		);

		return $this->validate($validator);
	}

	/**
	 * Get phone book's record by ID
	 *
	 * @param int $id
	 *
	 * @return Model
	 * @throws ApiException
	 */
	public function getById($id) {
		$book = self::findFirst($id);

		if (! $book) {
			throw new ApiException("The record with given ID not found", 204);
		} else {
			return $book;
		}
	}

	/**
	 * Get all records from the phone book page by page
	 *
	 * @param Model\Query\BuilderInterface $builder
	 * @param int $page
	 * @param string|null $nameFilter - First Name or Last Name or part of these names
	 *
	 * @return array
	 * @throws ApiException
	 */
	public function getList(Model\Query\BuilderInterface $builder, $page, $nameFilter = null) {
		$builder = $builder
			->columns('id, first_name, last_name, number, country_code, timezone, inserted_on, updated_on')
			->from('Models\PhoneBook')
			->orderBy('first_name');

		if ($nameFilter) {
			$builder->where(
				"first_name LIKE :first_name: OR last_name LIKE :last_name:",
				[
					'first_name' => "%{$nameFilter}%",
					'last_name' => "%{$nameFilter}%"
				]
			);
		}

		$options = [
			'builder' => $builder,
			'limit'   => self::RECORDS_PER_PAGE,
			'page'    => $page,
			'adapter' => 'queryBuilder',
		];

		$paginator = Factory::load($options)->getPaginate();

		if ($page > $paginator->total_pages) {
			$options['page'] = $paginator->total_pages;
			$paginator = Factory::load($options)->getPaginate();
		}

		if ($paginator->total_items == 0)
			throw new ApiException("There aren't any records in the phone book", 204);

		$result = [
			'total_records' => $paginator->total_items,
			'total_pages' => $paginator->total_pages,
			'current_page' => $paginator->current,
			'next_page' => $paginator->next,
			'items' => []
		];

		foreach ($paginator->items as $item) {
			$result['items'][] = (array)$item;
		}

		return $result;
	}

	/**
	 * Create or Update phone book's record
	 *
	 * @param array $data
	 *
	 * @return int|null
	 * @throws ApiException
	 */
	public function store(array $data) {
		$result = $this->save($data);
		$id = null;

		if ($result === false) {
			$messages = $this->getMessages();
			$errorStr = '';

			foreach ($messages as $message) {
				$errorStr .= $message->getMessage() . "; ";
			}

			throw new ApiException(trim($errorStr), 204);

		} else {
			$id = $this->getId();
		}

		return $id;
	}
}