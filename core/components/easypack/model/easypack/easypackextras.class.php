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

		public function getSettings($default = NULL)
		{
			$Settings = $this->getProperty('settings', $default);
			if ($Settings) {
				$Settings = @json_decode($Settings, 1);
				if ($Settings and is_array($Settings)) {
					return $Settings;
				}
			}
			return [];
		}

		public function getSnippets($default = NULL)
		{
			$Snippets = $this->getProperty('snippets', $default);
			if ($Snippets) {
				$Snippets = @json_decode($Snippets, 1);
				if ($Snippets and is_array($Snippets)) {
					return $Snippets;
				}
			}
			return [];
		}

		public function getPlugins($default = NULL)
		{
			$Plugins = $this->getProperty('plugins', $default);
			if ($Plugins) {
				$Plugins = @json_decode($Plugins, 1);
				if ($Plugins and is_array($Plugins)) {
					return $Plugins;
				}
			}
			return [];
		}

		public function getElem($k, $default = NULL)
		{
			switch ($k) {
				case 'snippets':
					return $this->getSnippets($default);
				case 'chunks':
					return $this->getChunks($default);
				case 'plugins':
					return $this->getPlugins($default);
				case 'templates':
					return $this->getTemplates($default);
			}
			return $this->getProperty($k, $default);
		}

		public function getTemplates($default = NULL)
		{
			$Templates = $this->getProperty('templates', $default);
			if ($Templates) {
				$Templates = @json_decode($Templates, 1);
				if ($Templates and is_array($Templates)) {
					return $Templates;
				}
			}
			return [];
		}

		public function getMenus($default = NULL)
		{
			$Menus = $this->getProperty('menus', $default);
			if ($Menus) {
				$Menus = @json_decode($Menus, 1);
				if ($Menus and is_array($Menus)) {
					return $Menus;
				}
			}
			return [];
		}

		public function getChunks($default = NULL)
		{
			$Chunks = $this->getProperty('chunks', $default);
			if ($Chunks) {
				$Chunks = @json_decode($Chunks, 1);
				if ($Chunks and is_array($Chunks)) {
					return $Chunks;
				}
			}
			return [];
		}

		public function getResources($default = NULL)
		{
			$resources = $this->getProperty('resources', $default);
			if ($resources) {
				$resources = @json_decode($resources, 1);
				if ($resources and is_array($resources)) {
					return $resources;
				}
			}
			return [];
		}

		public function getRest($default = NULL)
		{
			$elem = $this->getProperty('modUtilitiesRest', $default);
			if ($elem) {
				$elem = @json_decode($elem, 1);
				if ($elem and is_array($elem)) {
					return $elem;
				}
			}
			return [];
		}

		public function getTables($default = NULL)
		{
			$elem = $this->getProperty('tables', $default);
			if ($elem) {
				$elem = @json_decode($elem, 1);
				if ($elem and is_array($elem)) {
					return $elem;
				}
			}
			return [];
		}

		public function getRequires($default = NULL)
		{
			$elem = $this->getProperty('requires', $default);
			if ($elem) {
				$elem = @json_decode($elem, 1);
				if ($elem and is_array($elem)) {
					return $elem;
				}
			}
			return [];
		}

		public function getRequiresExtras($default = NULL)
		{
			$elem = $this->getProperty('requires', $default);
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