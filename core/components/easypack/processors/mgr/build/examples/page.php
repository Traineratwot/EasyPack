<?php
	if (!isset($PKG_NAME_LOWER)) {
		return FALSE;
	}
return <<<PHP
<?php
	if (file_exists(MODX_CORE_PATH . 'components/extraext/model/extraext.include.php')) {
		include_once MODX_CORE_PATH . 'components/extraext/model/extraext.include.php';
	}


	if (class_exists('extraExtManagerController')) {
		//Основной контроллер

		class {$PKG_NAME_LOWER}IndexManagerController extends extraExtManagerController
		{

			public function getLanguageTopics()
			{
				return [
					'{$PKG_NAME_LOWER}:default',
				];
			}

			public function getPageTitle()
			{
				return \$this->modx->lexicon('{$PKG_NAME_LOWER}');
			}

			public function loadCustomCssJs()
			{
				\$this->addJavascript('js/index.js', \$this->componentUrl);
			}
		}
	} else {
		//Запасной контроллер
		class {$PKG_NAME_LOWER}IndexManagerController extends modExtraManagerController
		{
			public function getLanguageTopics()
			{
				return [
					'$PKG_NAME_LOWER:default',
				];
			}

			public function getPageTitle()
			{
				return '$PKG_NAME_LOWER';
			}

			public function loadCustomCssJs()
			{
				\$this->addHtml('ERROR pleas install <strong><a href="https://modstore.pro/packages/other/extraext">ExtraExt</a></strong>');
			}
		}

	}
PHP;
