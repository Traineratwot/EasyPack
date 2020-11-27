Ext.onReady(function() {
	MODx.add({
		xtype: 'EasyPack-panel-home'
	})
	setTimeout(function(){
		document.querySelectorAll('pre code').forEach((block) => {
			if(typeof hljs != 'undefined') {
				hljs.highlightBlock(block)
			}
		})
	},1000)
})
var EasyPack = function(config) {
	config = config || {}
	EasyPack.superclass.constructor.call(this, config)
}
Ext.extend(EasyPack, MODx.Component, { // Перечисляем группы, внутрь которых будем "складывать" объекты
	panel: {},
	page: {},
	window: {},
	grid: {},
	tree: {},
	combo: {},
	config: {},
	view: {},
	utils: {}
})
Ext.reg('EasyPack', EasyPack)
EasyPack = new EasyPack()

var entityMap = {
	'&': '&amp;',
	'<': '&lt;',
	'>': '&gt;',
	'"': '&quot;',
	'\'': '&#39;',
	'/': '&#x2F;',
	'`': '&#x60;',
	'=': '&#x3D;'
}
var ToolTip = {
	render: function(e) {
		if(e.toolTip && e.getEl()) {
			Ext.QuickTips.register({
				target: e.getEl(),
				text: e.toolTip
			})
		}
	}
}

function escapeHtml(string) {
	return String(string).replace(/[&<>"'`=\/]/g, function(s) {
		return entityMap[s]
	})
}

var defaultRenderer = function(val) {
	return val || '<span class="false">' + _('ext_emptygroup') + '<span>'
}
var elemTemplate = new Ext.XTemplate('<tpl for=".">\
						<div class="x-combo-list-item">\
							<tpl if="id">({id})</tpl>\
							<strong>{name}</strong><small>({category_name})</small>\
						</div>\
					</tpl>', {compiled: true})
var elemTemplate2 = new Ext.XTemplate('<tpl for=".">\
						<div class="x-combo-list-item">\
							<tpl if="id">({id})</tpl>\
							<strong>{templatename}</strong><small>({category_name})</small>\
						</div>\
					</tpl>', {compiled: true})
var elemTemplate3 = new Ext.XTemplate('<tpl for=".">\
						<div class="x-combo-list-item">\
							<strong>{text_lex}</strong><small>({parent}),({namespace})</small>\
						</div>\
					</tpl>', {compiled: true})
var elemTemplate4 = new Ext.XTemplate('<tpl for=".">\
						<div class="x-combo-list-item">\
							<strong>{name_trans}</strong><small>({namespace})</small>\
						</div>\
					</tpl>', {compiled: true})
var elemTemplate5 = new Ext.XTemplate('<tpl for=".">\
						<div class="x-combo-list-item">\
							<strong>{url}</strong><small>({snippet})</small>\
						</div>\
					</tpl>', {compiled: true})
var elemTemplate6 = new Ext.XTemplate('<tpl for=".">\
						<div class="x-combo-list-item">\
							<strong>({id}) {pagetitle}</strong><small>({uri})</small>\
						</div>\
					</tpl>', {compiled: true})
var htmlRenderer = function(val) {
	return '<code> ' + escapeHtml(defaultRenderer(val)) + ' </code>'
}
var JSONRenderer = function(val) {
	if(val) {
		try {
			return hljs.highlight('json', val)
		} finally {
			return `<pre><code class="language-json">${val}</code></pre>`
		}
	}
	return defaultRenderer(val)
}
var todata = function(str, key = 'name') {
	if(str) {
		var arr = JSON.parse(str)
		if(arr.constructor.name == 'Array' || arr.constructor.name == 'Object') {
			var res = []
			for(var i of arr) {
				var t = {}
				t[key] = i
				res.push(t)
			}
			return res
		}
	}
	return null
}

