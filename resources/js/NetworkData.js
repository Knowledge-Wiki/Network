module.NetworkData = ( function ( viz, mw ) {
	"use strict"

	let NetworkData = function() {
		this.nodes = new vis.DataSet();
		this.edges = new vis.DataSet();
	};

	NetworkData.prototype.addPages = function(pages) {
		this.nodes.update(pages.map(
			page => {
				return {
					id: page.title,
					label: page.title,

					pageTitle: page.title
				}
			}
		));
	}

	NetworkData.prototype.addLinks = function(links) {
		this.edges.update(links.map(
			link => Object.assign(
				link,
				{
					id: link.from + '|' + link.to,
					arrows: 'to'
				}
			)
		));
	}

	return NetworkData;

}( window.vis, window.mediaWiki ) );
