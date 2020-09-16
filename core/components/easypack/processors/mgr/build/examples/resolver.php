<?php
	if(isset($code) and isset($PKG_NAME_LOWER)) {
		return '
<?php
	if (!isset($modx) and $object->xpdo) {
		$modx =& $object->xpdo;
	}
	if ($modx instanceof xPDO) {
		/** @var array $options */
		switch ($options[xPDOTransport::PACKAGE_ACTION]) {
			case xPDOTransport::ACTION_UPGRADE:
			case xPDOTransport::ACTION_INSTALL:
				$modx->addPackage(\'' . $PKG_NAME_LOWER . '\', MODX_CORE_PATH . \'components/' . $PKG_NAME_LOWER . '/model/\');
				$manager = $modx->getManager();
				if($manager){
				' . $code . '
				}
				break;

			case xPDOTransport::ACTION_UNINSTALL:
				break;
		}
	}
	return TRUE;
';
	}
	return false;