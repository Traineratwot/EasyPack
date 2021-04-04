<?PHP
	/**
	 * Created by Easypack.
	 */

	class EasypackExtrasGetListProcessor extends modObjectGetListProcessor
	{
		public $classKey = 'EasypackExtras';
		public $primaryKeyField = 'id';
		public $defaultSortField = 'id';
		public $defaultSortDirection = 'DESC';
	}
	return 'EasypackExtrasGetListProcessor';