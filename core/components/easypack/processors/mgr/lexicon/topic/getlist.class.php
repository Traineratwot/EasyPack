<?php

	/**
	 * Date: 10.06.2020
	 * Time: 21:10
	 */
	class EasypackLexiconTopicGetListProcessor extends modProcessor
	{
		public $classKey = 'EasypackExtras';

		public function process()
		{
			$id = $this->getProperty('extras',FALSE);
			$lang = $this->getProperty('lang',FALSE);
			$topic = $this->getProperty('topic',FALSE);
			$limit = (int)$this->getProperty('limit',0);
			$start = (int)$this->getProperty('start',0);
			$sort = $this->getProperty('sort');
			$dir = $this->getProperty('dir');
			$query = $this->getProperty('query', FALSE);
			if ($id and $lang and $topic) {
				$this->Easypack = $this->modx->getObject($this->classKey, $id);
				if(!$this->Easypack){
					return $this->outputArray();
				}
				$core = $this->Easypack->getProperty('core');
				$langPath = MODX_BASE_PATH . $core . '/lexicon/' . $lang . '/' . $topic;
				$response = [];
				if (file_exists($langPath) and is_file($langPath)) {
					$fp = tmpfile();
					try {
						$_lang = [];
						fwrite($fp, file_get_contents($langPath));
						$t = stream_get_meta_data($fp);
						if($t['unread_bytes'] > 0) {
							include $t['uri'];
						}else{
							include $langPath;
						}
						if ($sort == 'value') {
							if ($dir == 'ASC') {
								asort($_lang);
							} else {
								arsort($_lang);
							}
						} elseif ($sort == 'key') {
							if ($dir == 'ASC') {
								ksort($_lang);
							} else {
								krsort($_lang);
							}
						}
						if ($query) {
							foreach ($_lang as $key => $value) {
								if (mb_stripos ($value,$query) === FALSE and mb_stripos ($key,$query) === FALSE) {
									unset($_lang[$key]);
								}
							}
						}
						$i = 0;
						foreach ($_lang as $key => $value) {
							$i++;
							if ($i > $start) {
								$response[] = [
									'key' => $key,
									'value' => $value,
								];
							}
							if ($limit and count($response) >= $limit) {
								break;
							}
						}

						return $this->outputArray($response, count($_lang));
					} catch (Exception $e) {

					}
					fclose($fp);
					return $this->outputArray($response);
				}
				return $this->outputArray();
			}
			return $this->outputArray();
		}

		public function outputArray(array $array = [], $count = FALSE)
		{
			if ($count === FALSE) {
				$count = count($array);
			}
			$output = json_encode([
				'success' => TRUE,
				'total' => $count,
				'results' => $array,
			]);
			if ($output === FALSE) {
				$this->modx->log(modX::LOG_LEVEL_ERROR, 'Processor failed creating output array due to JSON error ' . json_last_error());
				return json_encode(['success' => FALSE]);
			}
			return $output;
		}
	}

	return "EasypackLexiconTopicGetListProcessor";