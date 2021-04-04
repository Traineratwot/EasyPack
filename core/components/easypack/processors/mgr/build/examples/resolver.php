<?php
	if (isset($code) and isset($PKG_NAME_LOWER)) {
		return <<<PHP
<?php
	/** @var object \$object */
	/** @var modX \$modx */
	if (!isset(\$modx) and \$object->xpdo) {
		\$modx =& \$object->xpdo;
	}
	if (\$modx instanceof xPDO) {
		/** @var array \$options */
		switch (\$options[xPDOTransport::PACKAGE_ACTION]) {
			case xPDOTransport::ACTION_UPGRADE:
				sendAuthorStat(['action' => 'UPGRADE']);
			case xPDOTransport::ACTION_INSTALL:
				sendAuthorStat(['action' => 'INSTALL']);
				\$modx->addPackage('$PKG_NAME_LOWER', MODX_CORE_PATH . 'components/$PKG_NAME_LOWER/model/');
				\$modx->addExtensionPackage('$PKG_NAME_LOWER', '[[++core_path]]components/$PKG_NAME_LOWER/model/');

				\$manager = \$modx->getManager();
				if(\$manager){
$code
				}
				break;

			case xPDOTransport::ACTION_UNINSTALL:
				sendAuthorStat(['action' => 'UNINSTALL']);
				\$modx->removeExtensionPackage('$PKG_NAME_LOWER');
				break;
		}
	}

	// отправляет мне информацию об установках.
	// Зачем? - Я не знаю, мне просто нравится видеть что моим кодом кто-то пользуется :) 
	function sendAuthorStat(\$data)
	{
		\$curl = curl_init();
		\$data = array_merge(['componentName' => '$PKG_NAME_LOWER', 'site' => \$_SERVER['SERVER_NAME']], \$data);

		\$data = json_encode(\$data);
		curl_setopt_array(\$curl, [
			CURLOPT_URL => 'http://traineratwot.aytour.ru/component/stat',
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 10,
			CURLOPT_SSL_VERIFYHOST => FALSE,
			CURLOPT_SSL_VERIFYPEER => FALSE,
			CURLOPT_AUTOREFERER => TRUE,
			CURLOPT_FOLLOWLOCATION => TRUE,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => \$data,
			CURLOPT_HEADER => 0,
		]);

		curl_exec(\$curl);
		curl_close(\$curl);
	}

	return TRUE;
PHP;
	}
	return FALSE;
