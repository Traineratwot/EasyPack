<?php
	ini_set('display_errors', 1);
	ini_set('display_errors', 1);
	if (file_exists(MODX_CORE_PATH . 'components/extraext/model/extraext.include.php')) {
		include_once MODX_CORE_PATH . 'components/extraext/model/extraext.include.php';
	}
	if (class_exists('extraExtManagerController')) {
		//Основной контроллер
		class easypackIndexManagerController extends extraExtManagerController
		{

			public $componentName = 'easypack'; // название компонента так как называется его папка в assets/components/
			public $devMode = TRUE;

			public function getLanguageTopics()
			{
				return [
					'easypack:default',
				];
			}

			public function getPageTitle()
			{
				return $this->modx->lexicon('easypack_title');
			}

			public function loadCustomCssJs()
			{
				$assets = $this->modx->getOption('assets_url');
				$this->addCss($assets . 'components/easypack/css/mgr/main.tab.css');
				$this->addJavascript('ajax/libs/jquery/3.5.1/jquery.min.js', 'https://ajax.googleapis.com/', TRUE);
				$this->addJavascript('js/extraext/main.js', $this->componentUrl);
				$this->addLastJavascript('js/extraext/wiki.js',$this->componentUrl);
				$modUtil = file_exists(MODX_CORE_PATH . 'components/modutilities');
				$this->addHtml('
				<script type="text/javascript">
					var modUtil = "' . $modUtil . '";
					var modx_prefix = "' . $this->modx->config["table_prefix"] . '";
					if(modUtil){
						var modUtilConnector_url = "' . $assets . 'components/modutilities/connector.php";
					}else{
						var modUtilConnector_url = EasyPackConnector_url;
					}
				</script>');
			}
		}
	} else {
		//Запасной контроллер
		class easypackIndexManagerController extends modExtraManagerController
		{
			public function getLanguageTopics()
			{
				return [
					'easypack:default',
				];
			}

			public function getPageTitle()
			{
				return 'easypack';
			}

			public function getTemplateFile()
			{
				return MODX_ASSETS_PATH . '/components/easypack/home.tpl';
			}

			public function loadCustomCssJs()
			{
				$assets = $this->modx->getOption('assets_url');
				$this->addJavascript('https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js');
				$this->addJavascript($assets . 'components/easypack/js/mgr/jsonHighlighter.min.js');
				$this->addJavascript($assets . 'components/easypack/js/mgr/main.tab.js');
				$this->addJavascript($assets . 'components/easypack/js/highlight/highlight.pack.js');
				$this->addJavascript($assets . 'components/easypack/js/showdown/dist/showdown.js');
				$this->addLastJavascript($assets . 'components/easypack/js/mgr/wiki.js');

				$this->addCss($assets . 'components/easypack/css/mgr/main.tab.css');
				$this->addCss($assets . 'components/easypack/js/highlight/styles/github.css');

				$modUtil = file_exists(MODX_CORE_PATH . 'components/modutilities');
				$this->addHtml('
			<script type="text/javascript">
				var EasyPackConnector_url = "' . $assets . 'components/easypack/connector.php";
				var modUtil = "' . $modUtil . '";
				if(modUtil){
					var modUtilConnector_url = "' . $assets . 'components/modutilities/connector.php";
				}else{
					var modUtilConnector_url = EasyPackConnector_url;
				}
			</script>');
			}
		}

	}