//основной блок
EasyPack.panel.Home = function(config) {
	config = config || {}
	var columns = [ // Добавляем ширину и заголовок столбца
		{
			dataIndex: 'id',
			width: 200,
			header: _('id'),
			sortable: true,
			renderer: defaultRenderer
		},
		{
			dataIndex: 'name',
			width: 330,
			header: _('name'),
			sortable: true,
			renderer: function(val, e, b) {
				if(b.data.path_to_last_transport) {
					return `<a href="${b.data.path_to_last_transport}" title="${_('EasyPack.path_to_last_transport')}">${val}</a>`
				} else {
					return `${val}`
				}
			},
			editor: {xtype: 'textfield'}
		},
		{
			dataIndex: 'version',
			width: 200,
			header: _('version'),
			emptyText: '0.0.1-pl',
			sortable: true,
			tooltip: _('EasyPack.description.version'),
			renderer: defaultRenderer,
			editor: {xtype: 'textfield'}
		},

		{
			dataIndex: 'date',
			width: 330,
			tooltip: _('EasyPack.description.date'),
			header: _('date'),
			sortable: true,
			renderer: defaultRenderer,
			editor: {xtype: 'textfield'}
		},

		{
			dataIndex: 'chunks',
			width: 330,
			header: _('chunks'),
			tooltip: _('EasyPack.description.chunks'),
			sortable: true,
			renderer: JSONRenderer,
			editor: {xtype: 'textfield'}
		},
		{
			dataIndex: 'snippets',
			width: 330,
			header: _('snippets'),
			tooltip: _('EasyPack.description.snippets'),
			sortable: true,
			renderer: JSONRenderer,
			editor: {xtype: 'textfield'}
		},
		{
			dataIndex: 'plugins',
			width: 330,
			header: _('plugins'),
			tooltip: _('EasyPack.description.plugins'),
			sortable: true,
			renderer: JSONRenderer,
			editor: {xtype: 'textfield'}
		},
		{
			dataIndex: 'templates',
			width: 330,
			header: _('templates'),
			tooltip: _('EasyPack.description.templates'),
			sortable: true,
			renderer: JSONRenderer,
			editor: {xtype: 'textfield'}
		},
		{
			dataIndex: 'resources',
			width: 330,
			header: _('resources'),
			tooltip: _('EasyPack.description.resources'),
			sortable: true,
			renderer: JSONRenderer,
			editor: {xtype: 'textfield'}
		},
		{
			dataIndex: 'menus',
			width: 330,
			header: _('edit_menu'),
			tooltip: _('EasyPack.description.menus'),
			sortable: true,
			renderer: JSONRenderer,
			editor: {xtype: 'textfield'}
		},
		{
			dataIndex: 'settings',
			width: 330,
			header: _('settings'),
			tooltip: _('EasyPack.description.settings'),
			sortable: true,
			renderer: JSONRenderer,
			editor: {xtype: 'textfield'}
		},

		{
			dataIndex: 'core',
			width: 330,
			header: _('EasyPack.core'),
			tooltip: _('EasyPack.description.core'),
			sortable: true,
			renderer: defaultRenderer,
			hidden: true,
			editor: {xtype: 'textfield'}
		},
		{
			dataIndex: 'assets',
			width: 330,
			header: _('EasyPack.assets'),
			tooltip: _('EasyPack.description.assets'),
			sortable: true,
			hidden: true,
			renderer: defaultRenderer,
			editor: {xtype: 'textfield'}
		},

		{
			dataIndex: 'requires',
			width: 330,
			header: _('EasyPack.requires'),
			tooltip: _('EasyPack.description.requires'),
			sortable: true,
			hidden: false,
			renderer: JSONRenderer,
			editor: {xtype: 'textarea'}
		},
		{
			dataIndex: 'readme',
			width: 330,
			tooltip: _('EasyPack.description.readme'),
			header: _('EasyPack.readme'),
			sortable: true,
			hidden: true,
			renderer: defaultRenderer,
			editor: {xtype: 'textfield'}
		},
		{
			dataIndex: 'changelog',
			width: 330,
			tooltip: _('EasyPack.description.changelog'),
			header: _('EasyPack.changelog'),
			sortable: true,
			hidden: true,
			renderer: defaultRenderer,
			editor: {xtype: 'textfield'}
		},

		{
			dataIndex: 'tables',
			width: 330,
			header: _('EasyPack.tables'),
			tooltip: _('EasyPack.description.tables'),
			sortable: true,
			hidden: true,
			renderer: JSONRenderer,
			editor: {xtype: 'textarea'}
		},

		{
			dataIndex: 'setup_option',
			width: 330,
			header: _('EasyPack.setup_option'),
			tooltip: _('EasyPack.description.setup_option'),
			sortable: true,
			renderer: defaultRenderer,
			editor: {xtype: 'textfield'}
		},
		{
			dataIndex: 'php_resolver',
			width: 330,
			tooltip: _('EasyPack.description.php_resolver'),
			header: _('EasyPack.php_resolver'),
			sortable: true,
			renderer: defaultRenderer,
			editor: {xtype: 'textfield'}
		},
		{
			dataIndex: 'license',
			width: 330,
			tooltip: _('EasyPack.description.license'),
			header: _('EasyPack.license'),
			sortable: true,
			hidden: true,
			renderer: defaultRenderer,
			editor: {xtype: 'textfield'}
		},
	]
	if(modUtil) {
		columns.push(
			{
				dataIndex: 'modUtilitiesRest',
				width: 330,
				tooltip: _('EasyPack.description.modUtilitiesRest'),
				header: _('EasyPack.modUtilitiesRest'),
				sortable: true,
				hidden: true,
				renderer: JSONRenderer,
				editor: {xtype: 'textfield'}
			}
		)
	}
	var app = {
		cls: 'container', // Добавляем отступы
		items: [{
			html: ' <h2>' + _('EasyPack') + ' <small style="font-size: 10px"><a href="https://forms.gle/E9hZht9RthdcX6Ur7" target="_blank">Bug report</a></small></h2><br><small style="color: red">deprecated interface, please install <strong><a href="https://modstore.pro/packages/other/extraext">ExtraExt</a></strong> </small>',
		},
			{
				xtype: 'modx-tabs',
				deferredRender: false,
				border: true,
				items: [
					{
						title: _('EasyPack.Packages'),
						items: [{
							id: 'EasyPack-main-table',
							xtype: 'EasyPack-grid',
							columns: columns,
							fields: [
								'id',
								'name',
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
								'requires',
								'readme',
								'changelog',
								'setup_option',
								'php_resolver',
								'license',
								'tables',
								'path_to_last_transport',
								'modUtilitiesRest',
							],
							tbar: [{
								xtype: 'button', // Перемещаем сюда нашу кнопку
								text: _('EasyPack.create_new_package'),
								cls: 'primary-button',
								handler: function() {
									MODx.load({
										xtype: 'EasyPack-window-add',
									}).show()
								},
							}],
							action: 'mgr/get',
							save_action: 'mgr/update',
							autosave: true,
							getMenu: function(grid, rowIndex) {
								var m = []
								m.push({
									text: _('delete'),
									grid: grid,
									rowIndex: rowIndex,
									handler: this.del
								})
								m.push({
									text: _('update'),
									grid: grid,
									rowIndex: rowIndex,
									handler: this.update
								})
								m.push({
									text: _('EasyPack.create_structure'),
									grid: grid,
									rowIndex: rowIndex,
									handler: this.create
								})
								m.push({
									text: _('EasyPack.build'),
									grid: grid,
									rowIndex: rowIndex,
									handler: this.build
								})
								m.push({
									text: _('EasyPack.getResolver'),
									grid: grid,
									rowIndex: rowIndex,
									handler: this.getResolver
								})
								// m.push({
								// 	text: _('EasyPack.test'),
								// 	grid: grid,
								// 	rowIndex: rowIndex,
								// 	handler: this.test
								// })
								return m
							},

							del: function() {
								var cs = this.getSelectedAsList()
								var self = this
								MODx.msg.confirm({
									title: _('delete'),
									text: _('confirm_remove'),
									url: EasyPackConnector_url,
									params: {
										action: 'mgr/del',
										id: cs,
									},
									listeners: {
										'success': {
											fn: function(r) {
												if(!r.success) {
													MODx.msg.status({
														title: _('undeleted'),
														message: 'Ошибка',
														delay: 3
													})
												} else {
													MODx.msg.status({
														title: _('delete'),
														message: 'Готово',
														delay: 3
													})
												}
												self.refresh()
											}, scope: true
										}
									}
								})
							},
							build: function() {
								var cs = this.getSelectedAsList()
								var self = this
								MODx.msg.confirm({
									title: _('create'),
									text: _('confirm'),
									url: EasyPackConnector_url,
									params: {
										action: 'mgr/build/build',
										id: cs,
									},
									listeners: {
										'success': {
											fn: function(r) {
												if(!r.success) {
													MODx.msg.status({
														title: 'building',
														message: 'Ошибка',
														delay: 3
													})
												} else {
													console.log(r)
													MODx.msg.status({
														title: 'building',
														message: `Готово ${r.object.time}<br>
														<a href="${r.object.path}" target="_blank" >Скачать</a>
														`,
														delay: 10
													})
												}
												self.refresh()
											}, scope: true
										}
									}
								})
							},
							update: function() {
								var cs = this.getSelectedAsList()
								var self = this
								var data = this.getSelectionModel().getSelections()[0].data
								MODx.load({
									xtype: 'EasyPack-window-add',
									title: `обновить ${data.name}`,
									updateId: cs,
									updateData: data,
									action: self.save_action,
								}).show()
							},
							create: function() {
								var cs = this.getSelectedAsList()
								var self = this
								var data = this.getSelectionModel().getSelections()[0].data
								MODx.load({
									xtype: 'EasyPack-window-create',
									title: _('EasyPack.create') + ` ${data.name}`,
									updateId: cs,
									updateData: data,
								}).show()
							},
							test: function() {
								var cs = this.getSelectedAsList()
								var self = this
								var data = this.getSelectionModel().getSelections()[0].data
								MODx.msg.confirm({
									title: _('create'),
									text: _('confirm'),
									url: EasyPackConnector_url,
									params: {
										action: 'mgr/build/testPack',
										id: cs,
									},
									listeners: {
										'success': {
											fn: function(r) {
												Ext.MessageBox.show({
													title: config.title || '',
													msg: r.object.msg || '',
													width: window.innerWidth / 100 * 50,
													height: window.innerHeight / 100 * 50,
													buttons: Ext.MessageBox.CANCEL,
													icon: Ext.MessageBox.QUESTION
												})
												self.refresh()
											}, scope: true
										}
									}
								})
							},
							getResolver: function() {
								var cs = this.getSelectedAsList()
								var self = this
								var data = this.getSelectionModel().getSelections()[0].data
								MODx.msg.confirm({
									title: _('create'),
									text: _('confirm'),
									url: EasyPackConnector_url,
									params: {
										action: 'mgr/build/genresolver',
										id: cs,
									},
									listeners: {
										'success': {
											fn: function(r) {
												Ext.MessageBox.show({
													title: config.title || '',
													msg: r.message || '',
													width: window.innerWidth / 100 * 50,
													height: window.innerHeight / 100 * 50,
													buttons: Ext.MessageBox.CANCEL,
													icon: Ext.MessageBox.QUESTION
												})
												self.refresh()
											}, scope: true
										}
									}
								})
							},
						}
						]
					},
					{
						title: 'Wiki',
						id:'Wiki-tabs',
						xtype: 'modx-tabs',
						deferredRender: false,
						border: true,
						items: [

						]

					}
				]
			}
		]
	}

	Ext.apply(config, app)
	EasyPack.panel.Home.superclass.constructor.call(this, config) // Чёртова магия =)
}

