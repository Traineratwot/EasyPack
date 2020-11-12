<?php

	class EasypackUpdateRestProcessor extends modObjectUpdateProcessor
	{
		public $classKey = 'EasypackExtras';
		public $primaryKeyField = 'id';
		public $required = ['name', 'core', 'version'];
		public $unique = ['name', 'core'];

		public function initialize()
		{
			if (isset($this->properties['data'])) {
				$this->properties = array_merge($this->properties, json_decode($this->properties['data'], 1));
			}
			return parent::initialize();
		}

		public function beforeSet()
		{

			$this->setProperty('date', date(DATE_ATOM));
			if (isset($this->properties['tables']) and !empty($this->properties['prefix'])) {
				$tables = [];
				$tables['tables'] = explode(',', $this->properties['tables']);
				$tables['tables'] = array_map('trim', $tables['tables']);
				$tables['tables'] = array_unique($tables['tables']);
				$tables['prefix'] = $this->properties['prefix'];
				$this->setProperty('tables', json_encode($tables));
				unset($this->properties['prefix']);
			}

			foreach ($this->properties as $key => $prop) {

				if (is_array($prop)) {
					$prop = array_unique($prop);
					if (count($prop) == 0 or empty($prop)) {
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

		public function isNoEmpty($var)
		{
			switch (gettype($var)) {
				case "array":
					if (count($var) == 0) {
						return 0;
					}
					break;
				case "string":
					return (trim($var) == '') ? 0 : 1;
				case "NULL":
				case "resource (closed)":
					return 0;
				case "boolean":
				case "integer":
				case "resource":
					return 1;
				default:
					return (int)!empty($var);
			}
			$score = 0;
			foreach ($var as $k => $v) {
				$score += $this->is_empty($v);
			}
			return !(bool)$score;
		}
	}

	return 'EasypackUpdateRestProcessor';