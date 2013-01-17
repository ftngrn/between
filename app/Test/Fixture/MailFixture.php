<?php
/**
 * MailFixture
 *
 */
class MailFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'hash' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 16, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'from_user_id' => array('type' => 'integer', 'null' => false, 'default' => null),
		'to_user_id' => array('type' => 'integer', 'null' => false, 'default' => null),
		'uid' => array('type' => 'integer', 'null' => false, 'default' => null),
		'source' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'hash' => array('column' => 'hash', 'unique' => 1)
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
			'hash' => 'Lorem ipsum do',
			'from_user_id' => 1,
			'to_user_id' => 1,
			'uid' => 1,
			'source' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created' => '2013-01-17 15:18:18'
		),
	);

}
