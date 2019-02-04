<?php
define("API_URL", 'http://phonebook.local');

//Create
$insertData = [
	'first_name' => 'Ivan',
	'last_name' => 'Ivanov',
	'number' => '+7 123 123 75 75',
	'country_code' => 'RU',
	'timezone' => 'Europe/Moscow'
];

$insertResult = call($insertData);

//Update
$updateData = [
	'first_name' => 'Petr',
	'last_name' => 'Petrov1',
	'number' => '+1 222 333-444-55-66',
	'country_code' => 'AF',
	'timezone' => 'America/Los_Angeles'
];

$updateResult = call($updateData, 'PUT', $insertResult->data->id);

//Delete
$deleteResult = call([], 'DELETE', $updateResult->data->id);

echo '<pre>';
var_dump($deleteResult);

function call($postData, $method = "POST", $id = null) {
	$url = API_URL . "/api/items";

	if ($id)
		$url .= "/{$id}";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);

	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

	if ($method != 'POST')
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);

	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);

	$response = curl_exec($ch);
	curl_close($ch);

	$decodedResponse = json_decode($response);

	if ($decodedResponse) {
		return $decodedResponse;
	} else {
		return null;
	}
}