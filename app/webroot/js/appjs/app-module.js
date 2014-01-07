/*jslint browser:true,devel:true,white:true,nomen:true,bad_new:true*/
/*globals jQuery,Backbone,Handlebars,_, undefined, FB*/

(function ($, Backbone, undef) {
	"use strict";

	var App = window.App || (window.App = {});

	// ----------
	// App module
	// ----------
	App.AppView = Backbone.View.extend({
		el: 'body',

		events:{
			'keyup': 'keyup'
		},

		initialize: function(options) {
			this.router = options.router;
		},

		keyup: function(e) {
			if ($(e.target).is('input')) {
				return;
			}
			if(e.keyCode === 37) {
				this.router.previousPage();
			} else if(e.keyCode === 39) {
				this.router.nextPage();
			}
		}
	});

}(jQuery, Backbone, amplify));
