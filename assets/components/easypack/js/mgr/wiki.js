Ext.onReady(function() {

	Ext.getCmp('Wiki-tabs').add(
		{
			id: 'EasyPack.wiki.Home',
			title: _('EasyPack.wiki.Home') || 'Главная',
			height: window.innerHeight,
			width: window.innerWidth,
			padding: '15px',
			html: `<code class="wiki" data-src="/assets/components/easypack/wiki/EasyPack.wiki/Home.md"></code>`,
		}
	)
	Ext.getCmp('Wiki-tabs').add(
		{
			id: 'EasyPack.wiki.resolver',
			title: _('EasyPack.wiki.resolver') || 'resolver',
			height: window.innerHeight,
			width: window.innerWidth,
			padding: '15px',
			html: `<code class="wiki" data-src="/assets/components/easypack/wiki/EasyPack.wiki/Resolver.md"></code>`,
		}
	)
	Ext.getCmp('Wiki-tabs').add(
		{
			id: 'EasyPack.wiki.setup_options',
			title: _('EasyPack.wiki.setup_options') || 'setup_options',
			height: window.innerHeight,
			width: window.innerWidth,
			padding: '15px',
			html: `<code class="wiki" data-src="/assets/components/easypack/wiki/EasyPack.wiki/setup_options.md"></code>`,
		}
	)
	Ext.getCmp('Wiki-tabs').add(
		{
			id: 'EasyPack.wiki.settings',
			title: _('EasyPack.wiki.settings') || 'Настройки',
			height: window.innerHeight,
			width: window.innerWidth,
			padding: '15px',
			html: `<code class="wiki" data-src="/assets/components/easypack/wiki/EasyPack.wiki/settings.md"></code>`,
		}
	)
	Ext.getCmp('Wiki-tabs').setActiveTab('EasyPack.wiki.Home')
	loadWiki()
})

function loadWiki() {
	$('code[data-src]').each(function() {
		var self = $(this)
		$.get(self.attr('data-src'), function(response) {
			convert(self, response)
		})
	})
}

function htmlUnEncode(text) {
	if(text instanceof String) {
		var t = text.replaceAll(/&amp;/g, '&').replaceAll(/&lt;/g, '<').replaceAll(/&gt;/g, '>')
		return t
	}
	return ''
}

showdown.setFlavor('github')

var converter = new showdown.Converter({
	tables: true,
	tasklists: true,
	smartIndentationFix: true,
	openLinksInNewWindow: true,
	emoji: true,
	// smoothPreview: '#wrap'
})

function convert(self, response) {
	$(self).html(converter.makeHtml(response))
	document.querySelectorAll('pre code').forEach((block) => {
		hljs.highlightBlock(block)
	})
	return true
}
//# sourceMappingURL=wiki.js.map

//# sourceMappingURL=wiki.js.map
