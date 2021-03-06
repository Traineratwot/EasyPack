<?php

	/**
	 * Date: 10.06.2020
	 * Time: 21:10
	 */
	class EasypackLexiconLangGetProcessor extends modProcessor
	{
		public $classKey = 'EasypackExtras';

		public function process()
		{
			$id = $this->getProperty('extras');
			if ($id) {
				$this->Easypack = $this->modx->getObject($this->classKey, $id);
				if(!$this->Easypack){
					return $this->outputArray();
				}
				$core = $this->Easypack->getProperty('core');
				$langPath = MODX_BASE_PATH . $core . '/lexicon/';
				$out = scandir($langPath);
				$response = [];
				foreach ($out as $lang) {
					if (in_array($lang, ['.', '..'])) {
						continue;
					}
					if (is_dir($langPath . $lang)) {
						$response[]['name'] = $lang;
					}
				}
				return $this->outputArray($response);
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

	return "EasypackLexiconLangGetProcessor";