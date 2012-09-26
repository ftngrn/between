<?php
config('account');

class FetchShell extends AppShell
{
	/**
	* Mail configurations
	*
	* @var array
	*/
	protected $mailConfig = array();

	public function startUp() {
		//$this->out("startUp");
		$this->log("Ready for fetch", LOG_INFO);
		$this->loadAccountConfig();
	}

	public function main() {
		//$this->out("main");
		$this->log("Start fetch", LOG_INFO);

		//IMAPオプションチェック
		$this->log("Check imap options", LOG_INFO);
		$keys = array_keys($this->mailConfig);
		if (!in_array('imap', $keys, true)) {
			$msg = sprintf("IMAP options not exist");
			$this->log($msg, LOG_ERR);
			exit;
		}
		$this->log("Get imap options", LOG_INFO);

		$mails = array();
		$mbox = null;
		try {
			$this->log("Check mail", LOG_INFO);

			//IMAPオープン
			$mbox = $this->getMailBox($this->mailConfig);

			//IMAPでヘッダとボディをフェッチ
			//条件 => 未読・未フラグ
			$search_options = array(
				'criteria' => array(
					'seen' => false,
					'flagged' => false,
				),
			);
			$mails = $this->getListMails($mbox, $search_options);
			foreach ($mails as $i => $mail) {
				$source = $mail['header'].$mail['body'];
				$msg = $this->parseMail($source);
				var_dump($msg);
			}
		}
		catch (Exception $ex) {
			$this->log($ex->getMessage());
		}
		//IMAPクローズ
		if (!is_null($mbox) && is_resource($mbox)) {
			imap_close($mbox);
		}
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
		$mailcon = sprintf("{%s:%d%s}", $options['imap']['host'], $options['imap']['port'], $options['imap']['flag']);
		$mailbox = sprintf("%s%s", $mailcon, $options['imap']['mailbox_name']);
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
			$this->log(sprintf("Not found for criteria [%s]", trim($criteria)), LOG_INFO);
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
		require_once(APP. 'Vendor' .DS. 'qdmail/qdmail.php');
		require_once(APP. 'Vendor' .DS. 'qdmail/qdmail_receiver.php');

		$m = QdmailReceiver::start('direct', $src);
		$msg = array(
			"from" => $m->header(array('from', 'mail')),
			"to" => $m->header(array('to', 'mail')),
			"subject" => $m->header(array('subject', 'name')),
			"date" => $m->header('date'),
			"body" => $m->bodyAutoSelect(),
			"attach" => $m->attach(),
		);
		//本文のエンコーディングを変換
		$msg["encoding"] = mb_detect_encoding($msg['body'], 'auto');
		if ($msg["encoding"] !== "UTF-8") {
			$msg['body'] = mb_convert_encoding($msg['body'], 'UTF-8', $msg["encoding"]);
		}

		return $msg;
	}

}
