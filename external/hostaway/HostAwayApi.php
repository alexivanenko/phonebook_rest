<?php

namespace External\HostAway;
use ApiExceptions\HttpRequestException;
use \GuzzleHttp\Client;

abstract class HostAwayApi {

	const STATUS_CODE_OK = 200;

	/**
	 * @var Client
	 */
	private $client;

	/**
	 * @var string
	 */
	protected $url;

	/**
	 * HostAwayApi constructor.
	 */
	public function __construct() {
		$this->client = new Client();
		$this->url = 'https://api.hostaway.com';
	}

	/**
	 * Run HostAway GET request
	 *
	 * @return array
	 * @throws HttpRequestException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	protected function request() {
		$response = $this->client->request('GET', $this->url);

		if ($response->getStatusCode() != self::STATUS_CODE_OK)
			throw new HttpRequestException("HTTP Error: server error", $response->getStatusCode());

		$data = json_decode($response->getBody()->getContents());

		if (! $data)
			throw new HttpRequestException("HTTP Error: bad response", 404);

		if ($data->status != 'success')
			throw new HttpRequestException("HTTP Error: wrong response status {$data->status}", 404);

		return (array)$data->result;
	}
}