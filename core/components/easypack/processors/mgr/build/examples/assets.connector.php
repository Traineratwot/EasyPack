<?php
	if (isset($PKG_NAME_LOWER)) {
		return <<<PHP
	<?php
		// Ищем MODX
		// Подключаем MODX
		if (!isset(\$modx)) {
			\$base_path = __DIR__;
			// Ищем MODX
			// устанавливаем лимит на всякий случай
			\$_i = 0;
			while (!file_exists(\$base_path . "/config.core.php")  and \$_i < 50) {
				\$base_path = dirname(\$base_path);
			}
			if (file_exists(\$base_path . "/index.php")) {
				require_once \$base_path . "/config.core.php";
				require_once MODX_CORE_PATH . "config/" . MODX_CONFIG_KEY . ".inc.php";
				require_once MODX_CONNECTORS_PATH . "index.php";
			} else {
				die("modx not found");
			}
		}
		if (!isset(\$modx)) {
			die("modx not found");
		}
	
		// Указываем путь к папке с процессорами и заставляем MODX работать
		\$modx->addPackage("{$PKG_NAME_LOWER}", MODX_CORE_PATH . "components/{$PKG_NAME_LOWER}/model/");
		\$modx->lexicon->load("{$PKG_NAME_LOWER}:default");
		\$modx->request->handleRequest([
			"processors_path" => MODX_CORE_PATH . "components/{$PKG_NAME_LOWER}/processors/",
			"location" => "",
		]);
PHP;
	}
	return FALSE;
