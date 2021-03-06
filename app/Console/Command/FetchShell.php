<?php
App::uses('Router', 'Routing');
config('routes');
config('account');
require_once(APP. 'Vendor' .DS. 'qdmail/qdmail.php');
require_once(APP. 'Vendor' .DS. 'qdmail/qdmail_receiver.php');
require_once(APP. 'Vendor' .DS. 'qdmail/qdsmtp.php');

class FetchShell extends AppShell
{
	public $uses  = array('Mail');
	
	/**
	* Mail configurations
	*
	* @var array
	*/
	protected $mailConfig = array();

	/**
	* Map configurations
	*
	* @var array
	*/
	protected $mapConfig = array();

	/**
	* Template configurations
	*
	* @var array
	*/
	protected $templateConfig = array();

	protected $qdm = null;

	protected $referencesNum = 1;

	public function startUp() {
		//$this->out("startUp");
		$this->log("Ready for fetch", LOG_DEBUG);
		$this->loadAccountConfig();

		$this->qdm = & new Qdmail();
	}

	public function main() {
		//$this->out("main");
		$this->log("Start fetch（・δ・●）", LOG_INFO);

		//IMAPオプションチェック
		$this->log("Check imap options", LOG_INFO);
		$keys = array_keys($this->mailConfig);
		if (!in_array('imap', $keys, true)) {
			$msg = sprintf("IMAP options not exist");
			$this->log($msg, LOG_ERR);
			exit;
		}
		$this->log("Get imap options", LOG_INFO);

		$other_mail_uid_list = array();
		$mails = array();
		$mbox = null;
		try {
			$this->log("Check mail", LOG_INFO);

			//IMAPオープン
			$mbox = $this->getMailBox($this->mailConfig['imap']);

			//IMAPでヘッダとボディをフェッチ
			//条件 => 未フラグ
			$search_options = array(
				'criteria' => array(
					'flagged' => false,
				),
			);
			$mails = $this->getListMails($mbox, $search_options);
			$this->log(sprintf("Get %d mails", count($mails)), LOG_INFO);

			foreach ($mails as $i => $mail) {
				$uid = $mail['uid'];
				$source = $mail['header'].$mail['body'];
				//ソースからパース
				$msg = $this->parseMail($source);
				$this->log(sprintf("Parsed mail uid:%s from:%s", $uid, $msg['from']), LOG_INFO);
				//送信者マップと送信者の宛先ターゲットマップを取得
				$map_id = null;
				$map = null;
				$target_map = null;
				foreach ($this->mapConfig as $_id => $_map) {
					if (in_array($msg['from'], $_map['emails'], true)) {
						$map_id = $_id;
						$map = $_map;
						if (array_key_exists($_map['to'], $this->mapConfig)) {
							$target_map = $this->mapConfig[$_map['to']];
						}
						break;
					}
				}

				if (is_null($map_id) || empty($map)) {
					$this->log(sprintf("No map for mail from %s", $msg['from']), LOG_DEBUG);
					$other_mail_uid_list []= $uid;
					continue;
				}
				$this->log(sprintf("Get map for mail from %s", $msg['from']), LOG_INFO);

				if (is_null($target_map)) {
					$this->log(sprintf("No target_map for map[%d]", $map_id), LOG_DEBUG);
					$other_mail_uid_list []= $uid;
					continue;
				}
				$this->log(sprintf("Get target_map for map[%d]", $map_id), LOG_INFO);

				//saveMany
				$mailDatum = array(
					'Mail' => array(
						'from_user_id' => $map_id,
						'to_user_id' => $map['to'],
						'uid' => $uid,
						'source' => $source,
					),
				);
				$this->Mail->create();
				if (!$this->Mail->save($mailDatum)) {
					$this->log(sprintf("Mail could not save %s uid:%d", $msg['from'], $uid), LOG_DEBUG);
				}
				//保存したデータを読み込み直し
				$mailDatum = $this->Mail->findById($this->Mail->id);
				$this->log(sprintf("Mail has been saved id:%d hash:%s", $mailDatum['Mail']['id'], $mailDatum['Mail']['hash']), LOG_INFO);

				//URL作成
				$urlArray = array('controller' => 'mails', 'action' => 'show', 'hash' => $mailDatum['Mail']['hash']);
				$url = Router::url($urlArray, true);
				$this->log(sprintf("Mail url:%s", $url), LOG_INFO);

				//ターゲットのテンプレート取得
				$template = $this->templateConfig[$target_map['template_for_receive']];
				if (!$template) {
					$this->log(sprintf("No template for [%s]", $target_map['template_for_receive']), LOG_DEBUG);
					$other_mail_uid_list []= $uid;
					continue;
				}
				$this->log(sprintf("Get template for [%s]", $target_map['template_for_receive']), LOG_INFO);

				$body = $template['body'];
				$body = str_replace('%%SUBJECT%%', $msg['subject'], $body);
				$body = str_replace('%%BODY%%', trim($msg['body']), $body);
				$body = str_replace('%%URL%%', $url, $body);
				$this->log(sprintf("Replace subject,body and url"), LOG_INFO);

				//メールを作成して送信
				$this->log(sprintf("Ready for smtp"), LOG_INFO);
				$this->qdm->errorDisplay(false);
				$this->qdm->smtpObject()->error_display = false;
				$this->qdm->smtpLoglevelLink(true);
				$this->qdm->logLevel(3);
				$this->qdm->logPath(LOGS);
				$this->qdm->logFilename("mail.log");
				$this->qdm->errorlogPath(LOGS);
				$this->qdm->errorlogFilename("error_mail.log");

				$this->qdm->smtp(true);	//smtp send
				$this->qdm->smtpServer($this->mailConfig['smtp']);
				$this->log(sprintf("Ready for smtp finished!"), LOG_INFO);

				$this->log(sprintf("Set header,body for smtp"), LOG_INFO);
				$this->qdm->messageIdRight("gmail.com");

				$this->qdm->to($target_map['email'], '');
				if (isset($target_map['cc']) && !empty($target_map['cc'])) {
					$this->qdm->cc($target_map['cc']);
				}
				$this->qdm->subject($template['subject']);
				$this->qdm->from($this->mailConfig['mail'], $template['from_name']);
				$this->qdm->text($body);

				//新規ではなく、何かへの返信だった場合
				if (!empty($msg['in-reply-to'])) {
					//直前に自分が送ったメールのMessage-IDを取得し、In-Reply-Toヘッダに指定
	//				$prev_mid = $this->getPrevMessageId($target_map['email']);
					$prev_mid = $this->getPrevMessageId($map['email']);
					if ($prev_mid) {
						$this->qdm->addHeader('In-Reply-To', $prev_mid);
						$this->log(sprintf("Set In-Reply-To [%s]", $prev_mid), LOG_DEBUG);
					}
					$refs = $this->getLastReferences($target_map['email'], $map['email']);
					if (!empty($refs)) {
						$this->qdm->addHeader('References', implode(" ", $refs));
						$this->log(sprintf("Set %d References", count($refs)), LOG_DEBUG);
					}
				}

				$this->log(sprintf("Set header,body for smtp finished!"), LOG_INFO);

				$ret = $this->qdm->send();
				if ($ret) {
					$this->log("Sent mail to [".$target_map['email']."]", LOG_INFO);

					//送信したMessageIdを取得し
					//送信メールの控えをとっておく
					$mid = $this->trimMessageId($this->qdm->other_header['Message-Id']);
					$copy_path = CACHE. 'mails' .DS. sprintf("%s_%s_TO_%s.eml", date("YmdHis"), $mid, $target_map['email']);
					file_put_contents($copy_path, $body, LOCK_EX|FILE_APPEND);

					//返信したメールは次回からフェッチしないように
					//UIDに対して既読にしてフラグを付ける
					$set_flag_list = array("\\Seen", "\\Flagged");
					$ret = $this->setMailFlag($mbox, array($uid), $set_flag_list);
					if ($ret) {
						$this->log(sprintf("Set flag %s for uid:%s from:%s", implode(",", $set_flag_list), $uid, $msg['from']), LOG_INFO);
					} else {
						$this->log(sprintf("Can not set flag %s for uid:%s from:%s", implode(",", $set_flag_list), $uid, $msg['from']), LOG_ERR);
					}
				} else {
					$this->log("Send mail failed for [".$target_map['email']."]", LOG_ERR);
				}

				$this->qdm->reset();
			}

			//無関係だったメールは次回からフェッチしないように
			//UIDに対して既読にしてフラグを付ける
			if (count($other_mail_uid_list)) {
				$set_flag_list = array("\\Seen", "\\Flagged");
				$ret = $this->setMailFlag($mbox, $other_mail_uid_list, $set_flag_list);
				if ($ret) {
					$this->log(sprintf("Set flag %s for other mails uid:%s", implode(",", $set_flag_list), implode(",", $other_mail_uid_list)), LOG_INFO);
				} else {
					$this->log(sprintf("Can not set flag %s for other mails uid:%s", implode(",", $set_flag_list), implode(",", $other_mail_uid_list)), LOG_ERR);
				}
			}
		}
		catch (Exception $ex) {
			$this->log($ex->getMessage(), LOG_ERR);
		}
		//IMAPクローズ
		if (!is_null($mbox) && is_resource($mbox)) {
			imap_close($mbox);
		}
		$this->log(sprintf("Finish fetch, bye! (*´∀｀)"), LOG_INFO);
	}



