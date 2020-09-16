<?php
	if (isset($PKG_NAME_LOWER) and isset($PKG_NAME)) {
		return '
	<?php
	/**
	 * Default English Lexicon Entries for Socialstream
	 *
	 * @package    '.$PKG_NAME_LOWER.'
	 * @subpackage lexicon
	 */
	$_lang[\''.$PKG_NAME_LOWER.'\']              = \''.$PKG_NAME.'\';
	';
	}
	return false;