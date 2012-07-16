/*jslint browser:true,devel:true,white:true,nomen:true,bad_new:true*/
/*globals jQuery,Backbone,Handlebars,_, undefined*/ 

(function ($, Backbone, _, H, undef) {
	"use strict";

	var CourseModel,
		MyCourseModel,
		CourseView,
		MyCourseView,
		CourseCollection,
		MyCourseCollection,
		CourseScheduleView,
		MyCourseScheduleView,
		CourseRouter,
		MyCourseRouter;
	
	CourseModel = Backbone.Model.extend({
	});
	
	CourseCollection = Backbone.Collection.extend({
		model: CourseModel
	});
	
	CourseScheduleView = Backbone.View.extend({
		
	});
	
	MyCourseCollection = new CourseCollection();
	
	CourseRouter = Backbone.Router.extend({
		routes: {
			'': 'index',
			'c/:d': 'fillCourseInput'
		},
		
		index: function() {
			
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
			
			console.log(data);
//			MyCourseCollection.fetch({
//				data: {
//					data: {
//						
//					}
//				}
//			});
		}
	});
	
	MyCourseRouter = new CourseRouter();
	
	
	//Backbone.history.start({pushState: true, root: '/siscode/'});
	
}(jQuery, Backbone, _, Handlebars));