<?php
	// Ищем MODX
	// Подключаем MODX
	if (!isset($modx)) {
		$base_path = __DIR__;
		// Ищем MODX
		$_i = 0;
		while (!file_exists($base_path . '/config.core.php') and $_i < 50) {
			$base_path = dirname($base_path);
			$_i++;
		}
		if (file_exists($base_path . '/index.php')) {
			ini_set('display_errors', 1);
			ini_set("max_execution_time", 50000);
			define('MODX_API_MODE', TRUE);
			require_once $base_path . '/config.core.php';
			require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
			require_once MODX_CONNECTORS_PATH . 'index.php';
		} else {
			die("modx not found");
		}
	}
	if (!isset($modx)) {
		die("modx not found");
	}

	// Указываем путь к папке с процессорами и заставляем MODX работать
	$modx->lexicon->load('easypack:default');

	$modx->addPackage('easypack',MODX_CORE_PATH.'components/easypack/model/');

	$modx->request->handleRequest([
		'processors_path' => MODX_CORE_PATH . 'components/easypack/processors/',
		'location' => '',
	]);
