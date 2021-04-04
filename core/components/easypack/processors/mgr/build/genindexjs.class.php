<?php

	class genIndexJsProcessor extends modObjectGetProcessor
	{
		public $classKey = 'EasypackExtras';

		public function process()
		{
			/* define package names */
			$this->PKG_NAME = $this->object->getProperty('name');
			$this->PKG_NAME_LOWER = str_replace([' ', '-', '.', '*', '!', '@', '#', '$', '%', '^', '&', '_'], '', mb_strtolower($this->PKG_NAME));
			$this->modelPath = MODX_BASE_PATH . $this->object->getProperty('core') . '/model/';

			$txt = $this->_GenResolver();;
			return $this->success($txt);
		}

		public function _GenResolver()
		{
			$txt = '';
			$shema = json_decode($this->object->getProperty('tables_shema', '[]'), 1);
			$PKG_NAME_LOWER = $this->PKG_NAME_LOWER;
			if (!empty($shema)) {
				$txt = include MODX_CORE_PATH . 'components/easypack/processors/mgr/build/examples/index.js.php';
			}
			return $txt;
		}
	}

	return 'genIndexJsProcessor';