Ext.extend(EasyPack.panel.Home, MODx.Panel)
Ext.reg('EasyPack-panel-home', EasyPack.panel.Home)

EasyPack.grid.ModGrid = function(config) { // Придумываем название, например, «Names»
	config = config || {}
	Ext.apply(config, {
		// Сюда перемещаем все свойства нашей таблички
		paging: true,
		autoHeight: true,
		viewConfig: {
			forceFit: true,
			scrollOffset: 0
		},
		remoteSort: true,
		url: EasyPackConnector_url,
		keyField: 'id',
		getSelectedAsList: function() {
			var selects = this.getSelectionModel().getSelections()
			if(selects.length <= 0) return false
			var cs = ''
			for(var i = 0; i < selects.length; i++) {
				cs += ',' + selects[i].data[this.keyField]
			}
			cs = cs.substr(1)
			return cs
		}
	})
	EasyPack.grid.ModGrid.superclass.constructor.call(this, config) // Магия
}
Ext.extend(EasyPack.grid.ModGrid, MODx.grid.Grid) // Наша табличка расширяет GridPanel
Ext.reg('EasyPack-grid', EasyPack.grid.ModGrid) // Регистрируем новый xtype

EasyPack.window.modWindow = function(config) {
	config = config || {}
	Ext.applyIf(config, {
		title: 'Create thing',
		width: window.innerWidth / 100 * 50,
		saveBtnText: 'Сохранить 💾',
		url: EasyPackConnector_url,
	})
	EasyPack.window.modWindow.superclass.constructor.call(this, config) // Магия
}
Ext.extend(EasyPack.window.modWindow, MODx.Window) // Расширяем MODX.Window
Ext.reg('EasyPack-window-modWindow', EasyPack.window.modWindow) // Регистрируем новый xtype
//добавление EasyPack

