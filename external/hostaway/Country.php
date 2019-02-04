<?php
namespace External\HostAway;

use ApiExceptions\ApiException;

class Country extends HostAwayApi {

	/**
	 * Country constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->url .= '/countries';
	}

	/**
	 * Get country name by country code in ISO 3166-1 standard
	 *
	 * @param string $code
	 *
	 * @return mixed|null
	 * @throws ApiException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getNameByCode($code) {
		try {
			$list = $this->request();
		} catch (\Exception $e) {
			throw new ApiException($e->getMessage(), $e->getCode());
		}

		$result = null;

		if (isset($list[$code]))
			$result = $list[$code];

		return $result;
	}
}