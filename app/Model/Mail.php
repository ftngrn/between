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
}
