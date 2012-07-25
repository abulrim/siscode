/*jslint browser:true,devel:true,white:true,nomen:true,bad_new:true*/
/*globals jQuery,Backbone,Handlebars,_, undefined*/ 

(function ($, Backbone, _, H, undef) {
	"use strict";

	//Handlebars 'equal' helper, enables us to select an option
	H.registerHelper('equal', function(lvalue, rvalue, options) {
		if (arguments.length < 3) {
			throw new Error("Handlebars Helper equal needs 2 parameters");
		}
		if(+lvalue !== +rvalue ) {
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
		CourseSlotView,
		PaginationView;
	
	/**** First module responsible for getting data from the inputs ****/
	
	//Course model
	CourseInputModel = Backbone.Model.extend({
		defaults: {
			subject_id: null,
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
		
		page: 1,
		
		events: {
			"click #add-course"	: 'addInputModel',
			"click .cil-submit"	: 'updateUrlEvent',
			'keypress .course-input' : 'keyLog'
		},
		
		keyLog: function(e) {
			if (e.keyCode === 13) {
				this.updateUrlEvent();
			}
		},
		
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
		
		updateUrlEvent: function(){
			this.updateUrl();
		},
		
		updateUrl: function(action) {
			if (action === undef) {
				this.page = 1;
			} else if(action === -1) {
				this.page -= 1;
			} else if (action === 1) {
				this.page += 1;
			}
			this.page = Math.max(1, this.page);
			var data = [], url = '', days = [], val;
			this.$('.cli-course-input').each(function() {
				data.push([
					$(this).find('select[name=subject_id]').val(),$(this).find('input[name=number]').val(),$(this).find('input[name=crn]').val()
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
		
		maxPage: 0,
		page: 0,
		
		//Specify the url where the courses will be fetched
		url: '/siscode/courses/fetch',
		
		//Each course has a specific color
		colors: [
			{
				background: '#73A5F7',
				border: '#03F'
			},
			{
				background: '#FF828E',
				border: '#D80505'
			},
			{
				background: '#FF71FF',
				border: '#B300B3'
			},
			{
				background: '#FFFF80',
				border: '#FF0'
			},
			{
				background: '#AFA',
				border: '#80FF00'
			},
			{
				background: '#FFA980',
				border: '#FF8000'
			}
		],
		
		initialize: function() {
			this.on('reset', this.fixSlots, this);
		},
		
		//Here we assign a color to each course and put the course info inside the course slots
		fixSlots: function(col) {
			var courseSlot, self, color;
			self = this;
			_.each(col.models, function(model, key) {
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
			this.maxPage = response.pagination.total;
			this.page = response.pagination.page;
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
			var self = this;
			this.collection.on('reset', this.addAll, this);
			this.$('.paginator').each(function() {
				$(this).html((new PaginationView({collection:self.collection})).render().$el);
			});
		},
		
		addAll: function(col) {
			this.$('.schedule-day').html('');
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
				startTime,
				endTime,
				height;
			self = this;
			_.each(model.get('CourseSlot').models, function (modelSlot) {
				view = new CourseSlotView({model:modelSlot});
				view.render();
				$list = self.$('.day-' + modelSlot.get('day'));
				$list.append(view.$el);
				startTime = self.fixTime(modelSlot.get('start_time'));
				endTime = self.fixTime(modelSlot.get('end_time'));
				topPos = 40 + (startTime * 40);
				height = 40 + (endTime * 40) - topPos;
				view.$el.css({
					position: 'absolute',
					top: topPos + 'px',
					height: height + 'px'
				});
			});
		},
		
		fixTime: function(time) {
				time = time.split(':');
				time[0] = +time[0];
				time[1] = +time[1];
				time = (time[0] - 7) + time[1]/60;
				return time;
		}
		
	});
	
	//The CourseSlot view
	CourseSlotView = Backbone.View.extend({
		tagName: 'div',
		className: 'course-slot',
		
		render: function() {
			var $template,
				compiled,
				data;
			$template = $('#course-slot-view-tmpl').html();
			compiled = H.compile($template);
			data = this.model.toJSON();
			data.Course = this.model.courseModel.toJSON();
			this.$el.html(compiled(data));
			this.$el.css({
				backgroundColor: this.model.courseModel.get('color').background,
				borderColor: this.model.courseModel.get('color').border
			});
			return this;
		}
	});
	
	PaginationView = Backbone.View.extend ({
		tagName: 'div',
		
		events: {
			'click .next': 'updateUrlNext',
			'click .previous': 'updateUrlPrevious'
		},
		
		updateUrlNext: function() {
			if (+this.collection.page < +this.collection.maxPage) {
				MyCourseInputListView.updateUrl(1);
			}
		},
		
		updateUrlPrevious: function() {
			MyCourseInputListView.updateUrl(-1);
		},
		
		initialize: function() {
			this.collection.on('reset', this.render, this);
		},
		
		render: function() {
			var $template, compiled, data;
			$template = $('#pagination-view-tmpl').html();
			compiled = H.compile($template);
			data = {
				page: this.collection.page,
				maxPage: this.collection.maxPage
			};
			this.$el.html(compiled(data));
			if (+this.collection.page >= +this.collection.maxPage) {
				this.$('.next').addClass('disabled');
			}
			if (+this.collection.page === 1) {
				this.$('.previous').addClass('disabled');
			}
			if (this.collection.maxPage === 0) {
				this.$el.hide();
			} else {
				this.$el.show();
			}
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
				array,
				days,
				page,
				joinedCourses = [],
				url;
			data = d.split('_');
			page = data.splice(0,1);
			page = page[0];
			days = data.splice(0,1);
			days = days[0].split('-');
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
			
			MyCourseInputListView.updateFilters(days);
			MyCourseInputListView.page = +page;
			
			_.each(data, function(course, key) {
				if (course.crn !== '') {
					joinedCourses.push('--' + course.crn);
				} else if (course.subject_id !== '' && course.number !== '') {
					joinedCourses.push(course.subject_id + '-' + course.number + '-');
				}
			});
			joinedCourses = _.sortBy(joinedCourses, function(item) {
				return item;
			});
			url = page + '_' + days.join('-') + '_' + joinedCourses.join('_') + '.json';

			MyCourseCollection.url = '/siscode/courses/fetch/' + url;
			MyCourseCollection.fetch();
		}
	});
	MyAppRouter = new AppRouter();
	
	Backbone.history.start({pushState: true, root: '/siscode/'});

	/**** End of Router Module ****/
	
}(jQuery, Backbone, _, Handlebars));