//окно редактора
EasyPack.window.add = function(config) {
	config.updateData = Object.assign({
		'id': null,
		'name': null,
		'version': null,
		'date': null,
		'chunks': null,
		'snippets': null,
		'plugins': null,
		'templates': null,
		'resources': null,
		'menus': null,
		'core': null,
		'assets': null,
		'readme': null,
		'changelog': null,
		'setup_option': null,
		'license': null,
		'tables': null,
		'modUtilitiesRest': null,
	}, config.updateData)
	config.tables = function(key) {
		if(config.updateData.tables) {
			var data = JSON.parse(config.updateData.tables)
			switch( key ) {
				case 'tables':
					return data.tables.join(',')
					break
				case 'prefix':
					return data.prefix
					break
			}
		}
	},
		this.ident = config.ident || 'EasyPack' + Ext.id()
	var fields = [
		{
			xtype: 'hidden',
			name: 'id',
			id: 'add-' + this.ident + '-id',
			value: config.updateId || null,
			allowBlank: true
		},
		{
			xtype: 'textfield',
			fieldLabel: _('name'),
			name: 'name',
			id: 'add-' + this.ident + '-name',
			anchor: '99%',
			value: config.updateData.name || null,
			allowBlank: false,
		},
		{
			xtype: 'textfield',
			fieldLabel: _('version'),
			emptyText: '0.0.1-pl',
			name: 'version',
			id: 'add-' + this.ident + '-version',
			anchor: '99%',
			value: config.updateData.version || null,
			allowBlank: false
		},

		{
			xtype: 'EasyPack-combo-modComboSuper',
			fieldLabel: _('chunk'),
			name: 'chunks',
			id: 'add-' + this.ident + '-chunk',
			anchor: '99%',
			value: todata(config.updateData.chunks) || null,
			forceSelection: true,
			fields: ['id', 'name', 'category_name'],
			url: MODx.config.connector_url,
			baseParams: {
				action: 'element/chunk/getlist', combo: 1, sort: 'id',
				dir: 'DESK',
			},
			allowBlank: true,
			valueField: 'name',
			displayField: 'name',
			tpl: elemTemplate
		},
		{
			xtype: 'EasyPack-combo-modComboSuper',
			fieldLabel: _('snippet'),
			name: 'snippets',
			id: 'add-' + this.ident + '-snippet',
			anchor: '99%',
			value: todata(config.updateData.snippets),
			forceSelection: true,
			fields: ['id', 'name', 'category_name'],
			url: MODx.config.connector_url,
			baseParams: {
				action: 'element/snippet/getlist', combo: 1, sort: 'id',
				dir: 'DESK',
			},
			allowBlank: true,
			valueField: 'name',
			displayField: 'name',
			tpl: elemTemplate
		},
		{
			xtype: 'EasyPack-combo-modComboSuper',
			fieldLabel: _('plugin'),
			name: 'plugins',
			id: 'add-' + this.ident + '-plugin',
			anchor: '99%',
			value: todata(config.updateData.plugins),
			forceSelection: true,
			fields: ['id', 'name', 'category_name'],
			url: MODx.config.connector_url,
			baseParams: {
				action: 'element/plugin/getlist', combo: 1, sort: 'id',
				dir: 'DESK'
			},
			allowBlank: true,
			valueField: 'name',
			displayField: 'name',
			tpl: elemTemplate
		},
		{
			xtype: 'EasyPack-combo-modComboSuper',
			fieldLabel: _('template'),
			name: 'templates',
			id: 'add-' + this.ident + '-template',
			anchor: '99%',
			value: todata(config.updateData.templates, 'templatename'),
			forceSelection: true,
			fields: ['id', 'templatename', 'category_name'],
			url: MODx.config.connector_url,
			baseParams: {
				action: 'element/template/getlist', combo: 1, sort: 'id',
				dir: 'DESK',
			},
			allowBlank: true,
			valueField: 'templatename',
			displayField: 'templatename',
			tpl: elemTemplate2
		},
		{
			xtype: 'EasyPack-combo-modComboSuper',
			fieldLabel: _('resources'),
			name: 'resources',
			id: 'add-' + this.ident + '-resources',
			anchor: '99%',
			value: todata(config.updateData.resources, 'id'),
			forceSelection: true,
			fields: ['id', 'pagetitle', 'uri'],
			url: MODx.config.connector_url,
			baseParams: {
				action: 'resource/getlist', combo: 1, sort: 'id',
				dir: 'DESK',
			},
			allowBlank: true,
			valueField: 'id',
			displayField: 'id',
			tpl: elemTemplate6
		},
		{
			xtype: 'EasyPack-combo-modComboSuper',
			fieldLabel: _('edit_menu'),
			name: 'menus',
			id: 'add-' + this.ident + '-menu',
			anchor: '99%',
			value: todata(config.updateData.menus, 'text'),
			forceSelection: true,
			fields: ['text', 'text_lex', 'parent', 'namespace'],
			url: MODx.config.connector_url,
			baseParams: {
				action: 'system/menu/getlist', combo: 1, sort: 'namespace',
				dir: 'DESK',
			},
			allowBlank: true,
			valueField: 'text',
			displayField: 'text',
			tpl: elemTemplate3
		},
		{
			xtype: 'EasyPack-combo-modComboSuper',
			fieldLabel: _('settings'),
			name: 'settings',
			id: 'add-' + this.ident + '-setting',
			anchor: '99%',
			value: todata(config.updateData.settings, 'key'),
			forceSelection: true,
			fields: ['key', 'name_trans', 'namespace'],
			url: MODx.config.connector_url,
			baseParams: {
				action: 'system/settings/getlist', combo: 1, sort: 'namespace',
				dir: 'DESK',
			},
			allowBlank: true,
			valueField: 'key',
			displayField: 'key',
			tpl: elemTemplate4
		},

		{
			xtype: 'textarea',
			toolTip: 'имена таблиц через запятую (с префиксом)',
			fieldLabel: _('EasyPack.tables'),
			name: 'tables',
			id: 'add-' + this.ident + '-tables',
			anchor: '99%',
			value: config.tables('tables') || null,
			allowBlank: true,
			rootVisible: true,
			listeners: ToolTip,

		},
		{
			xtype: 'textfield',
			fieldLabel: _('EasyPack.prefix'),
			name: 'prefix',
			id: 'add-' + this.ident + '-prefix',
			anchor: '99%',
			value: config.tables('prefix') || null,
			allowBlank: true,
			rootVisible: true,
			listeners: ToolTip,
		},
		{
			xtype: 'textfield',
			fieldLabel: _('EasyPack.core'),
			toolTip: _('EasyPack.description.core'),
			listeners: ToolTip,
			name: 'core',
			id: 'add-' + this.ident + '-core',
			anchor: '99%',
			value: config.updateData['core'] || null,
			allowBlank: true,
			rootVisible: true,
			source: MODx.config.default_media_source,
			openTo: '/core/components',
		},
		{
			xtype: 'textfield',
			fieldLabel: _('EasyPack.assets'),
			toolTip: _('EasyPack.description.assets'),
			listeners: ToolTip,
			name: 'assets',
			id: 'add-' + this.ident + '-assets',
			anchor: '99%',
			value: config.updateData['assets'] || null,
			allowBlank: true,
			rootVisible: true,
			source: MODx.config.default_media_source,
			openTo: '/core/components',
		},
		{
			xtype: 'textarea',
			name: 'requires',
			fieldLabel: _('EasyPack.requires'),
			tooltip: _('EasyPack.description.requires'),
			id: 'add-' + this.ident + '-requires',
			anchor: '99%',
			value: config.updateData['requires'] || null,
			allowBlank: true,
			rootVisible: true,
		},

		{
			xtype: 'modx-combo-browser',
			name: 'readme',
			tooltip: _('EasyPack.description.readme'),
			fieldLabel: _('EasyPack.readme'),
			id: 'add-' + this.ident + '-readme',
			anchor: '99%',
			value: config.updateData['readme'] || null,
			allowBlank: true,
			rootVisible: true,
			source: MODx.config.default_media_source,
			openTo: '/core/components'
		},
		{
			xtype: 'modx-combo-browser',
			name: 'changelog',
			tooltip: _('EasyPack.description.changelog'),
			fieldLabel: _('EasyPack.changelog'),
			id: 'add-' + this.ident + '-changelog',
			anchor: '99%',
			value: config.updateData['changelog'] || null,
			allowBlank: true,
			rootVisible: true,
			source: MODx.config.default_media_source,
			openTo: '/core/components'
		},
		{
			xtype: 'modx-combo-browser',
			fieldLabel: _('EasyPack.setup_option'),
			tooltip: _('EasyPack.description.setup_option'),
			listeners: ToolTip,
			name: 'setup_option',
			id: 'add-' + this.ident + '-setup_option',
			anchor: '99%',
			value: config.updateData['setup_option'] || null,
			allowBlank: true,
			rootVisible: true,
			allowedFileTypes: 'php',
			source: MODx.config.default_media_source,
			openTo: '/core/components'
		},
		{
			xtype: 'modx-combo-browser',
			tooltip: _('EasyPack.description.php_resolver'),
			fieldLabel: _('EasyPack.php_resolver'),
			listeners: ToolTip,
			name: 'php_resolver',
			id: 'add-' + this.ident + '-php_resolver',
			anchor: '99%',
			value: config.updateData['php_resolver'] || null,
			allowBlank: true,
			rootVisible: true,
			allowedFileTypes: 'php',
			source: MODx.config.default_media_source,
			openTo: 'core/components/'

		},
		{
			xtype: 'modx-combo-browser',
			tooltip: _('EasyPack.description.license'),
			fieldLabel: _('EasyPack.license'),
			listeners: ToolTip,
			name: 'license',
			id: 'add-' + this.ident + '-license',
			anchor: '99%',
			value: config.updateData['license'] || null,
			allowBlank: true,
			rootVisible: true,
			source: MODx.config.default_media_source,
			openTo: '/core/components'
		},
	]
	if(modUtil) {
		fields.push(
			{
				xtype: 'EasyPack-combo-modComboSuper',
				fieldLabel: _('EasyPack.modUtilitiesRest'),
				name: 'modUtilitiesRest',
				id: 'add-' + this.ident + '-modUtilitiesRest',
				anchor: '99%',
				value: todata(config.updateData.modUtilitiesRest, 'url'),
				forceSelection: true,
				fields: ['id', 'url', 'snippet'],
				url: modUtilConnector_url,
				baseParams: {
					action: 'mgr/rest/getRest', combo: 1, sort: 'id',
					dir: 'DESK',
				},
				allowBlank: true,
				valueField: 'url',
				displayField: 'url',
				tpl: elemTemplate5
			},
		)
	}

	Ext.applyIf(config, {
		title: _('EasyPack.create_new_pack'),
		fields: fields,
		action: 'mgr/create',
		listeners: {
			beforeSubmit: function(a) {
				// if(typeof a.allowMethod !== 'string' && a.allowMethod) {
				// 	var allowMethod = a.allowMethod.join()
				// 	if(typeof allowMethod == 'string') {
				// 		$(`input[name="allowMethod"]`).each(function() {
				// 			$(this).val(allowMethod)
				// 			$(this).attr('value', allowMethod)
				// 			this.value = allowMethod
				// 		})
				// 	}
				// }
				return true
			},
			success: function() {
				MODx.msg.status({
					title: _('created'),
					message: 'Готово',
					delay: 3
				})
				Ext.getCmp('EasyPack-main-table').refresh()
				this.remove()
			},
			failure: function() {
				this.remove()
			}
		}
	})
	EasyPack.window.add.superclass.constructor.call(this, config) // Магия
}
Ext.extend(EasyPack.window.add, EasyPack.window.modWindow) // Расширяем MODX.Window
Ext.reg('EasyPack-window-add', EasyPack.window.add) // Регистрируем новый xtype
//добавление категории

