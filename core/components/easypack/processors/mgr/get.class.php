<?php

	/**
	 * Date: 10.06.2020
	 * Time: 21:10
	 */
	class EasypackGetListLogProcessor extends modObjectGetListProcessor
	{
		public $classKey = 'EasypackExtras';
		public $defaultSortField = 'id';
		public $defaultSortDirection = 'desc';
		public $primaryKeyField = 'id';

	}

	return "EasypackGetListLogProcessor";