/*jslint browser:true,devel:true,white:true,nomen:true,bad_new:true*/
/*globals jQuery,Backbone,Handlebars,_, undefined*/ 

(function ($, Backbone, _, H, undef) {
	"use strict";
	
	var CourseInputModel,
		CourseInputCollection,
		MyCourseInputCollection,
		CourseInputListView,
		MyCourseInputListView,
		CourseInputView,
		MyCourseInputView;
	
	CourseInputModel = Backbone.Model.extend({
		defaults: {
			subject_id: 1,
			number: null,
			crn: null
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
			var view = new CourseInputView({model:model});
			view.render();
			var $viewEl = view.$el;
			this.$el.append($viewEl);
		}
	});
	
	CourseInputView = Backbone.View.extend({
		tagName: 'div',
		
		 render: function() {
			 var $tmpl = $('#course-input-tmpl');
			 var compiled = H.compile($tmpl.html());
			 this.$el.html(compiled());
			 return this;
		 }
	});
	
	MyCourseInputCollection = new CourseInputCollection();
	MyCourseInputListView = new CourseInputListView({collection:MyCourseInputCollection});
	
	MyCourseInputCollection.add([new CourseInputModel(), new CourseInputModel()]);

	
	
}(jQuery, Backbone, _, Handlebars));