//окно редактора2
EasyPack.window.create = function(config) {
	config.updateData = Object.assign({
		'name': null,
	}, config.updateData)
	saveBtnText: _('EasyPack.create'),
		this.ident = config.ident || 'EasyPack' + Ext.id()
	config.updateData.name_lower = config.updateData.name.toLowerCase()
	var self = this
	Ext.applyIf(config, {
		id: 'EasyPack-window-' + this.ident + '-create',
		title: _('EasyPack.create_structure') + ' - ' + config.updateData.name,
		fields: [
			{
				xtype: 'hidden',
				name: 'id',
				value: config.updateId || null,
				allowBlank: true
			},
			{
				xtype: 'xcheckbox',
				name: 'create__js_mgr_',
				id: 'add-' + this.ident + '-create__js_mgr_',
				boxLabel: _('_js_mgr_', {name: config.updateData.name_lower}),
				originalValue: true,

			}, {
				xtype: 'xcheckbox',
				name: 'create__controllers_mgr_',
				id: 'add-' + this.ident + '-create__controllers_mgr_',
				boxLabel: _('_controllers_mgr_', {name: config.updateData.name_lower}),
				originalValue: true,

			}, {
				xtype: 'xcheckbox',
				name: 'create__docs_',
				id: 'add-' + this.ident + '-create__docs_',
				boxLabel: _('_docs_', {name: config.updateData.name_lower}),
				originalValue: true,

			}, {
				xtype: 'xcheckbox',
				name: 'create__elements_chunks_',
				id: 'add-' + this.ident + '-create__elements_chunks_',
				boxLabel: _('_elements_chunks_', {name: config.updateData.name_lower}),
				originalValue: true,

			}, {
				xtype: 'xcheckbox',
				name: 'create__elements_plugins_',
				id: 'add-' + this.ident + '-create__elements_plugins_',
				boxLabel: _('_elements_plugins_', {name: config.updateData.name_lower}),
				originalValue: true,

			}, {
				xtype: 'xcheckbox',
				name: 'create__elements_snippets_',
				id: 'add-' + this.ident + '-create__elements_snippets_',
				boxLabel: _('_elements_snippets_', {name: config.updateData.name_lower}),
				originalValue: true,

			}, {
				xtype: 'xcheckbox',
				name: 'create__elements_templates_',
				id: 'add-' + this.ident + '-create__elements_templates_',
				boxLabel: _('_elements_templates_', {name: config.updateData.name_lower}),
				originalValue: true,

			}, {
				xtype: 'xcheckbox',
				name: 'create__lexicon_en_',
				id: 'add-' + this.ident + '-create__lexicon_en_',
				boxLabel: _('_lexicon_en_', {name: config.updateData.name_lower}),
				originalValue: true,

			}, {
				xtype: 'xcheckbox',
				name: 'create__processors_',
				id: 'add-' + this.ident + '-create__processors_',
				boxLabel: _('_processors_', {name: config.updateData.name_lower}),
				originalValue: true,

			},
			{
				xtype: 'xcheckbox',
				name: 'create__model_',
				id: 'add-' + this.ident + '-create__model_',
				boxLabel: _('_model_', {name: config.updateData.name_lower}),
				originalValue: true,

			},
			{
				xtype: 'xcheckbox',
				name: 'create__namespace_',
				id: 'add-' + this.ident + '-create__namespace_',
				boxLabel: _('_namespace_', {name: config.updateData.name_lower}),
				originalValue: true,
			},
			{
				xtype: 'xcheckbox',
				name: 'create__elements_',
				id: 'add-' + this.ident + '-create__elements_',
				boxLabel: _('_elements_', {name: config.updateData.name_lower}),
				originalValue: true,
			},
		],
		saveBtnText: _('EasyPack.create') + ' 🗂',
		action: 'mgr/build/create',
		bbar: [
			{
				text: _('EasyPack.Select_All'),
				handler: function(e) {
					var f = Ext.getCmp('EasyPack-window-' + self.ident + '-create').fields
					for(x of f) {
						if(x.id) {
							Ext.getCmp(x.id).setValue(true)
						}
					}
				}
			},
			{
				text: _('EasyPack.Deselect_All'),
				handler: function(e) {
					var f = Ext.getCmp('EasyPack-window-' + self.ident + '-create').fields
					for(x of f) {
						if(x.id) {
							Ext.getCmp(x.id).setValue(false)
						}
					}
				}
			}
		],
		listeners: {
			success: function() {
				MODx.msg.status({
					title: _('created'),
					message: 'Готово',
					delay: 3
				})
				Ext.getCmp('EasyPack-main-table').refresh()
				this.remove()
			},
			failure: function() {
				this.remove()
			}
		},
	})
	EasyPack.window.create.superclass.constructor.call(this, config) // Магия
}
Ext.extend(EasyPack.window.create, EasyPack.window.modWindow) // Расширяем MODX.Window
Ext.reg('EasyPack-window-create', EasyPack.window.create) // Регистрируем новый xtype
//добавление категории

