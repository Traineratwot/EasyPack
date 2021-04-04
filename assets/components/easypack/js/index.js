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
				html: ' <h2>' + _('easypack') + '</h2>',
			},
			{
				xtype: 'panel',
				layout: 'anchor',
				anchor: '100%',
				items: [{
					xtype: 'extraExt.grid.xtype',
					id: 'EasypackExtras-grid',
					url: 'easypackConnectorUrl',
					pageSize: 20,
					nameField: 'id',
					keyField: 'id',
					fields: [
						'id',
						'name',
						'signature',
						'version',
						'date',
						'chunks',
						'snippets',
						'plugins',
						'templates',
						'resources',
						'menus',
						'settings',
						'core',
						'assets',
						'customPaths',
						'requires',
						'readme',
						'changelog',
						'setup_option',
						'php_resolver',
						'license',
						'tables',
						'path_to_last_transport',
						'package_id',
						'modUtilitiesRest'
					],
					'columns': [
						{
							'dataIndex': 'id',
							'header': 'id',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						},
						{
							'dataIndex': 'name',
							'header': 'name',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						},
						{
							'dataIndex': 'signature',
							'header': 'signature',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						},
						{
							'dataIndex': 'version',
							'header': 'version',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						},
						{
							'dataIndex': 'date',
							'header': 'date',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						},
						{
							'dataIndex': 'chunks',
							'header': 'chunks',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						},
						{
							'dataIndex': 'snippets',
							'header': 'snippets',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						},
						{
							'dataIndex': 'plugins',
							'header': 'plugins',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						},
						{
							'dataIndex': 'templates',
							'header': 'templates',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						},
						{
							'dataIndex': 'resources',
							'header': 'resources',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						},
						{
							'dataIndex': 'menus',
							'header': 'menus',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						},
						{
							'dataIndex': 'settings',
							'header': 'settings',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						},
						{
							'dataIndex': 'core',
							'header': 'core',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						},
						{
							'dataIndex': 'assets',
							'header': 'assets',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						},
						{
							'dataIndex': 'customPaths',
							'header': 'customPaths',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						},
						{
							'dataIndex': 'requires',
							'header': 'requires',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						},
						{
							'dataIndex': 'readme',
							'header': 'readme',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						},
						{
							'dataIndex': 'changelog',
							'header': 'changelog',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						},
						{
							'dataIndex': 'setup_option',
							'header': 'setup_option',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						},
						{
							'dataIndex': 'php_resolver',
							'header': 'php_resolver',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						},
						{
							'dataIndex': 'license',
							'header': 'license',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						},
						{
							'dataIndex': 'tables',
							'header': 'tables',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						},
						{
							'dataIndex': 'path_to_last_transport',
							'header': 'path_to_last_transport',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						},
						{
							'dataIndex': 'package_id',
							'header': 'package_id',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						},
						{
							'dataIndex': 'modUtilitiesRest',
							'header': 'modUtilitiesRest',
							'sortable': true,
							'extraExtEditor': {
								'visible': false
							},
							'extraExtRenderer': {
								'popup': true
							},
							'renderer': 'extraExt.grid.renderers.default'
						}
					],
					'action': 'mgr\/EasypackExtras\/getlist'
				}
				]
			}
		]
		
	})
	page.panel.Home.superclass.constructor.call(this, config) // Чёртова магия =)
}
Ext.extend(page.panel.Home, MODx.Panel)
Ext.reg('page-panel-home', page.panel.Home)
