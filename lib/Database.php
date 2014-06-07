<?php 
	class Database {
		private $db;
		public $lastQuery;

		const TABLE_ITEMS = 'items';
		const TABLE_CONFIG = 'config';

		private function __construct(PDO $db) {
			$this->db = $db;
		}

		public static function MySQL($host, $username, $password, $database) {
			$db = new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', $username, $password);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

			return new self($db);
		}

		public static function SQLite($file) {
			$db = new PDO('sqlite:' . $file);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

			// CREATE TABLES
			$db->query("CREATE TABLE IF NOT EXISTS ".self::TABLE_ITEMS." (id INTEGER NOT NULL PRIMARY KEY UNIQUE, href TEXT, name TEXT, isPrivate INTEGER, isSubscribed INTEGER, isTrashed INTEGER, url TEXT, content_url TEXT, type TEXT, views INTEGER, remote_url TEXT, redirect_url TEXT, thumbnail_url TEXT, source TEXT, created_at TEXT, updated_at TEXT, deleted_at TEXT)");
			$db->query("CREATE TABLE IF NOT EXISTS ".self::TABLE_CONFIG." (key TEXT, value TEXT)");

			return new self($db);
		}

		protected function error($msg, $sql, $code) {
			exit('
				<b>SQL Error (' . $code . ')</b><br /><br />

				' . $msg . '<br />
				<code>' . $sql . '</code>
			');
		}

		public function setConfig($key, $value) {
			$res = $this->query("SELECT value FROM ".self::TABLE_CONFIG." WHERE key = ?", array($key));
			$row = $this->fetchObject($res);

			if ($row) {
				$this->query("
					UPDATE ".self::TABLE_CONFIG."
					SET value = :v
					WHERE key = :K
				", array(
					$value,
					$key
				));
			} else {
				$this->query("
					INSERT INTO ".self::TABLE_CONFIG."
					(key, value)
					VALUES
					(:k, :v)
				", array(
					$key,
					$value
				));
			}
		}

		public function getConfig($key) {
			$res = $this->query("SELECT value FROM ".self::TABLE_CONFIG." WHERE key = ?", array($key));
			$row = $this->fetchObject($res);

			if ($row) {
				return $row->value;
			}

			return null;
		}

		public function reset() {
			$this->query("DELETE FROM " . self::TABLE_CONFIG . " WHERE 1");
			$this->query("DELETE FROM " . self::TABLE_ITEMS . " WHERE 1");
		}

		/**
		 * 	Send a query.
		 *  This method supports prepared statements. Just write ? or use placeholders, like ':id' in your
		 *  $sql and provide $args with its values.
		 * 
		 *  @param string $sql Query to send.
		 *  @param array $args Params to bind, if preparing.
		 *  
		 *  @return PDOStatement PDO Statement
		 */
		public function query($sql, array $args = null) {
			if (preg_match('^SELECT COUNT\(([a-zA-Z0-9*]+)\) FROM^', $sql, $matches)) {
				$sql = "SELECT ".$matches[1]." FROM" . preg_replace('^SELECT COUNT\(([a-zA-Z0-9*]+)\) FROM^', '', $sql);
			}

			try {
				if (!is_null($args)) {
					$this->lastQuery = $this->db->prepare($sql);
					$this->lastQuery->execute($args);
				} else {
					$this->lastQuery = $this->db->query($sql);
				}

				return $this->lastQuery;
			} catch (PDOException $e) {
				$this->error($e->getMessage(), $sql, $e->getCode());
			}
		}

		/**
		 * 	Prepare a statement and return it without executing.
		 * 
		 *  @param string $query Query to send.
		 */
		public function prepare($sql) {
			try {
				return $this->db->prepare($sql);
			} catch (PDOException $e) {
				$this->error($e->getMessage(), $sql, $e->getCode());
			}
		}

		/**
		 * Fetch statement's results as object.
		 * 
		 * @param PDOStatement $stmt
		 */
		public function fetchObject(PDOStatement $stmt = null) {
			if (!is_null($stmt)) {
				return $stmt->fetch(PDO::FETCH_OBJ);
			} else {
				return $this->lastQuery->fetch(PDO::FETCH_OBJ);
			}
		}

		/**
		 * @deprecated Use fetchObject() instead.
		 */
		public function fetch_object($stmt) {
			return $this->fetchObject($stmt);
		}

		/**
		 * @deprecated Use fetchObject() instead.
		 */
		public function fetch_array($res) {
			if (!is_null($stmt)) {
				return $stmt->fetch(PDO::FETCH_ASSOC);
			} else {
				return $this->lastQuery->fetch(PDO::FETCH_ASSOC);
			}
		}

		/**
		 * Get the number of rows affected.
		 * In SELECT-statements, this will be the number of selected rows.
		 * 
		 * @param PDOStatement PDO Statement $stmt = null
		 * 
		 * @return int
		 */
		public function numRows(PDOStatement $stmt = null) {
			if (!is_null($stmt)) {
				return $stmt->rowCount();
			} else {
				return $this->lastQuery->rowCount();
			}
		}

		/**
		 * @deprecated Use numRows instead.
		 */
		public function num_rows($stmt) {
			return $this->numRows($stmt);
		}

		/**
		 * Get last inserted ID.
		 * 
		 * @return int
		 */
		public function insertID() {
			return $this->db->lastInsertId();
		}

		/**
		 * @deprecated Use insertID() instead.
		 */
		public function insert_id() {
			return $this->insertID();
		}

		/**
		 * @deprecated Use numRows() instead.
		 */
		public function affected_rows() {
			return $this->numRows();
		}

		/**
		 * @deprecated Does nothing anymore.
		 */
		public function free_result($res) {
			// mysqli_free_result($res);
		}

		/**
		 * @deprecated Use numRows() instead.
		 */
		public function result($res, $int) {
			return $this->numRows($res);
		}

		/**
		 * @deprecated Use prepared statements instead.
		 */
		public function chars($str) {
			return $this->db->quote($str);
		}

		/**
		 * Begin a transaction.
		 */
		public function beginTransaction() {
			return $this->db->beginTransaction();
		}

		/**
		 * Commit a transaction.
		 */
		public function commit() {
			return $this->db->commit();
		}
	}
?>