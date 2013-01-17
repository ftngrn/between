<?php
App::uses('AppModel', 'Model');
/**
 * Mail Model
 *
 */
class Mail extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'hash';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'uid' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'source' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	public function beforeSave($options = array()) {
		$result = parent::beforeSave($options);
		if (!$this->id && !isset($this->data[$this->alias]['id'])) {
			//新規作成時にhashを生成する
			$this->data[$this->alias]['hash'] = $this->generateHash();
		}
		return $result;
	}

	public function generateHash($byte_length = 8) {
		$bytes = openssl_random_pseudo_bytes($byte_length);
		$hash = bin2hex($bytes);
		return $hash;
	}

  /**
  * メールを解析する
  *
  * @param string $src メールのソース
  * @return array メール情報配列
  */
  public function parse($src) {
		//Qdmail
		require_once(APP. 'Vendor' .DS. 'qdmail/qdmail.php');
		require_once(APP. 'Vendor' .DS. 'qdmail/qdmail_receiver.php');

		$m = QdmailReceiver::start('direct', $src);
		$msg = array(
			"from" => $m->header(array('from', 'mail')),
			"to" => $m->header(array('to', 'mail')),
			"subject" => $m->header(array('subject', 'name')),
			"date" => $m->header('date'),
			"message-id" => $this->trimMessageId($m->header('message-id')),
			"references" => $this->trimMessageId($m->header('references')),
			"in-reply-to" => $this->trimMessageId($m->header('in-reply-to')),
			"body" => $m->text(),
			"body_auto" => $m->bodyAutoSelect(),
			"attach" => $m->attach(),
		);
		//本文のエンコーディングを変換
		$msg["encoding"] = mb_detect_encoding($msg['body'], 'auto');
		if ($msg["encoding"] !== "UTF-8") {
			$msg['body'] = mb_convert_encoding($msg['body'], 'UTF-8', $msg["encoding"]);
		}
		//署名以降を削除
		$msg['body_raw'] = $msg['body'];
		$body = preg_split('/--\r?\n/is', $msg['body'], 2);
		$msg['body'] = reset($body);

		return $msg;
	}

	public function trimMessageId($mid) {
		return preg_replace("/[<>]/", "", $mid);
	}

}
