<?php

	/**
	 * Date: 10.06.2020
	 * Time: 21:10
	 */
	class EasypackCreateProcessor extends modObjectCreateProcessor
	{
		public $classKey = 'EasypackExtras';
		public $primaryKeyField = 'id';
		public $required = ['name', 'version'];
		public $unique = ['name', 'core'];

		public function beforeSet()
		{

			if (isset($this->properties['tables']) and !empty($this->properties['prefix'])) {
				$tables = [];
				$tables['tables'] = explode(',', $this->properties['tables']);
				$tables['tables'] = array_map('trim', $tables['tables']);
				$tables['tables'] = array_unique($tables['tables']);
				$tables['prefix'] = $this->properties['prefix'];
				$this->setProperty('tables', json_encode($tables));
				unset($this->properties['prefix']);
			}

			foreach ($this->required as $tmp) {
				if (!$this->getProperty($tmp)) {
					$this->addFieldError($tmp, 'field_required');
				}
			}

			foreach ($this->unique as $tmp) {
				if ($this->modx->getCount($this->classKey, [$tmp => $this->getProperty($tmp)])) {
					$this->addFieldError($tmp, 'field_unique');
				}
			}
			$this->setProperty('date', date(DATE_ATOM));


			foreach ($this->properties as $key => $prop) {

				if (is_array($prop)) {
					if (count($prop) == 0) {
						$this->setProperty($key, NULL);
					} elseif (count($prop) == 1 and !$prop[0]) {
						$this->setProperty($key, NULL);
					} else {
						$prop = json_encode($prop, 256);
						$this->setProperty($key, $prop);
					}
				}
				switch ($key) {
					case 'core':
					case 'assets':
						if (empty($prop)) {
							$this->setProperty($key, "{$key}/components/" . mb_strtolower($this->getProperty('name')));
						}
					case 'readme':
					case 'changelog':
					case 'setup_option':
					case 'php_resolver':
					case 'license':
						$this->setProperty($key, trim(str_replace('\\', "/", $prop), "/"));
						if (!file_exists(MODX_BASE_PATH . $this->getProperty($key))) {
							$this->addFieldError($key, 'file not found');
						}
						break;
				}
			}

			return !$this->hasErrors();

		}

	}

	return "EasypackCreateProcessor";