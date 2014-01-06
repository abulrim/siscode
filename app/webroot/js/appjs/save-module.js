/*jslint browser:true,devel:true,white:true,nomen:true,bad_new:true*/
/*globals jQuery,Backbone,Handlebars,_, undefined, FB*/

(function ($, Backbone, _, H, amplify, undef) {
	"use strict";

	var App = window.App || {},
			CLICK_OR_TOUCH = 'click',
			IS_MOBILE = navigator.userAgent.match(/mobile/i);

	if (IS_MOBILE && 'ontouchstart' in document.documentElement) {
		CLICK_OR_TOUCH = 'touchstart';
	}

	// -----
	// Saved
	// -----

	// App.SavedModel
	App.SavedModel = Backbone.Model.extend({
		defaults: {
			url: null,
			name: null
		},

		sync: function(method, model, options) {
			var combinations, 
				createdModel, 
				modelId, 
				index;

			if (method === 'create') {
				combinations = amplify.store("savedCombinations");
				if (!combinations) {
					combinations = [];
				}
				createdModel = this.toJSON();
				createdModel.id = _.uniqueId();
				combinations.push(createdModel);
				amplify.store("savedCombinations", combinations);
				options.success(createdModel);
			} else if(method === 'delete') {
				modelId = model.get("id");
				combinations = amplify.store("savedCombinations", combinations);
				index = null;
				_.each(combinations, function(combination, key) {
					if (combination.id === modelId) {
						index = key;
						return false;
					}
				});
				if (index !== null) {
					combinations.splice(index, 1);
				}
				amplify.store("savedCombinations", combinations);
			} else if (method === 'update') {
				modelId = model.get("id");
				combinations = amplify.store("savedCombinations", combinations);
				index = null;
				_.each(combinations, function(combination, key) {
					if (combination.id === modelId) {
						combinations[key] = model.toJSON();
						return false;
					}
				});
				amplify.store("savedCombinations", combinations);
			}
		},

		clear: function() {
			this.destroy();
		}
	});

	// App.SavedView
	App.SavedView = Backbone.View.extend({
		tagName: 'li',
		router: null,
		editMode: false,

		template: H.compile($('#foot-bar-combination-tmpl').html()),

		initialize: function(options) {
			this.router = options.router;
			this.webroot = options.webroot;
			this.model.on('destroy', this.remove, this);
			this.model.on('change:name', this.render, this);
		},

		events: function() {
			var events = {};
			events[CLICK_OR_TOUCH + ' a'] = 'goToSavedUrl';
			events[CLICK_OR_TOUCH + ' .foot-bar-remove'] = 'removeCombination';
			events[CLICK_OR_TOUCH + ' .foot-bar-edit'] = 'editCombination';
			events['keypress input'] = 'updateCombination';
			return events;
		},

		editCombination: function() {
			if (this.editMode) {
				this.update();
			} else {
				this.editMode = true;
				this.$('.foot-bar-combination-name').hide();
				this.$('input').show().focus().select();
			}
			return false;
		},

		updateCombination: function(e) {
			if (e.keyCode === 13) {
				this.update();
			}
		},

		update: function() {
			var newName;
			this.$('.foot-bar-combination-name').show();
			newName = this.$('input').hide().val();
			this.model.save({ name: newName });
			this.editMode = false;

			window.woopraTracker.pushEvent({
				name: 'edit',
				url: window.location.origin + this.webroot + 'c/' + this.model.get('url'),
				title: newName
			});
		},

		removeCombination: function() {
			window.woopraTracker.pushEvent({
				name: 'delete',
				url: window.location.origin + this.webroot + 'c/' + this.model.get('url'),
				title: this.model.get('name')
			});
			this.model.clear();
			return false;
		},

		goToSavedUrl: function() {
			this.router.navigate('c/' + this.model.get("url"), { trigger: true });
			return false;
		},

		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		}
	});

	// App.SavedCollection
	App.SavedCollection = Backbone.Collection.extend({
		model: App.SavedModel,

		sync: function(method, collection, options) {
			if (method === 'read') {
				var savedCombinations = amplify.store("savedCombinations");
				options.success(savedCombinations);
			}
		}
	});

	// App.SavedCollectionView
	App.SavedCollectionView = Backbone.View.extend({
		el: '.foot-bar',
		toggled: false,
		router: null,
		webroot: null,
		courseCollection: null,
		collection: new App.SavedCollection(),

		events: function() {
			var events = IS_MOBILE ? {
				'touchstart .foot-bar-toggle': 'toggleFoot',
				'touchstart .foot-bar-add': 'save'
			}:{
				'click .foot-bar-toggle': 'toggleFoot',
				'click .foot-bar-add': 'save'
			};
			return events;
		},

		initialize: function(options) {
			this.router = options.router;
			this.webroot = options.webroot;
			this.courseCollection = options.courseCollection;
			this.collection.on('add', this.addOne, this);
			this.collection.on('reset', this.addAll, this);
			this.collection.fetch({ reset: true });
		},

		addAll: function(collection) {
			var self = this;

			_.each(collection.models, function(model) {
				self.addSaved(model, true);
			});
		},

		addOne: function(model) {
			this.addSaved(model);
		},

		addSaved: function(model, ignore) {
			var view, 
				$viewEl;

			view = new App.SavedView({
				router: this.router,
				webroot: this.webroot,
				model: model
			});
			view.render();

			$viewEl = view.$el;
			this.$('.foot-bar-combination').prepend($viewEl);
			if (!ignore) {
				view.editCombination();
				if (!this.toggled) {
					this.toggleFoot();
				}
			}
		},

		toggleFoot: function() {
			var arrow;

			arrow = this.$('.foot-bar-arrow');
			if (this.toggled === false) {
				this.$el.addClass('toggled');
				arrow.addClass('down');
				this.toggled = true;
			} else {
				this.$el.removeClass('toggled');
				arrow.removeClass('down');
				this.toggled = false;
			}
			return false;
		},

		crnHash: function() {
			var hash,
				coursesHash = [];

			_.each(this.courseCollection.models, function(course){
				coursesHash.push('--' + course.get('crn'));
			});

			hash = [
				this.router.institution.id, 
				this.router.page, 
				this.router.days.join('-'),
				coursesHash.join('_')
			].join('_');

			return hash;
		},

		save: function() {
			if (this.collection.where({url: this.router.url}).length === 0 && this.router.url.length !== 0) {
				var link = '';

				_.each(this.courseCollection.models, function(model, key) {
					if (key !== 0) {
						link += ' - ';
					}
					link += model.get("crn") + ' (' + model.get("subject_code") + ' ' + model.get("number") + ')';
				});

				window.woopraTracker.pushEvent({
					name: 'save',
					url: window.location.href,
					title: link
				})

				this.collection.create({ url: this.crnHash(), name: link });
			}
		}
	});

	window.App = App;

}(jQuery, Backbone, _, Handlebars, amplify));
