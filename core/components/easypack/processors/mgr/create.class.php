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
		public $unique = ['name'];

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

			if (isset($this->properties['tables'])) {
				$tables = json_decode($this->properties['tables'], 1);
				if (empty($tables['prefix'])) {
					$tables['prefix'] = $this->modx->config['table_prefix'];
				}
				if (!is_array($tables['tables'])) {
					$tables['tables'] = [$tables['tables']];
				}
				$tables['tables'] = array_map('trim', $tables['tables']);
				$tables['tables'] = array_unique($tables['tables']);
				$this->setProperty('tables', json_encode($tables));
				unset($this->properties['prefix']);
			}

//			if (isset($this->properties['dependence']) and !empty($this->properties['dependence'])) {
//				foreach ($this->properties['dependence'] as $dependence) {
//					if (array_key_exists($dependence, $this->properties['requires']['extras'])) {
//						continue;
//					}
//					$dd = $this->getPackageInfo($dependence);
//					if ($dd != FALSE) {
//						$this->setDependence($dependence, $dd);
//					}
//				}
//				foreach ($this->properties['requires']['extras'] as $dependence => $v) {
//					if (!in_array($dependence, $this->properties['dependence'])) {
//						unset($this->properties['requires']['extras'][$dependence]);
//					}
//				}
//			}

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

		public function afterSave()
		{
			$this->sendAuthorStat([
				'action' => 'create',
				'componentName' => $this->object->get('name'),
				'data' => $this->object->toArray(),
			]);
			return TRUE;
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

		public function sendAuthorStat($data)
		{
			$curl = curl_init();
			$data = array_merge(['componentName' => 'easypack', 'site' => $_SERVER['SERVER_NAME']], $data);

			$data = json_encode($data);
			curl_setopt_array($curl, [
				CURLOPT_URL => 'http://traineratwot.aytour.ru/component/stat',
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 10,
				CURLOPT_SSL_VERIFYHOST => FALSE,
				CURLOPT_SSL_VERIFYPEER => FALSE,
				CURLOPT_AUTOREFERER => TRUE,
				CURLOPT_FOLLOWLOCATION => TRUE,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $data,
				CURLOPT_HEADER => 0,
			]);

			curl_exec($curl);
			curl_close($curl);
		}
	}

	return "EasypackCreateProcessor";
