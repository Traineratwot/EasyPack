<?php
	if (!isset($PKG_NAME_LOWER)) {
		return FALSE;
	}
	if (!isset($shema)) {
		return FALSE;
	}


	if (!function_exists('EasyPackGetTabs')) {
		function EasyPackGetTabs($shema = NULL, $ConnectorUrl = NULL, $PKG_NAME_LOWER = NULL)
		{
			$tabs = [];
			foreach ($shema as $table => $fields) {
				$columns = EasyPackGetColumns($table, $fields['fields']);
				$fieldsNames = json_encode(array_keys($fields['fields']));
				$tabs[$table] = <<<JS
{
	title: _('{$table}'),
	id: "{$table}-tabs",
	xtype: 'panel',
	layout: 'anchor',
	items:[
		{
			xtype: extraExt.grid.xtype,
			id: "{$table}-grid",
			url: {$ConnectorUrl},
			pageSize: 20,
			anchor:'99.8%',
			nameField: 'id',
			keyField: 'id',
			fields: {$fieldsNames},
			columns: [$columns],
			extraExtSearch: true,//Включает поиск
			searchKey: 'query', // ключ для поиска
			extraExtUpdate: true,//Включает форму обновления
			extraExtCreate: true,//Включает кнопку создания
			action: "mgr/{$table}/getlist",
			create_action: "mgr/{$table}/create", // путь к процессору создания нового элемента
			save_action: "mgr/{$table}/update", //стандартный save_action
			delete_action: "mgr/{$table}/remove", // путь к процессору удаления элемента
			sortBy: 'id',
			sortDir: 'desc',
			autosave: true,
		}
	]
}
JS;

			}
			return implode(",\n\r", $tabs);
		}
	}
	if (!function_exists('EasyPackGetColumns')) {
		function EasyPackGetColumns($table = NULL, $fields = NULL)
		{
			$columns = [];
			foreach ($fields as $key => $field) {
				switch (strtolower($field['PhpType'])) {
					case 'boolean':
					case 'bool':
						$renderer = 'extraExt.grid.renderers.BOOL';
						break;
					default:
						$renderer = 'extraExt.grid.renderers.default';
						break;
				}
				if($key == 'id'){
					$visible = 'false';
				}else{
					$visible = 'true';
				}
				$columns[] = <<<JS
{
	dataIndex: "{$field['key']}",
	header: _("{$field['key']}"),
	sortable: true,
	extraExtEditor: {
		visible: {$visible},
	},
	extraExtRenderer: {
		popup: true,
	},
	renderer: $renderer,
}
JS;


			}
			return implode(",\n\r", $columns);

		}
	}


	$ConnectorUrl = $PKG_NAME_LOWER . 'ConnectorUrl';

	$tabs = EasyPackGetTabs($shema, $ConnectorUrl, $PKG_NAME_LOWER);
	return <<<JS
Ext.onReady(function() {
	MODx.add({
		xtype: 'page-panel-home'
	})

})
var page = function(config) {
	config = config || {}
	page.superclass.constructor.call(this, config)
}
Ext.extend(page, MODx.Component, {
	panel: {},
})
Ext.reg('page', page)
page = new page()

page.panel.Home = function(config) {
	config = config || {}
	Ext.apply(config, {
		cls: 'container', // Добавляем отступы
		items: [
			{
				html: ' <h2>' + _('{$PKG_NAME_LOWER}') + '</h2>',
			},
			{
				xtype: extraExt.tabs.xtype,
				items: [{$tabs}]
			}
		]

	})
	page.panel.Home.superclass.constructor.call(this, config) // Чёртова магия =)
}
Ext.extend(page.panel.Home, MODx.Panel)
Ext.reg('page-panel-home', page.panel.Home)
JS;
