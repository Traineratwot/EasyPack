<?php
	if (isset($code) and isset($PKG_NAME_LOWER)) {
		return <<<EOT
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
			case xPDOTransport::ACTION_INSTALL:
				\$modx->addPackage('$PKG_NAME_LOWER', MODX_CORE_PATH . 'components/$PKG_NAME_LOWER/model/');
				\$modx->addExtensionPackage('$PKG_NAME_LOWER', '[[++core_path]]components/$PKG_NAME_LOWER/model/');

				\$manager = \$modx->getManager();
				if(\$manager){
$code
				}
				break;

			case xPDOTransport::ACTION_UNINSTALL:
				\$modx->removeExtensionPackage('$PKG_NAME_LOWER');
				break;
		}
	}
	return TRUE;
EOT;
	}
	return FALSE;