	private function loadAccountConfig() {
		$acc = new ACCOUNT_CONFIG;
		$temp = get_class_vars(get_class($acc));
		foreach ($temp as $configName => $info) {
			$propName = $configName . 'Config';
			if (property_exists($this, $propName)) {
				$this->$propName = $info;
			}
		}
	}

	/**
	* IMAPリソースの作成
	*
	* @param array $options オプション
	* @return object IMAPリソース
	*/
	private function getMailBox($options) {
		$mailcon = sprintf("{%s:%d%s}", $options['host'], $options['port'], $options['flag']);
		$mailbox = sprintf("%s%s", $mailcon, $options['mailbox_name']);
		$mbox = imap_open($mailbox, $options['user'], $options['pass']);
		if (empty($mbox)) {
			throw new ErrorException(sprintf("Cant open mbox:%s", $mailbox));
		}
/*
		//メールボックスの確認
		$list = imap_list($mbox, $mailcon, "*");
		var_dump($list);
*/
		return $mbox;
	}


	/**
	* IMAPメールソース一覧の取得
	*
	* @param resource $mbox IMAPリソース
	* @param array $options オプション
	* @return array メールソースのリスト
	*/
	private function getListMails($mbox, $options) {
		$res = array();

		//Search INBOX
		$mboxes = imap_check($mbox);
		if ($mboxes->Nmsgs < 1) {
			return $res;
		}

		//条件作成
		$criteria = "";
		if (isset($options['criteria']['seen'])) {
			$criteria .= $options['criteria']['seen'] ? 'SEEN ' : 'UNSEEN ';
		}
		if (isset($options['criteria']['flagged'])) {
			$criteria .= $options['criteria']['flagged'] ? 'FLAGGED ' : 'UNFLAGGED ';
		}
		if (isset($options['criteria']['from'])) {
			$criteria .= sprintf("FROM \"%s\" ", $options['criteria']['from']);
		}
		if (isset($options['criteria']['since'])) {
			$criteria .= sprintf("SINCE \"%s\" ", $options['criteria']['since']);
		}
		if (isset($options['criteria']['before'])) {
			$criteria .= sprintf("BEFORE \"%s\" ", $options['criteria']['before']);
		}
		if (isset($options['criteria']['subject'])) {
			$criteria .= sprintf("SUBJECT \"%s\" ", $options['criteria']['subject']);
		}

/*
		//上記の条件に合致するメールのメッセージUIDを取得
		$msgNoList = imap_search($mbox, $criteria, SE_UID, 'UTF-8');
*/
		//日付昇順で、上記の条件に合致するメールのメッセージUIDを取得
		$msgNoList = imap_sort($mbox, SORTDATE, 0, SE_UID|SE_NOPREFETCH, $criteria, 'UTF-8');
		if (empty($msgNoList) || !is_array($msgNoList)) {
			$this->log(sprintf("Not found for criteria [%s]", trim($criteria)), LOG_DEBUG);
			return $res;
		}

		//Get mail information
		foreach ($msgNoList as $i => $msgNo) {
			if (isset($options['limit']) && $i >= (int)$options['limit']) {
				break;
			}

			$msg = array();
			$msg['uid'] = $msgNo;
			$msg['header'] = imap_fetchheader($mbox, $msgNo, FT_UID|FT_INTERNAL);
			//未読キープのためFT_PEEKを指定する
			$msg['body'] = imap_body($mbox, $msgNo, FT_UID|FT_PEEK|FT_INTERNAL);

			$res []= $msg;
		}
		return $res;
	}

