/*jslint browser:true,devel:true,white:true,nomen:true,bad_new:true*/
/*globals jQuery,Backbone,Handlebars,_, undefined*/ 

(function ($, Backbone, _, H, undef) {
	"use strict";

	//Handlebars 'equal' helper, enables us to select an option
	H.registerHelper('equal', function(lvalue, rvalue, options) {
		if (arguments.length < 3)
			throw new Error("Handlebars Helper equal needs 2 parameters");
		if( lvalue!=rvalue ) {
			return options.inverse(this);
		} else {
			return options.fn(this);
		}
	});
	
	
	//All variables go here
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
	
	
	/**** First module responsible for getting data from the inputs ****/
	
	//Course model
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
	
	//Course view
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
		
		afterRender: function() {
			this.$('select').chosen();
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

	//Course collection, has all the courses inside it
	CourseInputCollection = Backbone.Collection.extend({
		model: CourseInputModel
	});
	
	//Course collection view, updates the URL and fills the courses
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
			view.afterRender();
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
	
	
	//Create the necessary models and views
	MyCourseInputCollection = new CourseInputCollection();
	MyCourseInputListView = new CourseInputListView({collection:MyCourseInputCollection});
	
	
	/**** End of First Module ****/
	
	/**********************************************************************/
	
	/**** Second module responsible for displaying the actual schedule ****/
	
	//Each Course has a CourseSlot that will appear on the screen
	CourseSlotModel = Backbone.Model.extend({
	});
	
	//The CourseSlot collection
	CourseSlotCollection = Backbone.Collection.extend({
		model: CourseSlotModel
	});
	
	//The CourseModel that we will be fetching from the server
	CourseModel = Backbone.Model.extend({
	});
	
	//The CourseModels will be encapsulated inside the CourseCollection
	CourseCollection = Backbone.Collection.extend({
		model: CourseModel,
		
		//Specify the url where the courses will be fetched
		url: '/siscode/courses/fetch',
		
		//Each course has a specific color
		colors: ['#73A5F7','#FF828E', '#FF71FF', '#FFFF80', '#AFA', '#FFA980'],
		
		initialize: function() {
			this.on('reset', this.fixSlots, this);
		},
		
		//Here we assign a color to each course and put the course info inside the course slots
		fixSlots: function(col) {
			var courseSlot, self, color;
			self = this;
			_.each(col.models, function(model, key){
				//Set a color for each CourseModel
				if(self.colors[key] !== undef) {
					color = self.colors[key];
				} else {
					color = self.colors[key%self.colors.length];
				}
				model.set('color', color);
				//Get the courseSlot which are in turn models
				courseSlot = model.get('CourseSlot');
				_.each(courseSlot.models, function(courseSlotModel) {
					//Put the courseModel in the courseSlot (inception!) so we can easily retrieve the course info
					courseSlotModel.courseModel = model;
				});
			});
		},
		
		//Get the data from the server and create the corresponding models and collections
		parse: function(response) {
			var data = [];
			//The reponse that we get from the server has 'status' and 'content', inside content we have an array that has 'Course' and 'CourseSlot' 
			_.each(response.content, function(item) {
				var courseSlotModels = [], modelData;
				_.each(item.CourseSlot, function(courseSlotData) {
					//Fill the courseSlotModels array with each course slot
					courseSlotModels.push(new CourseSlotModel(courseSlotData));
				});
				//inside modelData put the Course info and the CourseSlot as a collection
				modelData = item.Course;
				modelData.CourseSlot = new CourseSlotCollection(courseSlotModels);
				//Store all the model data inside 'data' variable that will be returned later
				data.push(modelData);
			});
			return data;
		}
	});
	
	//CourseScheduleView is the view of the CourseCollection holding all the courses fetched
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
				$list,
				self,
				topPos,
				startTime;
			self = this;
			_.each(model.get('CourseSlot').models, function (modelSlot) {
				view = new CourseSlotView({model:modelSlot});
				view.render();
				$list = self.$('.day-' + modelSlot.get('day'));
				$list.append(view.$el);
				startTime = modelSlot.get('start_time');
				startTime = startTime.split(':');
				startTime[0] = +startTime[0];
				startTime[1] = +startTime[1];
				startTime = (startTime[0] - 7) + startTime[1]/60;
				topPos = 40+startTime*40;
				view.$el.css({
					position: 'absolute',
					top: topPos + 'px'
				});
			});
		}
	});
	
	//The CourseSlot view
	CourseSlotView = Backbone.View.extend({
		tagName: 'div',
		
		render: function() {
			var $template,
				compiled,
				data;
			$template = $('#course-slot-view-tmpl').html();
			compiled = H.compile($template);
			data = this.model.toJSON();
			data.Course = this.model.courseModel.toJSON();
			this.$el.html(compiled(data));
			this.$el.css('background-color', this.model.courseModel.get('color'));
			return this;
		}
	});
	
	//Create the necessary collections and views
	MyCourseCollection = new CourseCollection();
	MyCourseScheduleView = new CourseScheduleView({collection:MyCourseCollection});
	
	/***** End of the second module ****/
	
	/**********************************************************************/
	
	/**** Router module common to both modules  ****/
	
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

	/**** End of Router Module ****/
	
}(jQuery, Backbone, _, Handlebars));