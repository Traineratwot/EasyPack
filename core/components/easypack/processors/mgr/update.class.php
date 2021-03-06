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
			try {
				$this->properties['requires'] = json_decode($this->properties['requires'], 1);
			} catch (Exception $e) {
				$this->properties['requires'] = [];
			}
			if (!is_array($this->properties['requires'])) {
				$this->properties['requires'] = [];
			}
			$this->setProperty('date', date(DATE_ATOM));

			if (isset($this->properties['tables'])) {
				$tables = json_decode($this->properties['tables'],1);
				if (empty($tables['prefix'])) {
					$tables['prefix'] = $this->modx->config['table_prefix'];
				}
				if(!is_array($tables['tables'])){
					$tables['tables'] =[$tables['tables']];
				}
				$tables['tables'] = array_map('trim', $tables['tables']);
				$tables['tables'] = array_unique($tables['tables']);
				$this->setProperty('tables', json_encode($tables));
				unset($this->properties['prefix']);
			}


			foreach ($this->properties as $key => $prop) {

				if (is_array($prop) or is_object($prop)) {
					$prop = array_unique($prop);
					if (count($prop) == 0 or empty($prop)) {
						$this->setProperty($key, NULL);
					} elseif (count($prop) == 1 and empty(array_values($prop)[0])) {
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

		public function _addDependence()
		{
			if ((bool)$this->getProperty('add_dependence_extraExt')) {
				$this->setDependence('ExtraExt', ["service_url" => "modstore.pro"]);
			}
			if ((bool)$this->getProperty('add_dependence_modutil')) {
				$this->setDependence('modUtilities', ["service_url" => "modstore.pro"]);
			}
		}

		public function setDependence($name = '', $data = [])
		{
			if (!isset($this->properties['requires']['extras'])) {
				$this->properties['requires']['extras'] = [];
			}
			$requires = $this->properties['requires'] ?: [];
			$requires['extras'][$name] = $data;
			$this->properties['requires'] = $requires;
		}

		public function getPackageInfo($name)
		{
			$p = $this->modx->getObject('transport.modTransportPackage', ['package_name:LIKE' => $name]);
			if ($p) {
				$p = $p->toArray();
				$t = $this->modx->getObject('transport.modTransportProvider', $p['provider']);
				if ($t) {
					$t = $t->toArray();
					$resp = [];
					if ($p['version'] and $p['release']) {
						$resp['version'] = $p['version'] . '-' . $p['release'];
					}
					if ($t['service_url'] or $t['name']) {
						$resp['service_url'] = $t['service_url'] ?: $t['name'];
					}
					return $resp;
				}
			}
			return FALSE;
		}
	}

	return 'EasypackUpdateRestProcessor';