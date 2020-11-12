<?php

	class EasypackBuildProcessor extends modProcessor
	{
		public $classKey = 'EasypackExtras';
		public $primaryKeyField = 'id';
		/**
		 * @var EasypackExtras|null
		 */
		public $Easypack;
		/**
		 * @var modPackageBuilder
		 */
		public $builder;
		/**
		 * @var string
		 */
		public $packName;
		/**
		 * @var float
		 */
		public $tstart;
		/** @var modCategory $category */
		public $category;
		public $category_attr;
		public $classes;
		public $modelPath;
		/**
		 * @var bool|string
		 */
		private $tmp_resolver;
		/**
		 * @var array|bool|float|mixed|string|null
		 */
		private $PKG_NAME;
		/**
		 * @var mixed
		 */
		private $PKG_NAME_LOWER;
		/**
		 * @var mixed|null
		 */
		private $PKG_VERSION;
		/**
		 * @var mixed|null
		 */
		private $PKG_RELEASE;
		/**
		 * @var array
		 */
		private $reqExtras = [];

		public function process()
		{
			try {
				$this->tstart = microtime(TRUE);
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
				$version = explode('-', $this->Easypack->getProperty('version'));
				$this->PKG_NAME = $this->Easypack->getProperty('name');
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
					'resources' => 'modResource',
				];
				$this->modelPath = MODX_BASE_PATH . $this->Easypack->getProperty('core') . '/model/';
				$this->modx->loadClass('transport.modPackageBuilder', '', FALSE, TRUE);
				if (class_exists('modPackageBuilder')) {
					$this->builder = new modPackageBuilder($this->modx);
					if (!$this->builder or !($this->builder instanceof modPackageBuilder)) {
						return $this->failure('modPackageBuilder not found', ['line' => __LINE__]);
					}
				} else {
					return $this->failure('modPackageBuilder not found', ['line' => __LINE__]);
				}
				$otherProps = [
					'processors_path' => MODX_CORE_PATH . 'components/easypack/processors/',
				];
				$processorProps = [
					'id' => $id,
					//'create__model_' => 1,
					'create__namespace_' => 1,
					'create__elements_' => 1,
					'create__docs_' => 1,
				];

				$res = $this->modx->runProcessor('mgr/build/create', $processorProps, $otherProps);
				if (!$res->response['success']) {
					throw new RuntimeException($res->response['message']);
				}

				if ($this->Easypack->getProperty('core')) {
					$corePath = MODX_BASE_PATH . $this->Easypack->getProperty('core');
					if (!is_dir($corePath)) {
						throw new RuntimeException($this->modx->lexicon('core_no_created', ['path' => $corePath]));
					}
					if (!is_writable($corePath)) {
						throw new RuntimeException($this->modx->lexicon('core_no_writable', ['path' => $corePath]));
					}
				} else {
					throw new RuntimeException($this->modx->lexicon('core_no_define'));
				}

				$this->packName = $this->PKG_NAME_LOWER . '-' . $this->PKG_VERSION . '-' . $this->PKG_RELEASE;
				return $this->build();
			} catch (Exception $e) {
				return $this->failure($e->getMessage());
			}
		}

		public function menus($menus, $UPDATE_OBJECT = TRUE)
		{
			/* start create menu vehicle */
			foreach ($menus as $menuId) {
				$menuVehicle = NULL;
				$menu = $this->modx->getObject('modMenu', ['text' => $menuId]);
				if ($menu) {
					$menuVehicle = $this->builder->createVehicle($menu, [
						xPDOTransport::PRESERVE_KEYS => TRUE,
						xPDOTransport::UPDATE_OBJECT => $UPDATE_OBJECT,
						xPDOTransport::UNIQUE_KEY => 'text',
						xPDOTransport::RELATED_OBJECTS => TRUE,
						xPDOTransport::RELATED_OBJECT_ATTRIBUTES => [
							'Action' => [
								xPDOTransport::PRESERVE_KEYS => FALSE,
								xPDOTransport::UPDATE_OBJECT => TRUE,
								xPDOTransport::UNIQUE_KEY => ['namespace', 'controller'],
							],
						],
					]);
					$this->builder->putVehicle($menuVehicle);
				}
			}
			/* end create menu vehicle */
		}

		public function plugins($plugins, $UPDATE_OBJECT = TRUE)
		{
			/* start create plugin vehicle */
			$plugins_ = [];
			$attr = [
				xPDOTransport::UNIQUE_KEY => 'name',
				xPDOTransport::PRESERVE_KEYS => FALSE,
				xPDOTransport::UPDATE_OBJECT => $UPDATE_OBJECT,
				xPDOTransport::RELATED_OBJECTS => TRUE,
				xPDOTransport::RELATED_OBJECT_ATTRIBUTES => [
					'PluginEvents' => [
						xPDOTransport::PRESERVE_KEYS => TRUE,
						xPDOTransport::UPDATE_OBJECT => TRUE,
						xPDOTransport::UNIQUE_KEY => ['pluginid', 'event'],
					],
				],
			];
			foreach ($plugins as $pluginId) {
				if ($pluginId) {
					$pluginVehicle = NULL;
					/** @var modPlugin $plugin */
					$plugin = $this->modx->getObject('modPlugin', ['name' => $pluginId]);
					if ($plugin) {
						/** @var modPluginEvent $pluginEvents */
						$pluginEvents = $plugin->getMany('PluginEvents');
						$pluginData = $plugin->toArray();
						unset($pluginData['id']);
						unset($pluginData['category']);
						$plugin_ = $this->modx->newObject('modPlugin');
						/** @var modPlugin $plugin_ */
						$plugin_->fromArray($pluginData);
						$plugin_->addMany($pluginEvents);
						//$pluginVehicle = $this->builder->createVehicle($plugin_, $attr);
						//$this->builder->putVehicle($pluginVehicle);
						$plugins_[] = $plugin_;
					}
				}
			}
			if (!empty($plugins_)) {
				$this->category_attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Plugins'] = $attr;
				$this->category->addMany($plugins_);
			}
			/* end create plugin vehicle */
		}

		public function snippets($snippets, $UPDATE_OBJECT = TRUE)
		{
			/* start create snippet vehicle */
			$snippets_ = [];
			$attr = [
				xPDOTransport::PRESERVE_KEYS => FALSE,
				xPDOTransport::UPDATE_OBJECT => $UPDATE_OBJECT,
				xPDOTransport::UNIQUE_KEY => 'name',
			];
			foreach ($snippets as $snippetId) {
				if ($snippetId) {
					$snippetVehicle = NULL;
					$snippet = $this->modx->getObject('modSnippet', ['name' => $snippetId]);
					if ($snippet) {
						$snippetData = $snippet->toArray();
						unset($snippetData['id']);
						unset($snippetData['category']);
						$snippet = $this->modx->newObject('modSnippet');
						$snippet->fromArray($snippetData);
						$snippets_[] = $snippet;
					}
				}
			}
			if (!empty($snippets_)) {
				$this->category->addMany($snippets_);
				$this->category_attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Snippets'] = $attr;
			}

			/* end create snippet vehicle */
		}

		public function chunks($chunks, $UPDATE_OBJECT = TRUE)
		{
			/* start create chunk vehicle */
			$chunks_ = [];
			$attr = [
				xPDOTransport::PRESERVE_KEYS => FALSE,
				xPDOTransport::UPDATE_OBJECT => $UPDATE_OBJECT,
				xPDOTransport::UNIQUE_KEY => 'name',
			];
			foreach ($chunks as $chunkId) {
				if ($chunkId) {
					$chunk = $this->modx->getObject('modChunk', ['name' => $chunkId]);
					if ($chunk) {
						$chunkData = $chunk->toArray();
						unset($chunkData['id']);
						unset($chunkData['category']);
						$chunk = $this->modx->newObject('modChunk');
						$chunk->fromArray($chunkData);

						$chunks_[] = $chunk;
					}
				}
			}
			if (!empty($chunks_)) {
				$this->category->addMany($chunks_);
				$this->category_attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Chunks'] = $attr;

			}
			/* end create chunk vehicle */
		}

		public function templates($templates, $UPDATE_OBJECT = TRUE)
		{
			/* start create template vehicle */
			$templates_ = [];
			$attr = [
				xPDOTransport::PRESERVE_KEYS => TRUE,
				xPDOTransport::UPDATE_OBJECT => $UPDATE_OBJECT,
				xPDOTransport::UNIQUE_KEY => ['id', 'templatename'],
				xPDOTransport::RELATED_OBJECTS => TRUE,
				xPDOTransport::RELATED_OBJECT_ATTRIBUTES => [
					'modTemplateVar' => [
						xPDOTransport::PRESERVE_KEYS => TRUE,
						xPDOTransport::UPDATE_OBJECT => TRUE,
						xPDOTransport::UNIQUE_KEY => ['id', 'name'],
					],
				],
			];
			foreach ($templates as $templateId) {
				if ($templateId) {
					/** @var modTemplate $template */
					$template = $this->modx->getObject('modTemplate', ['templatename' => $templateId]);
					if ($template) {
						$templates_[] = $template;

						$c = $this->modx->newQuery('modTemplateVar');
						$c->innerJoin('modTemplateVarTemplate', 'TemplateVarTemplates');
						$c->where([
							'TemplateVarTemplates.templateid' => $template->get('id'),
						]);
						$tvList = $this->modx->getCollection('modTemplateVar', $c);
						foreach ($tvList as $tv) {
							/** @var modTemplateVar $tv */
							$tvs[] = $tv; /* only add TV once */
							$modTemplateVarTemplate = $this->modx->newObject('modTemplateVarTemplate');
							$modTemplateVarTemplate->set('tmplvarid', $tv->get('id'));
							$modTemplateVarTemplate->set('templateid', $template->get('id'));
							$vehicle = $this->builder->createVehicle($modTemplateVarTemplate, [
								xPDOTransport::PRESERVE_KEYS => TRUE,
								xPDOTransport::UPDATE_OBJECT => FALSE,
								xPDOTransport::UNIQUE_KEY => ['tmplvarid', 'templateid'],
								xPDOTransport::RELATED_OBJECTS => FALSE,
							]);
							$this->builder->putVehicle($vehicle);
						}
					}
				}
			}
			if (!empty($templates_)) {
				$this->category->addMany($templates_);
				$this->category_attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Templates'] = $attr;
				if (!empty($tvs)) {
					$this->category->addMany($tvs);
				}
			}
			/* end create template vehicle */
		}

		public function resources($resources, $UPDATE_OBJECT = TRUE)
		{
			/* start create resource vehicle */
			$templates_ = [];
			$attr = [
				xPDOTransport::UNIQUE_KEY => 'id',
				xPDOTransport::PRESERVE_KEYS => TRUE,
				xPDOTransport::UPDATE_OBJECT => $UPDATE_OBJECT,
				xPDOTransport::RELATED_OBJECTS => TRUE,
				xPDOTransport::RELATED_OBJECT_ATTRIBUTES => [
					'modTemplateVarTemplate' => [
						xPDOTransport::PRESERVE_KEYS => TRUE,
						xPDOTransport::UPDATE_OBJECT => FALSE,
						xPDOTransport::UNIQUE_KEY => ['contentid', 'tmplvarid'],
					],
				],
			];
			foreach ($resources as $resourceId) {
				if ($resourceId) {
					$resource = $this->modx->getObject('modResource', ['id' => $resourceId]);
					if ($resource) {
						$TemplateVarResources = $resource->getMany('TemplateVarResources');
						/** @var modResource $resource */
						$resource->addMany($TemplateVarResources);

						$vehicle = $this->builder->createVehicle($resource, $attr);
						$this->builder->putVehicle($vehicle);
					}
				}
			}

		}

		public function settings($settings, $UPDATE_OBJECT = FALSE)
		{
			/* start create setting vehicle */
			$attr = [
				xPDOTransport::UNIQUE_KEY => 'key',
				xPDOTransport::PRESERVE_KEYS => TRUE,
				xPDOTransport::UPDATE_OBJECT => $UPDATE_OBJECT,
				xPDOTransport::RELATED_OBJECTS => FALSE,
			];
			foreach ($settings as $settingId) {
				if ($settingId) {
					$settingVehicle = NULL;
					$setting = $this->modx->getObject('modSystemSetting', ['key' => $settingId]);
					if ($setting) {
						/** @var modSystemSetting $setting_ */
						$settingData = $setting->toArray();
						if (mb_strtolower($settingData['namespace']) == $this->PKG_NAME_LOWER) {
							$attr[xPDOTransport::UPDATE_OBJECT] = $UPDATE_OBJECT;
						} else {
							$attr[xPDOTransport::UPDATE_OBJECT] = TRUE;
						}

						$setting_ = $this->modx->newObject('modSystemSetting');
						unset($settingData['editedon']);
						$setting_->fromArray($settingData, '', TRUE, TRUE);
						$this->_settingLex($settingData);
						$vehicle = $this->builder->createVehicle($setting_, $attr);
						$this->builder->putVehicle($vehicle);
					}
				}
			}
			/* end create setting vehicle */
		}

		public function _settingLex($settingData)
		{
			$_lang = NULL;
			$namespace = $settingData['namespace'];
			if ($namespace == $this->PKG_NAME_LOWER) {
				$key = $settingData['key'];
				$area = $settingData['area'];
				$lang = $this->modx->config['manager_language'];

				$core = $this->Easypack->getProperty('core');
				$langPath = MODX_BASE_PATH . $core . '/lexicon/' . $lang . '/setting.inc.php';
				if (file_exists($langPath)) {
					include $langPath;
					if ($_lang) {
						if (!isset($_lang['setting_' . $key])) {
							$this->_writeLang($langPath, $key, $lang);
						}
						if (!isset($_lang['area_' . $area])) {
							$this->_writeLangArea($langPath, $area, $lang);
						}
					}
					return FALSE;
				} else {
					$this->_writeLang($langPath, $key, $lang);
					$this->_writeLangArea($langPath, $area, $lang);
				}
			}
			return FALSE;
		}

		public function _writeLang($langPath, $key, $lang)
		{
			$_lang = NULL;
			$txt = NULL;
			$k1 = 'setting_' . $key;
			$k2 = 'setting_' . $key . '_desc';

			$coreLangPath = MODX_CORE_PATH . 'lexicon/' . $lang . '/setting.inc.php';
			$permissions = $this->modx->config['new_file_permissions'] ?: 0777;
			$prefix = $this->modx->config['table_prefix'];
			$_lang = $this->modx->query("SELECT name, `value` FROM {$prefix}lexicon_entries WHERE name in('{$k1}','{$k2}')");
			if ($_lang) {
				$_lang = $_lang->fetchAll(PDO::FETCH_KEY_PAIR);
			} else {
				if (file_exists($coreLangPath)) {
					include $coreLangPath;
				} else {
					return FALSE;
				}
			}
			if (isset($_lang[$k1])) {
				$lex = $_lang['setting_' . $key];
				$txt .= "\n" . '$_lang[\'setting_' . $key . '\'] = \'' . $lex . '\';';

			}
			if (isset($_lang[$k2])) {
				$lex = $_lang['setting_' . $key . '_desc'];
				$txt .= "\n" . '$_lang[\'setting_' . $key . '_desc\'] = \'' . $lex . '\';';
			}
			if ($txt) {
				if (!@mkdir($concurrentDirectory = dirname($langPath), $permissions, 1) && !is_dir($concurrentDirectory)) {
					throw new Exception($this->modx->lexicon('failCreate_folder', ['path' => $concurrentDirectory]));
				}
				if (!file_exists($langPath)) {
					@file_put_contents($langPath, "<?php" . "\n" . $txt);
				} else {
					@file_put_contents($langPath, $txt, FILE_APPEND);
				}
			}
		}

		public function _writeLangArea($langPath, $area, $lang)
		{
			$_lang = NULL;
			$coreLangPath = MODX_CORE_PATH . 'lexicon/' . $lang . '/setting.inc.php';
			$prefix = (string)$this->modx->config['table_prefix'];
			$permissions = $this->modx->config['new_file_permissions'] ?: 0777;
			$_lang = $this->modx->query("SELECT name, `value` FROM {$prefix}lexicon_entries WHERE `name` LIKE '%{$area}%'");
			if ($_lang) {
				$_lang = $_lang->fetchAll(PDO::FETCH_KEY_PAIR);
			} else {
				if (file_exists($coreLangPath)) {
					include $coreLangPath;
				} else {
					return FALSE;
				}
			}
			if (isset($_lang['area_' . $area])) {
				$lex = $_lang['area_' . $area];
			} else {
				$lex = $area;
			}
			if ($lex and $area) {
				$txt = "\n" . '$_lang[\'area_' . $area . '\'] = \'' . $lex . '\';';
				if (!mkdir($concurrentDirectory = dirname($langPath), $permissions, 1) && !is_dir($concurrentDirectory)) {
					throw new Exception($this->modx->lexicon('failCreate_folder', ['path' => $concurrentDirectory]));
				}
				@file_put_contents($langPath, $txt, FILE_APPEND);
			}
		}

		public function _addElements()
		{
			foreach ($this->classes as $key => $cls) {
				$_tmp = $this->Easypack->getProperty($key, FALSE);
				if ($_tmp !== FALSE and !empty($_tmp)) {
					$_tmp = @json_decode($_tmp);
				}
				if (is_array($_tmp)) {
					if (method_exists($this, $key)) {
						$this->$key($_tmp);
					}
				}
			}
		}

		public function _GenResolver($categoryVehicle)
		{
			$xml_schema_file = $this->modelPath . $this->PKG_NAME_LOWER . '/' . $this->PKG_NAME_LOWER . '.mysql.schema.xml';
			if (file_exists($xml_schema_file)) {
				$SXE = new SimpleXMLElement($xml_schema_file, 0, TRUE);
				$classes = [];
				if (isset($SXE->object)) {
					foreach ($SXE->object as $object) {
						$classes[] = (string)$object['class'];
					}
				}
				if (!empty($classes)) {
					$code = [];
					foreach ($classes as $cls) {
						$code[] = '$manager->createObjectContainer(\'' . $cls . '\');';
					}
					$code = implode("\n", $code);
					$PKG_NAME_LOWER = $this->PKG_NAME_LOWER;
					$txt = include MODX_CORE_PATH . 'components/easypack/processors/mgr/build/examples/resolver.php';
					$this->tmp_resolver = tempnam(sys_get_temp_dir(), $this->PKG_NAME_LOWER . '_resolver_');
					if (!file_exists($this->tmp_resolver)) {
						$this->tmp_resolver = MODX_BASE_PATH . $this->Easypack->getProperty('core') . '/tmp' . $this->PKG_NAME_LOWER . '.resolver';
						if (!mkdir($concurrentDirectory = dirname($this->tmp_resolver), 0777, 1) && !is_dir($concurrentDirectory)) {
							$this->modx->log(MODX_LOG_LEVEL_ERROR, sprintf('Directory "%s" was not created', $concurrentDirectory));
						}
					}
					file_put_contents($this->tmp_resolver, $txt);
					if (!file_exists($this->tmp_resolver)) {
						$this->modx->log(MODX_LOG_LEVEL_ERROR, sprintf('resolver "%s" was not created', $this->tmp_resolver));
					}
					$categoryVehicle->resolve('php', [
						'source' => $this->tmp_resolver,
					]);
				}
			}
		}

		public function _addReqExtra($categoryVehicle)
		{
			$packages = json_encode($this->reqExtras, 256);
			$txt = include MODX_CORE_PATH . 'components/easypack/processors/mgr/build/examples/extras.download.php';
			$this->tmp_resolver = tempnam(sys_get_temp_dir(), $this->PKG_NAME_LOWER . '_resolver_');
			if (!file_exists($this->tmp_resolver)) {
				$this->tmp_resolver = MODX_BASE_PATH . $this->Easypack->getProperty('core') . '/tmp' . $this->PKG_NAME_LOWER . '.resolver';
				if (!mkdir($concurrentDirectory = dirname($this->tmp_resolver), 0777, 1) && !is_dir($concurrentDirectory)) {
					$this->modx->log(MODX_LOG_LEVEL_ERROR, sprintf('Directory "%s" was not created', $concurrentDirectory));
				}
			}
			file_put_contents($this->tmp_resolver, $txt);
			if (!file_exists($this->tmp_resolver)) {
				$this->modx->log(MODX_LOG_LEVEL_ERROR, sprintf('resolver "%s" was not created', $this->tmp_resolver));
			}
			$categoryVehicle->resolve('php', [
				'source' => $this->tmp_resolver,
			]);
			$this->modx->getVersionData();
			foreach ($this->reqExtras as $packageName => $options) {

				if (!empty($options["service_url"])) {
					$provider = $this->modx->getObject("transport.modTransportProvider", [
						"service_url:LIKE" => "%" . $options["service_url"] . "%",
					]);
				}
				if (empty($provider)) {
					$provider = $this->modx->getObject("transport.modTransportProvider", 1);
				}
				$productVersion = $this->modx->version["code_name"] . "-" . $this->modx->version["full_version"];

				$response = $provider->request("package", "GET", [
					"supports" => $productVersion,
					"query" => $packageName,
				]);
				if (empty($response)) {
					$this->modx->log(MODX_LOG_LEVEL_WARN, 'can`t download "' . $packageName . '" ');
				}
				unset($provider);
				unset($response);
			}
		}

		public function _addModUtilRest($UPDATE_OBJECT = FALSE)
		{
			/* start create REST vehicle */
			$rests = $this->Easypack->getRest();
			if (empty($rests)) {
				return FALSE;
			}
			$attr = [
				xPDOTransport::PRESERVE_KEYS => TRUE,
				xPDOTransport::UPDATE_OBJECT => $UPDATE_OBJECT,
				'package_path' => 'components/modutilities/model/',
				'package' => 'modutilities',
				'class' => 'Utilrest',
				xPDOTransport::UNIQUE_KEY => ['url'],
			];
			$attr2 = [
				xPDOTransport::PRESERVE_KEYS => TRUE,
				xPDOTransport::UPDATE_OBJECT => $UPDATE_OBJECT,
				'package_path' => 'components/modutilities/model/',
				'package' => 'modutilities',
				'class' => 'Utilrestcategory',
				xPDOTransport::UNIQUE_KEY => ['id'],
			];
			$this->modx->addPackage('modutilities', MODX_CORE_PATH . 'components/modutilities/model/');
			$UtilRestCategory = $this->modx->newObject('Utilrestcategory');
			$cat = rand(500, 10000);
			$UtilRestCategory->set('id', $cat);
			$UtilRestCategory->set('name', $this->PKG_NAME);
			$vehicle2 = $this->builder->createVehicle($UtilRestCategory, $attr2);
			$this->builder->putVehicle($vehicle2);
			foreach ($rests as $restId) {
				if ($restId) {
					$rest = $this->modx->getObject('Utilrest', ['url' => $restId]);
					if ($rest) {
						$rest->set('category', $cat);
						$vehicle = $this->builder->createVehicle($rest, $attr);
						$this->builder->putVehicle($vehicle);
					}
				}

			}
			/* end create REST vehicle */
		}

		public function build()
		{
			try {
				$this->builder->createPackage($this->PKG_NAME_LOWER, $this->PKG_VERSION, $this->PKG_RELEASE);
				$this->builder->registerNamespace($this->PKG_NAME_LOWER, FALSE, TRUE, '{core_path}components/' . $this->PKG_NAME_LOWER . '/', '{assets_path}components/' . $this->PKG_NAME_LOWER . '/');
				/* create category */
				$this->category = $this->modx->newObject('modCategory');
				$this->category->set('id', NULL);
				$this->category->set('category', $this->PKG_NAME);
				$this->category_attr = [
					xPDOTransport::UNIQUE_KEY => 'category',
					xPDOTransport::PRESERVE_KEYS => FALSE,
					xPDOTransport::UPDATE_OBJECT => TRUE,
					xPDOTransport::RELATED_OBJECTS => TRUE,
				];

				$this->_addModUtilRest();

				$this->_addElements();

				$packageAttributes = [];
				if ($this->Easypack->getProperty('license') and file_exists(MODX_BASE_PATH . $this->Easypack->getProperty('license'))) {
					$packageAttributes['license'] = file_get_contents(MODX_BASE_PATH . $this->Easypack->getProperty('license'));
				}
				if ($this->Easypack->getProperty('readme') and file_exists(MODX_BASE_PATH . $this->Easypack->getProperty('readme'))) {
					$packageAttributes['readme'] = file_get_contents(MODX_BASE_PATH . $this->Easypack->getProperty('readme'));
				}
				if ($this->Easypack->getProperty('changelog') and file_exists(MODX_BASE_PATH . $this->Easypack->getProperty('changelog'))) {
					$packageAttributes['changelog'] = file_get_contents(MODX_BASE_PATH . $this->Easypack->getProperty('changelog'));
				}
				if ($this->Easypack->getProperty('setup_option') and file_exists(MODX_BASE_PATH . $this->Easypack->getProperty('setup_option'))) {
					$packageAttributes['setup-options']['source'] = MODX_BASE_PATH . $this->Easypack->getProperty('setup_option');
				}
				if ($this->Easypack->getProperty('requires')) {
					$requires = $this->Easypack->getRequires();
					if (isset($requires['extras'])) {
						$this->reqExtras = $this->Easypack->getRequiresExtras();
						unset($requires['extras']);
					}
					if ($requires and is_array($requires)) {
						$packageAttributes['requires'] = $requires;
					}
				}

				if (!empty($packageAttributes)) {
					$this->builder->setPackageAttributes($packageAttributes);
				}


				/* start create category vehicle */

				$categoryVehicle = $this->builder->createVehicle($this->category, $this->category_attr);
				if ($this->Easypack->getProperty('assets')) {
					$categoryVehicle->resolve('file', [
						'source' => MODX_BASE_PATH . $this->Easypack->getProperty('assets'),
						'target' => "return MODX_ASSETS_PATH . 'components/';",
					]);
				}
				$categoryVehicle->resolve('file', [
					'source' => MODX_BASE_PATH . $this->Easypack->getProperty('core'),
					'target' => "return MODX_CORE_PATH . 'components/';",
				]);

				if ($this->Easypack->getProperty('php_resolver')) {
					$categoryVehicle->resolve('php', [
						'source' => MODX_BASE_PATH . $this->Easypack->getProperty('php_resolver'),
					]);
				} else {
					$this->_GenResolver($categoryVehicle);
				}

				$this->_addReqExtra($categoryVehicle);

				$this->builder->putVehicle($categoryVehicle);
				/* end create category vehicle */

				$path = $this->modx->getOption('core_path') . 'packages/' . $this->builder->signature . '.transport.zip';
				if (file_exists($path)) {
					unlink($path);
					unlink($this->modx->getOption('core_path') . 'packages/' . $this->builder->signature);
				}
				$test = $this->builder->pack();

				$this->tend = microtime(TRUE);
				$this->time = " <strong>$this->PKG_NAME</strong> time: <strong>".round(($this->tend - $this->tstart) * 1000) . '</strong> ms';
				if ($this->tmp_resolver) {
					unlink($this->tmp_resolver);
				}
				if ($test) {
					$newName = MODX_BASE_PATH . $this->classKey . '/' . basename($path);
					if (!is_dir($concurrentDirectory = MODX_BASE_PATH . '/' . $this->classKey)) {
						if (!mkdir($concurrentDirectory) && !is_dir($concurrentDirectory)) {
							throw new Exception(sprintf('Directory "%s" was not created', $concurrentDirectory));
						}
					}
					if (file_exists($newName)) {
						unlink($newName);
					}
					if (file_exists($path)) {
						if (!@symlink($path, $newName)) {
							if (!@link($path, $newName)) {
								throw new Exception('can`t create symlink "' . $path . '"');
							}
						}
					} else {
						return $this->failure('error', [
							'time' => $this->time,
							'path' => $path,
							'line' => __LINE__,
						]);
					}
					if (file_exists($newName)) {
						$url = str_replace(MODX_BASE_PATH, $this->modx->config['base_url'], $newName);
						$this->Easypack->set('path_to_last_transport', $url);
						$this->Easypack->save();
						return $this->success('ok', [
							'time' => $this->time,
							'path' => $url,
						]);
					} else {
						throw new Exception('can`t create ' . $newName);
					}
				} else {
					throw new Exception('can`t create package');
				}
			} catch (Exception $e) {
				$this->modx->log(MODX_LOG_LEVEL_ERROR, $e->getMessage(), '', '', $e->getFile(), $e->getLine());
				return $this->failure($e->getMessage(), [
					'time' => $e->getFile(),
					'path' => $e->getCode(),
					'line' => $e->getLine(),
				]);
			}
		}

	}

	return 'EasypackBuildProcessor';