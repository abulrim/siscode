/*jslint browser:true,devel:true,white:true,nomen:true,bad_new:true*/
/*globals jQuery,Backbone,Handlebars,_, undefined, FB*/

(function ($, Backbone, _, H, amplify, undef) {
	"use strict";


  var App = window.App || {},
			clickOrTouch = 'click',
			IS_MOBILE = navigator.userAgent.match(/mobile/i);

  if (IS_MOBILE && 'ontouchstart' in document.documentElement) {
    clickOrTouch = 'touchstart';
  }

  // -----------
  // Institution
  // -----------

  // App.InstitutionModel
	App.InstitutionModel = Backbone.Model.extend();

	// App.InstitutionCollection
	App.InstitutionCollection = Backbone.Collection.extend({
		model: App.InstitutionModel
	});

	// App.InstitutionListView
	// Fills subjects when institution select changes
	App.InstitutionListView = Backbone.View.extend({

		subjects: [],
		template: H.compile($('#institutions-select').html()),

		events: {
			'change select[name=institution_id]': 'fillSubjects'
		},

		fillSubjects: function() {
			var modelId,
				numbers,
				selectedModel;

			modelId = this.$('select[name=institution_id]').val();
			if (modelId) {
				selectedModel = MyInstitutionCollection.get(modelId).toJSON();
			} else {
				selectedModel = {subjects:[]};
			}
			this.subjects = selectedModel.subjects;
		},

		initialize: function() {
			this.collection.on('reset', this.render, this);
		},

		render: function() {
			var data = {
				institutions: this.collection.toJSON()
			};
			this.$el.html(this.template(data));
			return this;
		},

    afterRender: function() {
      this.$('select').chosen();
    }

	});

  // -------
  // Subject
  // -------

	// App.SubjectModel
	App.SubjectModel = Backbone.Model.extend();

	// App.SubjectCollection
	App.SubjectCollection = Backbone.Collection.extend({
		model: App.SubjectModel
	});

  // -----------
  // CourseInput
  // -----------

	// App.CourseInputModel
	App.CourseInputModel = Backbone.Model.extend();

	// App.CourseInputView
	// updates numbers when subject selected
	// needs to have subjects
	App.CourseInputView = Backbone.View.extend({
		tagName: 'div',
		className: 'cil-course-input',
		template: H.compile($('#course-input-tmpl').html()),
		numberTemplate: H.compile($('#course-input-numbers').html()),
		subjects: [],

		events: {
			'change select[name=subject_id]': 'fillNumber',
			'click .cil-remove-course': 'removeCourse',
			'keyup .cil-course-crn': 'crnKeyUp'
		},

		// Disables subjects and numbers select when crn present
		crnKeyUp: function(event) {
			if(this.$(event.target).val().length > 0) {
				this.$('select').prop('disabled', true).trigger('liszt:updated');
			} else {
				this.$('select').prop('disabled', false).trigger('liszt:updated');
			}
		},

		// Fills the numbers when subject select
		fillNumber: function() {
			var modelId,
				numbers,
				selectedModel;

			modelId = this.$('select[name=subject_id]').val();
			if (modelId) {
				selectedModel = MySubjectCollection.get(modelId).toJSON();
			} else {
				selectedModel = {numbers:[]};
			}
			this.$('select[name=number]').html(this.numberTemplate(selectedModel))
			.trigger('liszt:updated');
		},

		render: function() {
			var view, data;

			data = this.model.toJSON();
			data.subjects = this.subjects;
			this.$el.html(this.template(data))
			.find('select[name=subject_id]').val(this.model.get('subject_id')).trigger('change')
			.end()
			.find('select[name=number]').val(this.model.get('number'));

			return this;
		},

		afterRender: function() {
			this.$('select').chosen();
		},

		initialize: function() {
			this.model.on('destroy', this.remove, this);
		},

		removeCourse: function() {
			this.model.destroy();
		}
	});

	// App.CourseInputCollection
	App.CourseInputCollection = Backbone.Collection.extend({
		model: App.CourseInputModel
	});

	// App.CourseInputListView
	// updates the URL by calling the router and fills the courses
	// needs institutionListView, courseInputView
	App.CourseInputListView = Backbone.View.extend({
		el: '.cil',
		institutionListView: null,
		CourseInputModel: null,
		CourseInputView: null,
		// institution: null,

		page: 0,
		closed: false,

		events: {
			'keypress .course-input' : 'keyLog'
		},

		keyLog: function(e) {
			if (e.keyCode === 13) {
				this.updateUrlEvent();
			}
		},

		initialize: function() {
			this.addInstitutions();
			// this.institutionChanged();
			this.collection.on('add', this.addInput, this);
			this.collection.on('reset', this.closeInputs, this);
			// MyAppView.on('leftArrowUp', this.updatePrevious, this);
			// MyAppView.on('rightArrowUp', this.updateNext, this);
		},

		addInstitutions: function() {
			this.$('.cil-institutions').html(this.institutionListView.render().$el);
			this.institutionListView.afterRender();
		},

		// institutionChanged: function(newInstitution) {
		// 	this.institution = newInstitution;
		// 	if (this.institution != null) {
		// 		this.collection.add([new CourseInputModel(), new CourseInputModel()]);
		// 		this.$('.cil-add-course').show();
		// 	} else {
		// 		this.$('.cil-add-course').hide();
		// 	}
		// },

		updatePrevious: function() {
			this.updateUrl(-1);
		},

		updateNext: function() {
			this.updateUrl(1);
		},

		closeInputs: function() {
			this.$el.addClass('cil-hidden');
			$('body').addClass('cil-hidden');
		},

		openInputs: function() {
			this.$el.removeClass('cil-hidden');
			$('body').removeClass('cil-hidden');
		},

		addInput: function(model) {
			var view, $viewEl;
			view = new this.CourseInputView({model:model});
			view.render();
			$viewEl = view.$el;
			this.$('.cil-courses').append($viewEl);
			view.afterRender();
		},

		// workaround for mobile browsers
		// in which the view elem will become active if click event
		// is used by default backbone events
		afterRender: function() {
			var self = this, event;
			this.$('.cil-add-course').on('click', function() {
				self.addInputModel();
			});

			this.$('.cil-submit').on('click', function() {
				self.updateUrlEvent();
			});

			event = 'click';
			if (IS_MOBILE) {
				event = 'touchstart';
			}
			this.$('.cil-expand-arrow').on(event, function() {
				self.openInputs();
				return false;
			});

			return this;
		},

		addInputModel: function() {
			this.collection.add(new this.CourseInputModel());
		},

		updateUrlEvent: function(){
			this.updateUrl();
			this.closeInputs();
		},

		updateUrl: function(action) {

			if (action === undef) {
				this.page = 1;
			} else if(action === -1) {
				this.page -= 1;
			} else if (action === 1) {
				if (this.page < MyCourseCollection.maxPage) {
					this.page += 1;
				}
			}
			this.page = Math.max(1, this.page);
			var data = [], url = '', days = [], val;
			this.$('.cil-course-input').each(function() {
				data.push([
					$(this).find('select[name=subject_id]').val(),$(this).find('select[name=number]').val(),$(this).find('input[name=crn]').val()
				]);
			});
			_.each(data, function(item) {
				url = url + item.join('-') + '_';
			});
			$('input[name^="filter"]').each(function(){
				if ($(this).prop('checked')) {
					days.push($(this).val());
				}
			});
			days = days.join('-') + '_';
			url = 'c/' + this.page + '_' + days + url.substr(0, url.length-1);

			MyAppRouter.navigate(url, {trigger: true});
		},

		updateFilters: function(days) {
			var $boxes, self;
			self = this;
			$boxes = self.$('input[name^="filter"]');
			$boxes.prop('checked', false);

			_.each(days, function(day) {
				self.$('input[name="filter[' + day + ']"]').prop('checked', true);
			});
		}

	});

}(jQuery, Backbone, _, Handlebars, amplify));
