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
	// Calls router.institutionChanged when institution selected
	App.InstitutionListView = Backbone.View.extend({

		template: H.compile($('#institutions-select').html()),

		events: {
			'change select[name=institution_id]': 'institutionChanged'
		},

		institutionChanged: function() {
			var modelId,
				selectedModel;

			modelId = this.$('select[name=institution_id]').val();
			if (modelId) {
				selectedModel = this.collection.get(modelId).toJSON();
			} else {
				selectedModel = {subjects:[]};
			}
			this.trigger('institutionChanged', selectedModel);
		},

		initialize: function(options) {
			this.collection = new App.InstitutionCollection(options.institutions),
			this.collection.on('reset', this.render, this);
		},

		selectInstitution: function(institutionId) {
			this.$('select[name=institution_id]').val(institutionId).trigger('change').trigger('chosen:updated');
		},

		render: function() {
			this.$el.html(this.template({
				institutions: this.collection.toJSON()
			}));

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
		subjectCollection: null,

		events: {
			'change select[name=subject_id]': 'fillNumber',
			'click .cil-remove-course': 'removeCourse',
			'keyup .cil-course-crn': 'crnKeyUp'
		},

		// Disables subjects and numbers select when crn present
		crnKeyUp: function(event) {
			if(this.$(event.target).val().length > 0) {
				this.$('select').prop('disabled', true).trigger('chosen:updated');
			} else {
				this.$('select').prop('disabled', false).trigger('chosen:updated');
			}
		},

		// Fills the numbers when subject select
		fillNumber: function() {
			var modelId,
				numbers,
				selectedModel;

			modelId = this.$('select[name=subject_id]').val();
			if (modelId) {
				selectedModel = this.subjectCollection.get(modelId).toJSON();
			} else {
				selectedModel = {numbers:[]};
			}
			this.$('select[name=number]').html(this.numberTemplate(selectedModel))
			.trigger('chosen:updated');
		},

		render: function() {
			var view, 
				data = this.model.toJSON();

			data.subjects = this.subjectCollection.toJSON();
			this.$el.html(this.template(data))
			.find('select[name=subject_id]').val(this.model.get('subject_id')).trigger('change')
			.end()
			.find('select[name=number]').val(this.model.get('number'));

			return this;
		},

		afterRender: function() {
			this.$('select').chosen();
		},

		initialize: function(options) {
			this.subjectCollection = new App.SubjectCollection(options.subjects);
			this.model.on('remove', this.remove, this);
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
	// needs insitutions and router
	App.CourseInputListView = Backbone.View.extend({
		el: '.cil',
		institutionListView: null,
		collection: new App.CourseInputCollection(),
		institution: null,
		closed: false,

		events: {
			'keypress .course-input': 'keyLog'
		},

		keyLog: function(e) {
			if (e.keyCode === 13) {
				this.updateUrlEvent();
			}
		},

		initialize: function(options) {
			this.router = options.router;
			this.initInstitutionListView(options);
			this.afterRender();

			this.collection.on('add', this.addInput, this);
			this.collection.on('reset', this.collectionReset, this);
			this.router.on('urlChanged', this.dehasherize, this)
		},

		initInstitutionListView: function(options) {
			var institutionListView = new App.InstitutionListView({
				institutions: options.institutions
			});

			this.$('.cil-institutions').html(institutionListView.render().$el);
			institutionListView.afterRender();

			institutionListView.on('institutionChanged', this.institutionChanged, this);

			this.institutionListView = institutionListView;
			this.institutionChanged();
		},

		institutionChanged: function(newInstitution) {
			this.institution = newInstitution;
			this.collection.reset();
			if (this.institution != null) {
				this.$('.cil-add-course').show();
			} else {
				this.$('.cil-add-course').hide();
			}
		},

		collectionReset: function(collection, options) {
			_.each(options.previousModels, function(model) {
		        model.trigger('remove');
		    });
			this.closeInputs();
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
			var view;
			view = new App.CourseInputView({ 
				model: model,
				subjects: this.institution.subjects
			});
			view.render();
			this.$('.cil-courses').append(view.$el);
			view.afterRender();
		},

		// workaround for mobile browsers
		// in which the view elem will become active if click event
		// is used by default backbone events
		afterRender: function() {
			var self = this;

			this.$('.cil-add-course').on('click', function() {
				self.addInputModel();
			});

			this.$('.cil-submit').on('click', function() {
				self.updateUrlEvent();
			});

			this.$('.cil-expand-arrow').on(CLICK_OR_TOUCH, function() {
				self.openInputs();
				return false;
			});

			return this;
		},

		addInputModel: function() {
			this.collection.add(new App.CourseInputModel());
		},

		updateUrlEvent: function(){
			this.updateUrl();
			this.closeInputs();
		},

		updateUrl: function(action) {
			var props = this.getProps();

			this.router.handleUrl({
				page: 1,
				institution: this.institution,
				days: props.days,
				courses: props.courses
			});
		},

		getProps: function() {
			var courses = [],
				days = [];

			// Fill courses
			this.$('.cil-course-input').each(function() {
				courses.push({
					subject: $(this).find('select[name=subject_id]').val(),
					number: $(this).find('select[name=number]').val(),
					crn: $(this).find('input[name=crn]').val()
				});
			});

			// Fill days
			$('input[name^="filter"]').each(function(){
				if ($(this).prop('checked')) {
					days.push($(this).val());
				}
			});

			return {
				courses: courses,
				days: days
			}
		},

		// {institution}_{page}_{days separated by '-'}_{subject-number-crn}
		dehasherize: function(hash) {
			var data = [],
				array,
				days,
				page,
				institution,
				self = this,
				props;

			data = hash.split('_');

			institution = data.splice(0, 1)[0];
			page = data.splice(0, 1)[0];
			days = data.splice(0, 1)[0].split('-');

			this.institutionListView.selectInstitution(institution);
			this.updateFilters(days);

			this.collection.reset();
			_.each(data, function(course, key) {
				array = course.split('-');
				self.collection.add(new App.CourseInputModel({
					subject_id: array[0],
					number: array[1],
					crn: array[2]
				}));
			});

			props = this.getProps();

			this.router.updateProps({
				page: page,
				institution: this.institution,
				days: props.days,
				courses: props.courses
			});
		},

		updateFilters: function(days) {
			var $boxes = this.$('input[name^="filter"]'), 
				self = this;

			$boxes.prop('checked', false);

			_.each(days, function(day) {
				self.$('input[name="filter[' + day + ']"]').prop('checked', true);
			});
		}
	});
	
	window.App = App;

}(jQuery, Backbone, _, Handlebars, amplify));
