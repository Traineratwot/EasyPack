<?php

	class EasypackExtras extends xPDOSimpleObject
	{

		public function set($k = NULL, $v = NULL, $vType = '')
		{
			if (is_array($v) or is_object($v)) {
				$v = @json_encode($v, 256);
			}
			parent::set($k, $v, $vType);
		}

		public function getProperty($k, $default = NULL)
		{
			$value = $this->get($k);
			return (!empty($value) and !is_null($value)) ? $value : $default;
		}

		public function getSettings()
		{
			$Settings = $this->getProperty('settings');
			if ($Settings) {
				$Settings = @json_decode($Settings, 1);
				if ($Settings and is_array($Settings)) {
					return $Settings;
				}
			}
			return [];
		}

		public function getSnippets()
		{
			$Snippets = $this->getProperty('snippets');
			if ($Snippets) {
				$Snippets = @json_decode($Snippets, 1);
				if ($Snippets and is_array($Snippets)) {
					return $Snippets;
				}
			}
			return [];
		}

		public function getPlugins()
		{
			$Plugins = $this->getProperty('plugins');
			if ($Plugins) {
				$Plugins = @json_decode($Plugins, 1);
				if ($Plugins and is_array($Plugins)) {
					return $Plugins;
				}
			}
			return [];
		}

		public function getElem($k, $format = NULL, $formatTemplate = NULL)
		{
			switch ($k) {
				case 'snippets':
					return $this->getSnippets();
				case 'chunks':
					return $this->getChunks();
				case 'plugins':
					return $this->getPlugins();
			}
			return parent::get($k, $format, $formatTemplate);
		}

		public function getTemplates()
		{
			$Templates = $this->getProperty('templates');
			if ($Templates) {
				$Templates = @json_decode($Templates, 1);
				if ($Templates and is_array($Templates)) {
					return $Templates;
				}
			}
			return [];
		}

		public function getMenus()
		{
			$Menus = $this->getProperty('menus');
			if ($Menus) {
				$Menus = @json_decode($Menus, 1);
				if ($Menus and is_array($Menus)) {
					return $Menus;
				}
			}
			return [];
		}

		public function getChunks()
		{
			$Chunks = $this->getProperty('chunks');
			if ($Chunks) {
				$Chunks = @json_decode($Chunks, 1);
				if ($Chunks and is_array($Chunks)) {
					return $Chunks;
				}
			}
			return [];
		}

		public function getResources()
		{
			$resources = $this->getProperty('resources');
			if ($resources) {
				$resources = @json_decode($resources, 1);
				if ($resources and is_array($resources)) {
					return $resources;
				}
			}
			return [];
		}

		public function getRest()
		{
			$elem = $this->getProperty('modUtilitiesRest');
			if ($elem) {
				$elem = @json_decode($elem, 1);
				if ($elem and is_array($elem)) {
					return $elem;
				}
			}
			return [];
		}

		public function getTables()
		{
			$elem = $this->getProperty('tables');
			if ($elem) {
				$elem = @json_decode($elem, 1);
				if ($elem and is_array($elem)) {
					return $elem;
				}
			}
			return [];
		}

		public function getRequires()
		{
			$elem = $this->getProperty('requires');
			if ($elem) {
				$elem = @json_decode($elem, 1);
				if ($elem and is_array($elem)) {
					return $elem;
				}
			}
			return [];
		}

		public function getRequiresExtras()
		{
			$elem = $this->getProperty('requires');
			if ($elem) {
				$elem = @json_decode($elem, 1);
				if ($elem and is_array($elem)) {
					if (isset($elem['extras'])) {
						return $elem['extras'];
					}
				}
			}
			return [];
		}


	}