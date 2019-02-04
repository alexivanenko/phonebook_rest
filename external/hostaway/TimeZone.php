<?php
namespace External\HostAway;

use ApiExceptions\ApiException;

class TimeZone extends HostAwayApi {

	/**
	 * Country constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->url .= '/timezones';
	}

	/**
	 * Get TimeZone details
	 *
	 * @param string $zone
	 *
	 * @return mixed|null
	 * @throws ApiException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getDetails($zone) {
		try {
			$list = $this->request();
		} catch (\Exception $e) {
			throw new ApiException($e->getMessage(), $e->getCode());
		}

		$result = null;

		if (isset($list[$zone]))
			$result = $list[$zone];

		return $result;
	}
}