EasyPack.combo.modCombo = function(config) {
	config = config || {}
	this.ident = config.ident || 'mecnewsletter' + Ext.id()
	Ext.applyIf(config, {
		url: EasyPackConnector_url,
		valueField: 'id',
		width: '100%',
		anchor: '99%',
		editable: true,
		pageSize: 20,
		preventRender: true,
		forceSelection: true,
		enableKeyEvents: true,
	})
	EasyPack.combo.modCombo.superclass.constructor.call(this, config) // Магия
}
Ext.extend(EasyPack.combo.modCombo, MODx.combo.ComboBox) // Расширяем MODX.ComboBox
Ext.reg('EasyPack-combo-modCombo', EasyPack.combo.modCombo) // Регистрируем новый xtype

EasyPack.combo.modComboSuper = function(config) {
	config = config || {}
	Ext.applyIf(config, {
		xtype: 'superboxselect'
		, allowBlank: true
		, msgTarget: 'under'
		, allowAddNewData: true
		, addNewDataOnBlur: true
		, width: '100%'
		, editable: true
		, pageSize: 20
		, preventRender: true
		, forceSelection: true
		, enableKeyEvents: true
		, minChars: 2
		, hiddenName: config.name + '[]'
		, store: new Ext.data.JsonStore({
			id: (config.name || 'tags') + '-store'
			, root: 'results'
			, autoLoad: true
			, autoSave: false
			, totalProperty: 'total'
			, fields: config.fields
			, url: config.url || EasyPackConnector_url
			, baseParams: config.baseParams
		})
		, mode: 'remote'
		, displayField: 'value'
		, valueField: 'value'
		, triggerAction: 'all'
		, extraItemCls: 'x-tag'
		, expandBtnCls: 'x-form-trigger'
		, clearBtnCls: 'x-form-trigger'
		, listeners: {
			newitem: function(config, v, f) {bs.addItem({tag: v})}
		}
		, renderTo: Ext.getBody()
	})
	EasyPack.combo.modComboSuper.superclass.constructor.call(this, config)
}
Ext.extend(EasyPack.combo.modComboSuper, Ext.ux.form.SuperBoxSelect)
Ext.reg('EasyPack-combo-modComboSuper', EasyPack.combo.modComboSuper)



