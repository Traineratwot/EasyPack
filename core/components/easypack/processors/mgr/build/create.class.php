<?php

	class EasypackBuildCreatProcessor extends modProcessor
	{
		public $classKey = 'EasypackExtras';
		public $directories = [];

		public $prefix = '';
		public $dbtype = '';
		public $tables = [];
		public $modelPath = '';
		public $PKG_NAME = '';
		public $PKG_NAME_LOWER = '';
		public $dbName = '';
		/**
		 * @var object|EasypackExtras $Easypack
		 */
		public $Easypack;
		/**
		 * @var float
		 */
		public $tend;
		/**
		 * @var float
		 */
		public $tstart;
		/**
		 * @var float
		 */
		public $time;
		/**
		 * @var string[]
		 */
		public $classes;
		public $permissions = 0777;

		public function process()
		{
			$this->permissions = $this->modx->getOption('new_file_permissions',null,'0777');
			try {
				$this->tstart = microtime(1);
				$id = $this->getProperty('id');
				if ($id) {
					$this->Easypack = $this->modx->getObject($this->classKey, $id);
					if (!$this->Easypack) {
						return $this->failure($this->classKey . ' object not found', ['$id' => $id, 'line' => __LINE__]);
					}
				} else {
					return $this->failure('id not found', ['line' => __LINE__]);
				}
				$this->PKG_NAME = $this->Easypack->getProperty('name', FALSE);
				$this->PKG_NAME_LOWER = str_replace([' ', '-', '.', '*', '!', '@', '#', '$', '%', '^', '&', '_'], '', mb_strtolower($this->PKG_NAME));
				$tables = $this->Easypack->getProperty('tables', FALSE);
				if ($tables) {
					$tables = json_decode($tables, 1);
					if (!is_array($tables['tables'])) {
						$this->tables = [];
						$this->prefix = '';
					} else {
						$this->prefix = isset($tables['prefix']) ? $tables['prefix'] : $this->modx->config['table_prefix'];
						$this->tables = $tables['tables'];
					}
				}
				$this->dbName = $this->modx->config['dbname'];
				$this->dbtype = $this->modx->config['dbtype'];
				$this->classes = [
					'plugins' => ['k' => 'name', 'name' => 'modPlugin', 'ext' => 'php', 'processor' => 'element/plugin/update'],
					'snippets' => ['k' => 'name', 'name' => 'modSnippet', 'ext' => 'php', 'processor' => 'element/snippet/update'],
					'chunks' => ['k' => 'name', 'name' => 'modChunk', 'ext' => 'tpl', 'processor' => 'element/chunk/update'],
					'templates' => ['k' => 'templatename', 'name' => 'modTemplate', 'ext' => 'tpl', 'processor' => 'element/template/update'],
					//'resources' => ['k' => 'id'          , 'name' => 'modResource', 'ext' => 'tpl', 'processor'=>'resource/update'],
				];
				$this->modelPath = MODX_BASE_PATH . $this->Easypack->get('core') . '/model/';

				$this->directories = [];
				if ((bool)$this->getProperty('create__js_mgr_')) {
					$this->directories['assets'] = MODX_ASSETS_PATH . 'components/' . $this->PKG_NAME_LOWER . '/';
					$this->directories['assets_sections'] = MODX_ASSETS_PATH . 'components/' . $this->PKG_NAME_LOWER . '/js/mgr/sections/';
					$this->directories['assets_widgets'] = MODX_ASSETS_PATH . 'components/' . $this->PKG_NAME_LOWER . '/js/mgr/widgets/';
				}
				if ((bool)$this->getProperty('create__controllers_mgr_')) {
					$this->directories['code_base'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/';
					$this->directories['controllers'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/controllers/';
					$this->directories['controllers_mgr'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/controllers/mgr/';

				}
				if ((bool)$this->getProperty('create__docs_')) {
					$this->directories['code_base'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/';
					$this->directories['docs'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/docs/';
				}
				if ((bool)$this->getProperty('create__elements_chunks_')) {
					$this->directories['code_base'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/';
					$this->directories['elements'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/elements/';
					$this->directories['chunks'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/elements/chunks/';

				}
				if ((bool)$this->getProperty('create__elements_plugins_')) {
					$this->directories['code_base'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/';
					$this->directories['elements'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/elements/';
					$this->directories['plugins'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/elements/plugins/';

				}
				if ((bool)$this->getProperty('create__elements_snippets_')) {
					$this->directories['code_base'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/';
					$this->directories['elements'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/elements/';
					$this->directories['snippets'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/elements/snippets/';

				}
				if ((bool)$this->getProperty('create__elements_templates_')) {
					$this->directories['code_base'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/';
					$this->directories['elements'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/elements/';
					$this->directories['templates'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/elements/templates/';

				}
				if ((bool)$this->getProperty('create__lexicon_en_')) {
					$this->directories['code_base'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/';
					$this->directories['lexicon'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/lexicon/';
					$this->directories['en'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/lexicon/en/';

				}
				if ((bool)$this->getProperty('create__processors_')) {
					$this->directories['code_base'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/';
					$this->directories['processors'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/processors/';
					$this->directories['processors_mgr'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/processors/mgr/';

				}
				if ((bool)$this->getProperty('create__model_')) {
					$this->directories['code_base'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/';
					$this->directories['model'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/model/';
					$this->directories['my_model'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/model/' . $this->PKG_NAME_LOWER . '/';
					$this->directories['mysql'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/model/' . $this->PKG_NAME_LOWER . '/' . $this->dbtype . '/';
					$this->directories['request'] = MODX_CORE_PATH . 'components/' . $this->PKG_NAME_LOWER . '/model/request/';
				}

				$this->generate();
				$this->tend = microtime(1);
				$this->time = round($this->tend - $this->tstart, 2);
				return $this->success('ok', [
					'time' => $this->time,
				]);
			} catch (Exception $e) {
				return $this->failure($e->getMessage());
			}


		}

		public function generate()
		{
			foreach ($this->directories as $key => $value) {
				if (!is_dir($value)) {
					if (!@mkdir($value, 0777, 1) && !is_dir($value)) {
						throw new RuntimeException($this->modx->lexicon('failCreate_folder', ['path' => $value]));
					}
					if (!is_writable($value)) {
						throw new RuntimeException($this->modx->lexicon('no_writable', ['path' => $value]));
					}
				}
			}

			if (isset($this->directories['assets'])) {
				$assets = trim(str_replace(MODX_BASE_PATH, '', $this->directories['assets']), '/');
				$this->Easypack->set('assets', $assets);

			}
			if (isset($this->directories['code_base'])) {
				$core = trim(str_replace(MODX_BASE_PATH, '', $this->directories['code_base']), '/');
				$this->Easypack->set('core', $core);

			}
			if ($this->Easypack->isDirty('core') or $this->Easypack->isDirty('assets')) {
				$this->Easypack->save();
			}


			$q = "select id from {$this->prefix}categories where `category` LIKE '{$this->PKG_NAME}' OR `category` LIKE '{$this->PKG_NAME_LOWER}'";
			$categoryId = $this->modx->query($q);
			if ($categoryId) {
				$categoryId = (int)$categoryId->fetch(PDO::FETCH_COLUMN);
				if ($categoryId == 0) {
					$cat = $this->modx->newObject('modCategory');
					$cat->set('category', $this->PKG_NAME);
					$cat->save();
				}
			}

			$this->_prepareResources();
			if ((bool)$this->getProperty('create__model_') and !empty($this->tables)) {
				$this->_GenShema();
			}
			if ((bool)$this->getProperty('create__js_mgr_')) {
				$this->_GenConnector();
			}
			if ((bool)$this->getProperty('create__lexicon_en_')) {
				$this->_GenLexicon();
			}
			if ((bool)$this->getProperty('create__docs_')) {
				$this->_GenDocs();
			}
			if ((bool)$this->getProperty('create__namespace_')) {
				$this->_GenNamespace();
			}
			if ((bool)$this->getProperty('create__elements_')) {
				$this->_addElements();
			}
			if ((bool)$this->getProperty('import_from_category')) {
				$this->_import_from_category();
			}
			$this->_addDependence();

		}

		public function _prepareResources()
		{
			$resources = $this->Easypack->getResources();
			if (is_array($resources) and !empty($resources)) {
				$r = $this->modx->newQuery('modResource');
				$r->select('template,pagetitle');
				$r->where([
					'id:in' => $resources,
				]);
				$resources = $this->modx->getIterator('modResource', $r);

				/** @var modResource $res */
				foreach ($resources as $res) {
					/** @var modTemplate $template */
					$template = $this->modx->getObject('modTemplate', $res->get('template'));
					$templates = $this->Easypack->getTemplates();
					if (is_array($templates) and !empty($templates)) {
						$templates[] = $template->get('templatename');
						$templates = array_unique($templates);
					} else {
						$templates = [$template->get('templatename')];
					}
					$this->Easypack->set('templates', $templates);

				}
				if ($this->Easypack->isDirty('templates')) {
					$this->Easypack->save();

				}
			}
		}

		public function _GenConnector()
		{
			if (!file_exists($this->directories['assets'] . 'connector.php')) {
				$PKG_NAME_LOWER = $this->PKG_NAME_LOWER;
				$txt = include MODX_CORE_PATH . 'components/easypack/processors/mgr/build/examples/assets.connector.php';
				if ($txt) {
					@file_put_contents($this->directories['assets'] . 'connector.php', $txt);
				}
			}
		}

		public function _GenLexicon()
		{
			if (!file_exists($this->directories['en'] . 'default.inc.php')) {
				$PKG_NAME_LOWER = $this->PKG_NAME_LOWER;
				$PKG_NAME = $this->PKG_NAME;
				$txt = include MODX_CORE_PATH . 'components/easypack/processors/mgr/build/examples/core.lexicon.php';
				if ($txt) {
					@file_put_contents($this->directories['en'] . 'default.inc.php', $txt);
				}
			}
			$_tmp = $this->Easypack->getProperty('settings', FALSE);
			if ($_tmp !== FALSE and !empty($_tmp)) {
				$_tmp = @json_decode($_tmp);
			}
			if (is_array($_tmp)) {
				foreach ($_tmp as $settingId) {
					if ($settingId) {
						$settingVehicle = NULL;
						/** @var modSystemSetting $setting */
						$setting = $this->modx->getObject('modSystemSetting', ['key' => $settingId]);
						$this->_settingLex($setting->toArray());
					}
				}
			}

		}

		public function _GenDocs()
		{
			if (!file_exists($this->directories['docs'] . 'readme.md')) {
				@file_put_contents($this->directories['docs'] . 'readme.md', '');
			}
			if (!file_exists($this->directories['docs'] . 'License.txt')) {
				@file_put_contents($this->directories['docs'] . 'License.txt', '');
			}
			if (!file_exists($this->directories['docs'] . 'changelog.md')) {
				@file_put_contents($this->directories['docs'] . 'changelog.md', '');
			}
		}

		public function _GenShema()
		{
			try {
				$manager = $this->modx->getManager();
				$dbType = $this->dbtype;
				if (!class_exists('my_xPDOGenerator_' . $dbType)) {
					include(MODX_CORE_PATH . 'components/easypack/model/my_xpdogenerator.class.php');
				}

				if (class_exists('my_xPDOGenerator_' . $dbType)) {
					$generatorClass = 'my_xPDOGenerator_' . $dbType;
					/** @var my_xPDOGenerator_mysql $generator */
					$generator = new $generatorClass($manager);

					// set the allowed tables:
					if (empty($this->tables)) {
						throw new Exception('table not set');
					}
					$generator->setAllowedTables($this->tables);

					$xml_schema_file = $this->modelPath . $this->PKG_NAME_LOWER . '/' . $this->PKG_NAME_LOWER . '.mysql.schema.xml';
					// (re)Build the schema file
					// echo 'Scheme: '.$cmp->get('build_scheme');
					// set the db:

					$generator->setDatabase($this->dbName);
					$restrict_prefix = TRUE;
					if (!empty($this->dbName) && empty($this->prefix)) {
						$restrict_prefix = FALSE;
					}
					// now generate the scheme
					if (file_exists($xml_schema_file)) {
						unlink($xml_schema_file);
					}
					$xml = $generator->writeTableSchema($xml_schema_file, $this->PKG_NAME_LOWER, 'xPDOObject', $this->prefix, $restrict_prefix);
					if ($xml and file_exists($xml_schema_file)) {
						$generator->parseSchema($xml_schema_file, $this->directories['model']);
					} else {
						throw new Exception('can`t create xml sheme');
					}
				} else {
					throw new Exception('class ' . 'my_xPDOGenerator_' . $dbType . ' not found');
				}
			} catch (Exception $e) {
				$this->modx->log(MODX_LOG_LEVEL_ERROR, $e->getMessage(), '', '', $e->getFile(), $e->getLine());
			}

		}

		public function _GenNamespace()
		{
			/** @var modNamespace $Namespace */
			$c = $this->modx->getCount('modNamespace', ['name' => $this->PKG_NAME_LOWER]);
			if ($c === 0) {
				$Namespace = $this->modx->newObject('modNamespace');
				$Namespace->set('name', $this->PKG_NAME_LOWER);
				$Namespace->set('path', '{core_path}components/' . $this->PKG_NAME_LOWER . '/');
				$Namespace->set('assets_path', '{assets_path}components/' . $this->PKG_NAME_LOWER . '/');
				$Namespace->save();
			}
		}

		public function _addElements()
		{
			if (is_array($this->classes)) {
				foreach ($this->classes as $key => $cls) {
					$_tmp = $this->Easypack->getProperty($key, FALSE);
					if ($_tmp !== FALSE and !empty($_tmp)) {
						$_tmp = @json_decode($_tmp);
					}
					if (is_array($_tmp)) {
						foreach ($_tmp as $name) {
							$this->saveElement($name, $key);
						}
					}
				}
			}
		}

		public function saveElement($name, $class)
		{
			$className = $this->classes[$class]['name'];
			/** @var modChunk $object */
			$object = $this->modx->getObject($className, [$this->classes[$class]['k'] => $name]);
			if ($object) {
				if ((bool)$object->get('isStatic')) {

				} else {
					$newPath = $this->Easypack->getProperty('core') . '/elements/' . $class . '/' . $name . '.' . $this->classes[$class]['ext'];
					$param = $object->toArray();
					$param['static'] = $class == 'plugins' ? 0 : 1;
					$param['static_file'] = $newPath;
					$this->modx->runProcessor($this->classes[$class]['processor'], $param);

				}
			}
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
				$txt .= '$_lang[\'setting_' . $key . '\'] = \'' . $lex . '\';' . "\n";

			}
			if (isset($_lang[$k2])) {
				$lex = $_lang['setting_' . $key . '_desc'];
				$txt .= '$_lang[\'setting_' . $key . '_desc\'] = \'' . $lex . '\';' . "\n";
			}
			if ($txt) {
				if (!@mkdir($concurrentDirectory = dirname($langPath), 0777, 1) && !is_dir($concurrentDirectory)) {
					throw new Exception($this->modx->lexicon('failCreate_folder', ['path' => $concurrentDirectory]));
				}
				if (!file_exists($langPath)) {
					@file_put_contents($langPath, "<?php" . "\n" . $txt, FILE_APPEND);
				} else {
					@file_put_contents($langPath, $txt, FILE_APPEND);
				}
			}
		}

		public function _writeLangArea($langPath, $area, $lang)
		{
			$_lang = NULL;
			$k1 = '';
			$coreLangPath = MODX_CORE_PATH . 'lexicon/' . $lang . '/setting.inc.php';
			$prefix = $this->modx->config['table_prefix'];

			$_lang = $this->modx->query("SELECT name, `value` FROM {$prefix}lexicon_entries WHERE `name` = '{$k1}'");
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
				$txt = '$_lang[\'area_' . $area . '\'] = \'' . $lex . '\';';
				if (!@mkdir($concurrentDirectory = dirname($langPath), 0777, 1) && !is_dir($concurrentDirectory)) {
					throw new Exception($this->modx->lexicon('failCreate_folder', ['path' => $concurrentDirectory]));
				}
				@file_put_contents($langPath, $txt, FILE_APPEND);
			}
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
			$requires = $this->Easypack->getProperty('requires', []);
			if ($requires) {
				$requires = json_decode($requires, 1);
			}
			$requires['extras'][$name] = $data;
			$this->Easypack->set('requires', $requires);
			$this->Easypack->save();
		}

		public function _import_from_category()
		{
			$PKG_NAME = $this->PKG_NAME;
			$PKG_NAME_LOWER = $this->PKG_NAME_LOWER;
			$classes = [
				'modSnippet' => 'snippets',
				'modChunk' => 'chunks',
				'modPlugin' => 'plugins',
			];
			$q = "select id from {$this->prefix}categories where `category` LIKE '{$PKG_NAME}' OR `category` LIKE '{$PKG_NAME_LOWER}'";
			$categoryId = $this->modx->query($q);
			if ($categoryId) {
				$categoryId = (int)$categoryId->fetch(PDO::FETCH_COLUMN);
				if ($categoryId > 0) {
					foreach ($classes as $class => $v) {
						$elems = $this->modx->getIterator($class, ['category' => $categoryId]);
						$e = $this->Easypack->getElem($v);
						foreach ($elems as $elem) {
							$name = $elem->get('name');
							$e[] = $name;
						}
						$e = array_unique($e);
						if (!empty($e)) {
							$this->Easypack->set($v, $e);
						}
					}
					$this->Easypack->save();
				}
			}

		}

	}

	return 'EasypackBuildCreatProcessor';