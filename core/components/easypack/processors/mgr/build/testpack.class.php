<?php


	class EasypackTestPackProcessor extends modProcessor
	{
		public $classKey = 'EasypackExtras';
		public $primaryKeyField = 'id';
		/**
		 * @var modTransportPackage|null
		 */
		private $package;
		/**
		 * @var EasypackExtras|null
		 */
		private $Easypack;


		/**
		 * @return array|mixed|string
		 */
		public function process()
		{
			$this->tstart = microtime();
			set_time_limit(0);
			$id = $this->getProperty('id');
			if ($id) {
				$this->Easypack = $this->modx->getObject($this->classKey, $id);
				if (!$this->Easypack) {
					return $this->failure($this->classKey . ' object not found', ['$id' => $id, 'line' => __LINE__]);
				}
			} else {
				return $this->failure('id not found', ['line' => __LINE__]);
			}
			/* define package names */
			$version = explode('-', $this->Easypack->get('version'));
			$this->PKG_NAME = $this->Easypack->get('name');
			$this->PKG_NAME_LOWER = str_replace([' ', '-', '.', '*', '!', '@', '#', '$', '%', '^', '&', '_'], '', mb_strtolower($this->PKG_NAME));
			$this->PKG_VERSION = isset($version[0]) ? $version[0] : NULL;
			$this->PKG_RELEASE = isset($version[1]) ? $version[1] : NULL;
			$this->classes = [
				'settings' => 'modSystemSetting',
				//'events' => 'modEvent',
				//'AccessPolices' => 'modAccessPolicy',
				//'policies' => 'modAccessPolicyTemplate',
				'menus' => 'modMenu',
				//'widgets' => 'modDashboardWidget',
				'plugins' => 'modPlugin',
				'snippets' => 'modSnippet',
				'chunks' => 'modChunk',
				'templates' => 'modTemplate',
				//'resources' => 'modResource',
			];

			$this->signature = $this->PKG_NAME_LOWER . '-' . $this->PKG_VERSION . '-' . $this->PKG_RELEASE;
			$this->package = $this->modx->getObject('transport.modTransportPackage', $this->signature);
			if ($this->package) {
				return $this->testPack();
			} else {
				$this->modx->runProcessor('workspace/packages/scanLocal');
				$this->package = $this->modx->getObject('transport.modTransportPackage', $this->signature);
				if ($this->package) {
					return $this->testPack();
				} else {
					return $this->failure('error package not found: "' . $this->signature . '"', ['line' => __LINE__]);
				}
			}
		}

		/**
		 * @return array|string
		 */
		private function testPack()
		{
			$this->package->parseSignature();
			$identifiers = $this->modx->getOption('package_install_identifiers', NULL, []);
			$this->package->getTransport();
			$this->addResponse('identifiers', 'name', $identifiers);

			foreach ($this->package->package->vehicles as $vIndex => $vehicleMeta) {
				$vOptions = $vehicleMeta;
				if ($vehicle = $this->package->package->get($vehicleMeta['filename'], $vOptions)) {

					switch ($vehicle->payload['class']) {
						case 'modNamespace':
							$this->modNamespace($vehicle->payload);
							break;
						case 'modSystemSetting':
							$this->modSystemSetting($vehicle->payload);
							break;
						case 'modMenu':
							$this->modMenu($vehicle->payload);
							break;
						case 'modCategory':
							$this->modCategory($vehicle->payload);
							foreach ($vehicle->payload['related_objects'] as $key => $elem) {
								switch ($key) {
									case 'Plugins':
										foreach ($elem as $obj) {
											$this->modPlugin($obj);
										}
										break;
								}
							}
							break;
					}
				}
			}


			return $this->success('ok', ['msg' => $this->toHtml()]);
		}

		/**
		 * @return string
		 */
		private function toHtml()
		{
			$resp = '<table style="width:100%">';
			$resp .= '
			<colgroup >
				<col span="2" style="text-align: left;">
				<col span="2" style="text-align: center;">
				<col span="1" style="text-align: right;">
			</colgroup>';
			foreach ($this->response['data'] as $data) {
				if ($data['result']) {
					$resp .= "<tr><td>{$data['class']}</td> <td style=\"text-align: center;\">-</td> <td>{$data['name']}</td> <td style=\"text-align: center;\">:</td> <td style=\"text-align: center;\"><span class='true'>проверенно</span></td></tr>";
				} else {
					$resp .= "<tr><td>{$data['class']}</td> <td style=\"text-align: center;\">-</td> <td>{$data['name']}</td> <td style=\"text-align: center;\">:</td> <td style=\"text-align: center;\"><span class='false'>{$data['msg']}</span></td></tr>";
				}
			}
			$resp .= '</table>';
			return $resp;
		}

		/**
		 * @param        $class
		 * @param        $name
		 * @param string $msg
		 * @param bool   $result
		 */
		private function addResponse($class, $name, $msg = 'ok', $result = TRUE)
		{
			$this->response['data'][] = ['class' => $class, 'name' => $name, 'msg' => $msg, 'result' => $result];
		}

		/**
		 * @param $vehicle
		 */
		private function modNamespace($vehicle)
		{
			try {
				$name = $this->Easypack->get('name');
				$modNamespace = $this->modx->getObject(__FUNCTION__, $name);
				/** @var modNamespace $modNamespace */
				if (!$modNamespace) {
					throw new PackagerException(__FUNCTION__ . ' not found');
				} else {
					$modNamespace = $modNamespace->toArray();
				}
				$this->Namespace = $modNamespace['name'];
				$vehicle['object'] = json_decode($vehicle['object'], 1);
				if ($vehicle['object']['name'] != $modNamespace['name']) {
					throw new PackagerException('invalid name: "' . $this->modx->lexicon('EasyPack.compare.notEq', ['one' => $vehicle['object']['name'], 'two' => $modNamespace['name']]) . '"');
				}
				if ($vehicle['object']['path'] != $modNamespace['path']) {
					throw new PackagerException('invalid path: "' . $this->modx->lexicon('EasyPack.compare.notEq', ['one' => $vehicle['object']['path'], 'two' => $modNamespace['path']]) . '"');
				}
				if ($vehicle['object']['assets_path'] != $modNamespace['assets_path']) {
					throw new PackagerException('invalid assets_path: "' . $this->modx->lexicon('EasyPack.compare.notEq', ['one' => $vehicle['object']['assets_path'], 'two' => $modNamespace['assets_path']]) . '"');
				}
			} catch (PackagerException $e) {
				return $this->addResponse(__FUNCTION__, $vehicle['native_key'], $e->getMessage(), FALSE);
			}
			return $this->addResponse(__FUNCTION__, $vehicle['native_key']);
		}

		/**
		 * @param $vehicle
		 */
		private function modSystemSetting($vehicle)
		{
			try {
				$modSystemSetting = $this->modx->getObject(__FUNCTION__, $vehicle['native_key']);
				/** @var modSystemSetting $modSystemSetting */
				if (!$modSystemSetting) {
					throw new PackagerException(__FUNCTION__ . ' not found');
				} else {
					$modSystemSetting = $modSystemSetting->toArray();
				}
				$vehicle['object'] = json_decode($vehicle['object'], 1);
				if ($vehicle['object']['key'] != $modSystemSetting['key']) {
					throw new PackagerException('invalid key: "' . $this->modx->lexicon('EasyPack.compare.notEq', ['one' => $vehicle['object']['key'], 'two' => $modSystemSetting['key']]) . '"');
				}
				if ($vehicle['object']['value'] != $modSystemSetting['value']) {
					throw new PackagerException('invalid value: "' . $this->modx->lexicon('EasyPack.compare.notEq', ['one' => $vehicle['object']['value'], 'two' => $modSystemSetting['value']]) . '"');
				}
				if ($vehicle['object']['xtype'] != $modSystemSetting['xtype']) {
					throw new PackagerException('invalid xtype: "' . $this->modx->lexicon('EasyPack.compare.notEq', ['one' => $vehicle['object']['xtype'], 'two' => $modSystemSetting['xtype']]) . '"');
				}
				if ($vehicle['object']['area'] != $modSystemSetting['area']) {
					throw new PackagerException('invalid area: "' . $this->modx->lexicon('EasyPack.compare.notEq', ['one' => $vehicle['object']['area'], 'two' => $modSystemSetting['area']]) . '"');
				}
				if ($vehicle['object']['namespace'] != $modSystemSetting['namespace']) {
					throw new PackagerException('invalid namespace: "' . $this->modx->lexicon('EasyPack.compare.notEq', ['one' => $vehicle['object']['namespace'], 'two' => $modSystemSetting['namespace']]) . '"');
				}
			} catch (PackagerException $e) {
				return $this->addResponse(__FUNCTION__, $vehicle['object'][$vehicle['unique_key']], $e->getMessage(), FALSE);
			}
			return $this->addResponse(__FUNCTION__, $vehicle['object'][$vehicle['unique_key']]);
		}

		/**
		 * @param $vehicle
		 */
		private function modMenu($vehicle)
		{
			try {
				$modMenu = $this->modx->getObject(__FUNCTION__, $vehicle['native_key']);
				/** @var modSystemSetting $modMenu */
				if (!$modMenu) {
					throw new PackagerException(__FUNCTION__ . ' not found');
				} else {
					$modMenu = $modMenu->toArray();
				}
				$vehicle['object'] = json_decode($vehicle['object'], 1);
				if ($vehicle['object']['text'] != $modMenu['text']) {
					throw new PackagerException('invalid text: "' . $this->modx->lexicon('EasyPack.compare.notEq', ['one' => $vehicle['object']['text'], 'two' => $modMenu['text']]) . '"');
				}
				if ($vehicle['object']['parent'] != $modMenu['parent']) {
					throw new PackagerException('invalid parent: "' . $this->modx->lexicon('EasyPack.compare.notEq', ['one' => $vehicle['object']['parent'], 'two' => $modMenu['parent']]) . '"');
				}
				if ($vehicle['object']['action'] != $modMenu['action']) {
					throw new PackagerException('invalid action: "' . $this->modx->lexicon('EasyPack.compare.notEq', ['one' => $vehicle['object']['action'], 'two' => $modMenu['action']]) . '"');
				}
				if ($vehicle['object']['description'] != $modMenu['description']) {
					throw new PackagerException('invalid description: "' . $this->modx->lexicon('EasyPack.compare.notEq', ['one' => $vehicle['object']['description'], 'two' => $modMenu['description']]) . '"');
				}
				if ($vehicle['object']['namespace'] != $modMenu['namespace']) {
					throw new PackagerException('invalid namespace: "' . $this->modx->lexicon('EasyPack.compare.notEq', ['one' => $vehicle['object']['namespace'], 'two' => $modMenu['namespace']]) . '"');
				}
			} catch (PackagerException $e) {
				return $this->addResponse(__FUNCTION__, $vehicle['object'][$vehicle['unique_key']], $e->getMessage(), FALSE);
			}
			return $this->addResponse(__FUNCTION__, $vehicle['object'][$vehicle['unique_key']]);
		}

		/**
		 * @param $vehicle
		 */
		private function modCategory($vehicle)
		{
			try {
				$vehicle['object'] = json_decode($vehicle['object'], 1);
				$modCategory = $this->modx->getObject(__FUNCTION__, ['category' => $vehicle['object']['category']]);
				/** @var modCategory $modCategory */
				if (!$modCategory) {
					throw new PackagerException(__FUNCTION__ . ' not found');
				}

			} catch (PackagerException $e) {
				return $this->addResponse(__FUNCTION__, $vehicle['object'][$vehicle['unique_key']], $e->getMessage(), FALSE);
			}
			return $this->addResponse(__FUNCTION__, $vehicle['object'][$vehicle['unique_key']]);
		}

		/**
		 * @param $vehicle
		 */
		private function modPlugin($vehicle)
		{
			try {
				$vehicle['object'] = json_decode($vehicle['object'], 1);
				$modPlugin = $this->modx->getObject(__FUNCTION__, [$vehicle['unique_key'] => $vehicle['object'][$vehicle['unique_key']]]);
				/** @var modPlugin $modPlugin */
				if (!$modPlugin) {
					throw new PackagerException(__FUNCTION__ . ' not found');
				}
				$modPluginEvent = $modPlugin->getMany('PluginEvents');
				/** @var modPluginEvent $event */
				foreach ($modPluginEvent as $event) {
					$event = $event->toArray();
					$score = 1;
					foreach ($vehicle['related_objects']['PluginEvents'] as $ev) {
						$ev['object'] = json_decode($ev['object'],1);
						if ($ev['object']['event'] == $event['event']) {
							$score--;
							break;
						}
					}
					if ($score != 0) {
						throw new PackagerException('invalid PluginEvent: "' . $this->modx->lexicon('EasyPack.compare.notEq', ['one' => $ev['object']['event'], 'two' => $event['event']]) . '"');
					}
				}

			} catch (PackagerException $e) {
				return $this->addResponse(__FUNCTION__, $vehicle['object'][$vehicle['unique_key']], $e->getMessage(), FALSE);
			}
			return $this->addResponse(__FUNCTION__, $vehicle['object'][$vehicle['unique_key']]);
		}

	}

	class PackagerException extends Exception
	{
	}

	return 'EasypackTestPackProcessor';