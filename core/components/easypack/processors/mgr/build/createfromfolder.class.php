<?php

	class EasypackBuildCreatFromFolderProcessor extends modProcessor
	{
		public $classKey = 'EasypackExtras';

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
		public $corePath;
		public $categoryId = FALSE;

		public function process()
		{
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
				$this->corePath = $this->Easypack->getProperty('core', FALSE);
				if (!$this->corePath) {
					return $this->failure('core not set', ['line' => __LINE__]);
				}
				$this->corePath = MODX_BASE_PATH . $this->corePath;
				$this->PKG_NAME_LOWER = str_replace([' ', '-', '.', '*', '!', '@', '#', '$', '%', '^', '&', '_'], '', mb_strtolower($this->PKG_NAME));
				$tables = $this->Easypack->getTables('tables', FALSE);
				if ($tables) {
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
				];
				$q = "select id from {$this->prefix}categories where `category` LIKE '{$this->PKG_NAME}' OR `category` LIKE '{$this->PKG_NAME_LOWER}'";
				$categoryId = $this->modx->query($q);
				if ($categoryId) {
					$categoryId = (int)$categoryId->fetch(PDO::FETCH_COLUMN);
					if ($categoryId > 0) {
						$this->categoryId = $categoryId;
					}
				}
				$this->generate();
				return $this->success('extraExt.html.success');
			} catch (Exception $e) {
				return $this->failure($e->getMessage());
			}
		}

		public function generate()
		{
			$directory = new RecursiveDirectoryIterator($this->corePath);
			$iterator = new RecursiveIteratorIterator($directory);
			$files = [];
			/** @var SplFileInfo &$element */
			foreach ($iterator as $element) {
				if ($element->isFile()) {
					if (substr($element->getBasename(), 0, 1) == '_') {
						continue;
					}

					$dirname = basename(dirname($element->getRealPath()));

					$_n = explode('.', $element->getBasename());
					array_pop($_n);
					$_n = implode('.', $_n);
					$element->name = $_n;

					switch ($dirname) {
						case "plugins":
						case "snippets":
						case "chunks":
						case "templates":
							$this->genElem($dirname, $element);
							break;
					}
				}
			}
			$this->Easypack->save();
		}

		public function genElem($dirname, SplFileInfo $element)
		{
			if ($this->checkElem($dirname, $element)) {
				$relativePath = str_replace(MODX_CORE_PATH, '', $element->getRealPath());
				switch ($dirname) {
					case "plugins":
						$this->genPlugins($relativePath, $element);
						break;
					case "snippets":
						$this->genSnippets($relativePath, $element);
						break;
					case "chunks":
						$this->genChunks($relativePath, $element);
						break;
					case "templates":
						$this->genTemplates($relativePath, $element);
						break;
				}
			}
		}

		public function genPlugins($relativePath, SplFileInfo $element)
		{
			$content = file_get_contents($element->getRealPath());
			$events = $this->pluginGetEvents($content);
			if (!empty($events)) {
				$pluginCode =
					<<<EOD
<?php
	\$path = MODX_CORE_PATH."$relativePath";
	if(file_exists(\$path)){
		return include \$path;
	}
EOD;
				$data = [
					'name' => $element->name,
					'plugincode' => $pluginCode,
					'description' => 'created by EasyPack',
					'events' => $events,
				];
				if ($this->categoryId) {
					$data['category'] = $this->categoryId;
				}
				$response = $this->modx->runProcessor('/element/plugin/create', $data);
				if (!$response->isError()) {
					$elems = $this->Easypack->getPlugins([]);
					$elems[] = $element->name;
					$elems = array_unique($elems);
					$this->Easypack->set('plugins', $elems);
				}
			}
		}

		public function genSnippets($relativePath, SplFileInfo $element)
		{
			$content = file_get_contents($element->getRealPath());
			$data = [
				'name' => $element->name,
				'snippet' => $content,
				'description' => 'created by EasyPack',
				'static_file' => $relativePath,
				'static' => TRUE,
			];
			if ($this->categoryId) {
				$data['category'] = $this->categoryId;
				}
			$response = $this->modx->runProcessor('/element/snippet/create', $data);
			if (!$response->isError()) {
				$elems = $this->Easypack->getSnippets([]);
				$elems[] = $element->name;
				$elems = array_unique($elems);
				$this->Easypack->set('snippets', $elems);
			}
		}

		public function genChunks($relativePath, SplFileInfo $element)
		{
			$content = file_get_contents($element->getRealPath());
			$data = [
				'name' => $element->name,
				'snippet' => $content,
				'description' => 'created by EasyPack',
				'static_file' => $relativePath,
				'static' => TRUE,
			];
			if ($this->categoryId) {
				$data['category'] = $this->categoryId;
				}
			$response = $this->modx->runProcessor('/element/chunk/create', $data);
			if (!$response->isError()) {
				$elems = $this->Easypack->getChunks([]);
				$elems[] = $element->name;
				$elems = array_unique($elems);
				$this->Easypack->set('chunks', $elems);
			}
		}

		public function genTemplates($relativePath, SplFileInfo $element)
		{
			$content = file_get_contents($element->getRealPath());
			$data = [
				'templatename' => $element->name,
				'content' => $content,
				'description' => 'created by EasyPack',
				'static_file' => $relativePath,
				'static' => TRUE,
			];
			if ($this->categoryId) {
				$data['category'] = $this->categoryId;
				}
			$response = $this->modx->runProcessor('/element/template/create', $data);
			if (!$response->isError()) {
				$elems = $this->Easypack->getTemplates([]);
				$elems[] = $element->name;
				$elems = array_unique($elems);
				$this->Easypack->set('templates', $elems);
			}
		}

		public function checkElem($dirname, SplFileInfo $element)
		{
			$elems = $this->Easypack->getElem($dirname, []) ?: [];
			$class = $this->classes[$dirname];
			$obj = $this->modx->getCount($class['name'], [$class['k'] => $element->name]);
			if ($obj > 0) {

				if (in_array($element->name, $elems, TRUE)) {
					return FALSE;
				} else {
					$elems[] = $element->name;
					$elems = array_unique($elems);
					$this->Easypack->set($dirname, $elems);
					return FALSE;
				}
			} else {
				return TRUE;
			}
			return TRUE;
		}

		public function pluginGetEvents($str)
		{

			$re = '%(#\s{0,}(\S+)$)|(\/\/\s{0,}(\S+)$)|(\*\s{0,}([^\\\\]\S{1,})$)%m';
			preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
			$events = [];
			foreach ($matches as $i => $matche) {
				$_t = explode(':', end($matche));
				if ($_t[0]) {
					$events[$_t[0]]['name'] = $_t[0];
					$events[$_t[0]]['enabled'] = TRUE;
					if (isset($_t[1])) {
						$events[$_t[0]]['priority'] = (is_numeric($_t[1]) and $_t[1]) ? $_t[1] : 0;
					} else {
						$events[$_t[0]]['priority'] = 0;
					}
				}
			}
			return $events;
		}


	}

	return 'EasypackBuildCreatFromFolderProcessor';

