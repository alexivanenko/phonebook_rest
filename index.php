<?php
require_once('vendor/autoload.php');

use Phalcon\Mvc\Micro;
use Phalcon\Loader;

$loader = new Loader();

$loader->registerNamespaces(
	[
		'Db' => __DIR__ . '/db/',
		'Models' => __DIR__ . '/models/',
		'ApiExceptions' => __DIR__ . '/exceptions/',
		'External\HostAway' => __DIR__ . '/external/hostaway',
	]
)->register();

use Db\DbConnection;
use Models\PhoneBook;

$connection = DbConnection::get();
$app = new Micro($connection);

//All records
$app->get(
	'/api/items',
	function () use ($app) {
		$builder = $app->modelsManager->createBuilder();
		$page = (int)$_GET['page'];

		if (! $page)
			$page = 1;

		$book = new PhoneBook();
		response($app, $book->getList($builder, $page));
	}
);

//Search records by First/Last Name
$app->get(
	'/api/items/search/{name}',
	function ($name) use ($app) {
		$builder = $app->modelsManager->createBuilder();
		$page = (int)$_GET['page'];

		if (! $page)
			$page = 1;

		$book = new PhoneBook();
		response($app, $book->getList($builder, $page, $name));
	}
);

//Get the record by ID
$app->get(
	'/api/items/{id:[0-9]+}',
	function ($id) use ($app) {
		$book = new PhoneBook();
		response($app, $book->getById($id)->toArray());
	}
);

//Create new record
$app->post(
	'/api/items',
	function () use($app) {
		$data = (array)$app->request->getJsonRawBody();
		$now = new DateTime();
		$data['inserted_on'] = $now->format('Y-m-d H:i:s');

		$book = new PhoneBook();
		$id = $book->store($data);

		response($app, ['id' => $id]);
	}
);

//Update existing record
$app->put(
	'/api/items/{id:[0-9]+}',
	function ($id) use($app) {
		$data = (array)$app->request->getJsonRawBody();
		$now = new DateTime();
		$data['updated_on'] = $now->format('Y-m-d H:i:s');

		$book = new PhoneBook();
		$book = $book->getById($id);
		/** @var PhoneBook $book */
		$id = $book->store($data);

		response($app, ['id' => $id]);
	}
);

//Delete record
$app->delete(
	'/api/items/{id:[0-9]+}',
	function ($id) use($app) {
		$book = new PhoneBook();
		$book = $book->getById($id);
		$book->delete();

		response($app, ['id' => $id, 'message' => 'Record was deleted successfully']);
	}
);

//If the path not registered in the routes collection
$app->notFound(function () use ($app) {
	throw new \ApiExceptions\ApiException('The Page NotFound', 404);
});

//Exceptions handler
$app->error(function (\Exception $exception) use ($app) {
	$app->response->setJsonContent(
		[
			'code'    => $exception->getCode(),
			'status'  => 'ERROR',
			'message' => $exception->getMessage(),
		]
	);

	$app->response->send();

	return false;
});

$app->handle();

/**
 * Send successful response in JSON format
 *
 * @param Micro $app
 * @param array $content
 */
function response(Micro $app, array $content) {
	$app->response->setJsonContent(
		[
			'code'    => 202,
			'status'  => 'SUCCESS',
			'data' => $content,
		]
	);

	$app->response->send();
}
