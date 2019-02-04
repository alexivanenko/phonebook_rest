<?php
namespace Db;

use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Symfony\Component\Yaml\Yaml;

class DbConnection {

	/**
	 * @var self
	 */
	private static $instance;

	/**
	 * @var FactoryDefault
	 */
	private $connect;

	/**
	 * @return FactoryDefault
	 */
	public function getConnect() {
		return $this->connect;
	}

	/**
	 * DbConnection constructor.
	 */
	private function __construct() {
		$this->connect();
	}

	/**
	 * @return FactoryDefault
	 */
	public static function get() {
		$self = new self();

		return $self->getConnect();
	}

	/**
	 * @return DbConnection
	 */
	public static function getInstance() {
		if (!self::$instance instanceof self)
			self::$instance = new self();

		return self::$instance;
	}

	/**
	 * Set up DB service
	 */
	private function connect() {
		$factory = new FactoryDefault();

		//TODO: move to separate Config class
		$config = Yaml::parseFile('phinx.yml');
		$dbConfig = $config['environments']['production'];

		$factory->set(
			'db',
			function () use ($dbConfig) {
				return new PdoMysql(
					[
						'host'     => $dbConfig['host'],
						'username' => $dbConfig['user'],
						'password' => $dbConfig['pass'],
						'dbname'   => $dbConfig['name'],
					]
				);
			}
		);

		$this->connect = $factory;
	}
}
