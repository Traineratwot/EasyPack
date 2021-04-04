<?php

	class genResolverProcessor extends modObjectGetProcessor
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
			$code = [];
			$xml_schema_file = $this->modelPath . $this->PKG_NAME_LOWER . '/' . $this->PKG_NAME_LOWER . '.mysql.schema.xml';
			if (file_exists($xml_schema_file)) {
				$SXE = new SimpleXMLElement($xml_schema_file, 0, TRUE);
				$classes = [];
				if (isset($SXE->object)) {
					foreach ($SXE->object as $object) {
						$classes[] = (string)$object['class'];
					}
				}
			}

			if (!empty($classes)) {
				foreach ($classes as $cls) {
					$code[] = '$manager->createObjectContainer(\'' . $cls . '\');';
				}
			}

			$code = implode("\n", $code);
			$PKG_NAME_LOWER = $this->PKG_NAME_LOWER;
			$txt = include MODX_CORE_PATH . 'components/easypack/processors/mgr/build/examples/resolver.php';
			return $txt;
		}
	}

	return 'genResolverProcessor';
