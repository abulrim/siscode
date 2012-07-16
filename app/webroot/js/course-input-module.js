/*jslint browser:true,devel:true,white:true,nomen:true,bad_new:true*/
/*globals jQuery,Backbone,Handlebars,_, undefined*/ 

(function ($, Backbone, _, H, undef) {
	"use strict";
	
	 H.registerHelper('equal', function(lvalue, rvalue, options) {
		if (arguments.length < 3)
				throw new Error("Handlebars Helper equal needs 2 parameters");
		if( lvalue!=rvalue ) {
				return options.inverse(this);
		} else {
				return options.fn(this);
		}
	});

	var CourseInputModel,
		CourseInputCollection,
		MyCourseInputCollection,
		CourseInputListView,
		MyCourseInputListView,
		CourseInputView,
		MyCourseInputView,
		AppRouter,
		MyAppRouter,
		
		CourseModel,
		MyCourseModel,
		CourseView,
		MyCourseView,
		CourseCollection,
		MyCourseCollection,
		CourseScheduleView,
		MyCourseScheduleView,
		
		CourseSlotModel,
		CourseSlotCollection,
		CourseSlotView;
	
	CourseInputModel = Backbone.Model.extend({
		defaults: {
			subject_id: 1,
			number: null,
			crn: null
		},
		
		clear: function() {
			this.destroy();
		}
	});
	
	CourseInputView = Backbone.View.extend({
		tagName: 'div',
		className: 'cli-course-input',
		
		render: function() {
			var $tmpl, 
				compiled;
			$tmpl = $('#course-input-tmpl');
			compiled = H.compile($tmpl.html());
			this.$el.html(compiled(this.model.toJSON()));
			return this;
		},
		
		initialize: function() {
      this.model.on('destroy', this.remove, this);
    },
		
		removeCourse: function() {
			this.model.clear();
		},
		
		events: {
			'click .remove-course' : 'removeCourse'
		}
	});

	CourseInputCollection = Backbone.Collection.extend({
		model: CourseInputModel
	});
	
	CourseInputListView = Backbone.View.extend({
		el: '.cil',
		
		initialize: function() {
			this.collection.on('add', this.addInput, this);
		},
		
		addInput: function(model) {
			var view, $viewEl;
			view = new CourseInputView({model:model});
			view.render();
			$viewEl = view.$el;
			this.$('.cil-courses').append($viewEl);
		},
		
		addInputModel: function() {
			this.collection.add(new CourseInputModel());
			
		},
		
		updateUrl: function() {
			var data = [], url = '';
			this.$('.cli-course-input').each(function() {
				data.push([
					$(this).find('select[name=subject_id]').val(),$(this).find('input[name=number]').val(),$(this).find('input[name=crn]').val()
				]);
			});
			_.each(data, function(item) {
				url = url + item.join('-') + '_';
			});
			url = 'c/' + url.substr(0, url.length-1);
			MyAppRouter.navigate(url, {trigger: true});
		},
		
		events: {
			"click #add-course"	: 'addInputModel',
			"click .cil-submit"	: 'updateUrl'
		}
	});
	
	MyCourseInputCollection = new CourseInputCollection();
	MyCourseInputListView = new CourseInputListView({collection:MyCourseInputCollection});
	
	/**************************************************************/
	/* Here starts the second module */
	CourseSlotModel = Backbone.Model.extend({
	});
	
	CourseSlotCollection = Backbone.Collection.extend({
		model: CourseSlotModel
	});
	
	CourseModel = Backbone.Model.extend({
	});
	
	CourseCollection = Backbone.Collection.extend({
		model: CourseModel,
		url: '/siscode/courses/fetch',
		
		initialize: function() {
			this.on('reset', this.fixSlots, this);
		},
		
		fixSlots: function(col) {
			var courseSlot;
			_.each(col.models, function(model){
				courseSlot = model.get('CourseSlot');
				_.each(courseSlot.models, function(courseSlotModel) {
					courseSlotModel.courseModel = model;
				});
			});
		},
		
		parse: function(response) {
			var data = [];
			_.each(response.content, function(item) {
				var courseSlotModels = [], modelData;
				_.each(item.CourseSlot, function(courseSlotData) {
					courseSlotModels.push(new CourseSlotModel(courseSlotData));
				});
				modelData = item.Course;
				modelData.CourseSlot = new CourseSlotCollection(courseSlotModels);
				data.push(modelData);
			});
			return data;
		}
	});
	
	CourseScheduleView = Backbone.View.extend({
		el: '.schedule',
		
		initialize: function() {
			this.collection.on('reset', this.addAll, this);
		},
		
		addAll: function(col) {
			this.$('.schedule-list').html('');
			var self = this;
			_.each(col.models, function(model) {
				self.addOne(model);
			});
		},
		
		addOne: function(model) {	
			var view,
				$list;
			$list = this.$('.schedule-list');
			_.each(model.get('CourseSlot').models, function (modelSlot) {
				view = new CourseSlotView({model:modelSlot});
				view.render();
				$list.append(view.$el);
			});
		}
	});
	
	CourseSlotView = Backbone.View.extend({
		tagName: 'li',
		
		render: function() {
			var $template,
				compiled,
				data;
			$template = $('#course-slot-view-tmpl').html();
			compiled = H.compile($template);
			data = this.model.toJSON();
			data.Course = this.model.courseModel.toJSON();
			this.$el.html(compiled(data));
			return this;
		}
	});
	
	MyCourseCollection = new CourseCollection();
	MyCourseScheduleView = new CourseScheduleView({collection:MyCourseCollection});
	
	AppRouter = Backbone.Router.extend({
		routes: {
			'': 'index',
			'c/:d': 'fillCourseInput'
		},
		
		index: function() {
			MyCourseInputCollection.add([new CourseInputModel(), new CourseInputModel()]);
		},

		fillCourseInput: function(d) {
			var data = [],
				models,
				array;
			data = d.split('_');
			_.each(data, function(item, key) {
				array = item.split('-');
				data[key] = {
					subject_id: array[0],
					number: array[1],
					crn: array[2]
				};
			});
			
			models = [];
			_.each(MyCourseInputCollection.models, function(model) {
				models.push(model);
			});
			_.each(models, function(model) {
				model.clear();
			});
			
			_.each(data, function(modelData) {
				MyCourseInputCollection.add(new CourseInputModel(modelData));
			});
		
			MyCourseCollection.fetch({
				type: 'post',
				data: {
					data: data
				}
			});
		}
	});
	
	MyAppRouter = new AppRouter();
	
	Backbone.history.start({pushState: true, root: '/siscode/'});

}(jQuery, Backbone, _, Handlebars));