	/**
	* IMAPメールにフラグ追加
	*
	* @param resource $mbox IMAPリソース
	* @param array $uid_list UIDリスト
	* @param array $flag_list セットしたいフラグリスト
	* @return bool 成功:true 失敗:false
	*/
	private function setMailFlag($mbox, $uid_list, $flag_list) {
		$uid_str = implode(",", $uid_list);
		$flag_str = implode(" ", $flag_list);
		$ret = imap_setflag_full($mbox, $uid_str, $flag_str, ST_UID);
		return $ret;
	}

	/**
	* IMAPメールからフラグ削除
	*
	* @param resource $mbox IMAPリソース
	* @param array $uid_list UIDリスト
	* @param array $flag_list 削除したいフラグリスト
	* @return bool 成功:true 失敗:false
	*/
	private function clearMailFlag($mbox, $uid_list, $flag_list) {
		$uid_str = implode(",", $uid_list);
		$flag_str = implode(" ", $flag_list);
		$ret = imap_clearflag_full($mbox, $uid_str, $flag_str, ST_UID);
		return $ret;
	}

	/**
	* メールを解析する
	*
	* @param string $src メールのソース
	* @return array メール情報配列
	*/
	private function parseMail($src) {
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

	private function trimMessageId($mid) {
		return preg_replace("/[<>]/", "", $mid);
	}

	//直前に自分が送ったメールのMessage-IDを取得
	private function getPrevMessageIdList($prev_to, $limit = 1) {
		$mid_list = array();
		$path = CACHE. 'mails' .DS. sprintf('*_TO_%s.eml', $prev_to);
		$files = glob($path);
		if (count($files) > 0) {
			rsort($files, SORT_STRING);
			if ($limit > count($files)) {
				$limit = count($files);
			}
			for ($i = 0; $i < $limit; $i++) {
				$prev_mid = $files[$i];
				$prev_mid = preg_replace('#^'.CACHE.'mails/[0-9]+_#', '', $prev_mid);
				$prev_mid = preg_replace('/_TO_(.+)$/', '', $prev_mid);
				$mid_list []= $prev_mid;
			}
		}
		return $mid_list;
	}

	private function getPrevMessageId($prev_to) {
		$list = $this->getPrevMessageIdList($prev_to);
		$prev_mid = null;
		if (count($list) > 0) {
			$prev_mid = $list[0];
		}
		return $prev_mid;
	}

	//直前に自分が送受信したメールのMessage-IDを取得
	private function getLastReferences($prev_to, $prev_from) {
		$mid_list = array();
		$to_list = $this->getPrevMessageIdList($prev_to, $this->referencesNum);
		$from_list = $this->getPrevMessageIdList($prev_from, $this->referencesNum);
		$count = count($to_list) > count($from_list) ? count($to_list) : count($from_list);
		for ($i = 0; $i < $count; $i++) {
			if ($i < count($to_list)) {
				$mid_list []= sprintf("<%s>", $to_list[$i]);
			}
			if ($i < count($from_list)) {
				$mid_list []= sprintf("<%s>", $from_list[$i]);
			}
		}
		return $mid_list;
	}
}
