<?php
	ini_set('display_errors', 1);

	class easypackIndexManagerController extends modExtraManagerController
	{
		public function getLanguageTopics(){
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
			$this->addJavascript($assets . 'components/easypack/js/mgr/main.tab.js?t='.time());
			$this->addCss($assets . 'components/easypack/css/mgr/main.tab.css?t='.time());
			$this->addCss('https://code.cdn.mozilla.net/fonts/fira.css');
			$this->addHtml('<script type="text/javascript">var EasyPackConnector_url = "' . $assets . 'components/easypack/connector.php";</script>');
		}
	}
