<?php
/**
 * MapFixture
 *
 */
class MapFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'is_active' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'sender_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => 'user_emails.id'),
		'receiver_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => 'user_emails.id'),
		'updated' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'is_active' => 1,
			'sender_id' => 1,
			'receiver_id' => 1,
			'updated' => '2012-09-26 16:23:49',
			'created' => '2012-09-26 16:23:49'
		),
	);

}
