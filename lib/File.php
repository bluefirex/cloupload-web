<?php
	class File {
		const TYPE_ALL = 'all';
		const TYPE_AUDIO = 'audio';
		const TYPE_ARCHIVE = 'archive';
		const TYPE_BOOKMARK = 'bookmark';
		const TYPE_IMAGE = 'image';
		const TYPE_TEXT = 'text';
		const TYPE_UNKNOWN = 'unknown';
		const TYPE_VIDEO = 'video';

		const TRASHED = 'trashed';

		protected $href;
		protected $name;
		protected $private;
		protected $subscribed;
		protected $trashed;
		protected $url;
		protected $contentURL;
		protected $itemType;
		protected $viewCount;
		protected $remoteURL;
		protected $redirectURL;
		protected $thumbnailURL;
		protected $source;
		protected $createdAt;
		protected $updatedAt;
		protected $deletedAt;

		public static function fromRow(stdClass $row) {
			return new self(
				$row->href,
				$row->name,
				$row->isPrivate,
				$row->isSubscribed,
				$row->isTrashed,
				$row->url,
				$row->content_url,
				$row->type,
				$row->views,
				$row->remote_url,
				$row->redirect_url,
				$row->thumbnail_url,
				$row->source,
				$row->created_at,
				$row->updated_at,
				$row->deleted_at
			);
		}

		public static function fromAPI(array $row) {
			$row = (object) $row;

			return new self(
				$row->href,
				$row->name,
				$row->private,
				$row->subscribed,
				!is_null($row->deleted_at),
				$row->url,
				$row->content_url,
				$row->item_type,
				$row->view_counter,
				$row->remote_url,
				$row->redirect_url,
				$row->thumbnail_url,
				$row->source,
				strtotime($row->created_at),
				strtotime($row->updated_at),
				strtotime($row->deleted_at)
			);
		}

		public function __construct(
			$href, $name, $private, $subscribed, $trashed, $url, $contentURL, $itemType, $viewCount, $remoteURL,
			$redirectURL, $thumbnailURL, $source, $createdAt, $updatedAt, $deletedAt
		) {
			$this->href = $href;
			$this->name = $name;
			$this->private = $private;
			$this->subscribed = $subscribed;
			$this->trashed = $trashed;
			$this->url = $url;
			$this->contentURL = $contentURL;
			$this->itemType = $itemType;
			$this->viewCount = $viewCount;
			$this->remoteURL = $remoteURL;
			$this->redirectURL = $redirectURL;
			$this->thumbnailURL = $thumbnailURL;
			$this->source = $source;
			$this->createdAt = $createdAt;
			$this->updatedAt = $updatedAt;
			$this->deletedAt = $deletedAt;
		}

		public function getHref() {
			return $this->href;
		}

		public function getName() {
			return $this->name;
		}

		public function isPrivate() {
			return $this->private;
		}

		public function isSubscribed() {
			return $this->subscribed;
		}

		public function isTrashed() {
			return $this->trashed;
		}

		public function getURL() {
			return $this->url;
		}

		public function getContentURL() {
			return $this->contentURL;
		}

		public function getType() {
			return $this->itemType;
		}

		public function getViews() {
			return $this->viewCount;
		}

		public function getRemoteURL() {
			return $this->remoteURL;
		}

		public function getRedirectURL() {
			return $this->redirectURL;
		}

		public function getThumbnailURL() {
			return $this->thumbnailURL;
		}

		public function getSource() {
			return $this->source;
		}

		public function getCreationDate() {
			return $this->createdAt;
		}

		public function getUpdateDate() {
			return $this->updatedAt;
		}

		public function getDeletionDate() {
			return $this->deletedAt;
		}

		public function __toString() {
			$ret = '
				<div class="item">
					<div class="thumb" '.($this->getType() == self::TYPE_IMAGE ? 'style="background-image:url('.$this->getThumbnailURL().');"' : '').'>
			';
						
			switch ($this->getType()) {
				case self::TYPE_IMAGE:
					// Nothing
					break;

				case self::TYPE_AUDIO:
					$ret .= '
						<audio controls>
							<source src="'.$this->getContentURL().'" />
							<p>Audio preview is not supported by your browser.</p>
						</audio>
					';
					break;

				case self::TYPE_VIDEO:
					$ret .= '
						<video controls>
							<source src="'.$this->getContentURL().'" />
							<p>Video preview is not supported by your browser.</p>
						</video>
					';
					break;

				default:
					$ret .= getSVG($this->getType());
			}
			
			$ret .= '
					</div>

					<div class="meta">
						<h3><a href="'.$this->getURL().'" target="_blank">'.$this->getName().'</a></h3>
						<span>'.$this->getViews().' views &minus; '.PHPDateTime::getStringDifference(time(), $this->getCreationDate(), PHPDateTime::MODE_AGO).'</span>
					</div>
				</div>
			';

			return $ret;
		}

		/* DB Transfer */
		
		public static function deleteAll() {
			global $db;
			$db->query("DELETE FROM " . Database::TABLE_ITEMS . " WHERE 1");
		}

		public static function insert(array $items) {
			global $db;

			$stmt = $db->prepare("
				INSERT INTO " . Database::TABLE_ITEMS . "
				(
					href,
					name,
					isPrivate, isSubscribed, isTrashed,
					url, content_url,
					type,
					views,
					remote_url, redirect_url, thumbnail_url,
					source,
					created_at, updated_at, deleted_at
				)
				VALUES (
					:href,
					:name,
					:isPrivate, :isSubscribed, :isTrashed,
					:url, :content_url,
					:type,
					:views,
					:remote_url, :redirect_url, :thumbnail_url,
					:source,
					:created_at, :updated_at, :deleted_at
				)
			");

			foreach ($items as $item) {
				// Execute prepared statement
				$stmt->execute(array(
					$item->getHref(),
					$item->getName(),
					$item->isPrivate() ? 1 : 0,
					$item->isSubscribed() ? 1 : 0,
					$item->isTrashed() ? 1 : 0,
					$item->getURL(),
					$item->getContentURL(),
					$item->getType(),
					$item->getViews(),
					$item->getRemoteURL(),
					$item->getRedirectURL(),
					$item->getThumbnailURL(),
					$item->getSource(),
					$item->getCreationDate(),
					$item->getUpdateDate(),
					$item->getDeletionDate()
				));
			}
		}

		/* STATIC */
		public static function getItems($type) {
			global $db;

			if ($type == self::TYPE_ALL) {
				$res = $db->query("
					SELECT *
					FROM ".Database::TABLE_ITEMS."
					ORDER BY id ASC
				");
			} else if ($type == self::TRASHED) {
				$res = $db->query("
					SELECT *
					FROM ".Database::TABLE_ITEMS."
					WHERE isTrashed = 1
					ORDER BY id ASC
				");
			} else {
				$res = $db->query("
					SELECT *
					FROM ".Database::TABLE_ITEMS."
					WHERE type = ?
					ORDER BY id ASC
				", array($type));
			}

			$items = array();

			while ($row = $db->fetchObject($res)) {
				$items[] = self::fromRow($row);
			}

			return $items;
		}

		public static function getForQuery($qry) {
			global $db;

			$res = $db->query("
				SELECT *
				FROM ".Database::TABLE_ITEMS."
				WHERE name LIKE ?
				ORDER BY id ASC
			", array(
				'%' . $qry . '%'
			));

			$items = array();

			while ($row = $db->fetchObject($res)) {
				$items[] = self::fromRow($row);
			}

			return $items;
		}
	}
?>