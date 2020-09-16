<?php

	class EasypackExtras extends xPDOSimpleObject
	{

		public function getProperty($k, $default = NULL)
		{
			$value = $this->get($k);
			return (!empty($value) and $value != NULL) ? $value : $default;
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
	}