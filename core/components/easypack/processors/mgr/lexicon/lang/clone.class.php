<?php

	/**
	 * Date: 10.06.2020
	 * Time: 21:10
	 */
	class EasypackLexiconLangCloneProcessor extends modProcessor
	{
		public $classKey = 'EasypackExtras';

		public function process()
		{
			$id = $this->getProperty('extras', FALSE);
			$lang = $this->getProperty('lang', FALSE);
			$newLang = $this->getProperty('newLang', FALSE);
			if ($id and $lang and $newLang) {
				$this->Easypack = $this->modx->getObject($this->classKey, $id);
				if (!$this->Easypack) {
					return $this->failure('error');
				}
				$core = $this->Easypack->getProperty('core');
				$langPath = MODX_BASE_PATH . $core . '/lexicon/';
				$out = scandir($langPath . $lang . '/');
				$response = [];
				foreach ($out as $topic) {
					if (in_array($topic, ['.', '..'])) {
						continue;
					}
					if (is_file($langPath . $lang . '/' . $topic)) {
						$response[] = $topic;
					}
				}
				if (!mkdir($concurrentDirectory = $langPath . $newLang) && !is_dir($concurrentDirectory)) {
					throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
				}
				foreach ($response as $topic) {
					file_put_contents($langPath . $newLang . '/' . $topic, file_get_contents($langPath . $lang . '/' . $topic));
				}
				return $this->success('ok');
			}
			return $this->failure('error');
		}

	}

	return "EasypackLexiconLangCloneProcessor";