/*jslint browser:true,devel:true,white:true,nomen:true,bad_new:true*/
/*globals jQuery,Backbone,Handlebars,_, undefined, FB*/

(function ($, Backbone, _, H, amplify, undef) {
  "use strict";

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
    url: webroot + 'courses/fetch',

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
        item.Course.subject_code = MySubjectCollection.get(item.Course.subject_id).get("code");
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

    errorMessages: [
      "No results! Take a break this semester, work at McDonalds instead",
      "No results! Seriously, flipping burgers is fun",
      "No results! I like fries with my burger",
      "No results! One tawouk, toum extra please",
      "Hooray! No need to come to uni this semester. Just kidding, No results",
      "No results! Have you considered dropping out?",
      "No results! Try LUCK 101",
      "No results! Never give up, Bill Gates was a dropout and look where he is today"
    ],

    initialize: function() {
      var self = this;
      this.collection.on('reset', this.addAll, this);
      this.$('.paginator').each(function() {
        $(this).html((new PaginationView({collection:self.collection})).render().$el);
      });
    },

    loading: function(show) {
      if (show) {
        this.$('.schedule-loader').show();
      } else {
        this.$('.schedule-loader').hide();
      }
    },

    addAll: function(col, options) {
      var $error,self;
      this.$('.schedule-day').html('');
      self = this;
      $error  = this.$('.schedule-empty-error');
      if (options.success && col.models.length === 0) {
        $error.html(this.errorMessages[Math.floor(Math.random()*this.errorMessages.length)]);
        $error.show();
      } else {
        $error.hide();
      }
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
        view.afterInsert();
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
    className: 'schedule-course-slot',

    render: function() {
      var $template,
        compiled,
        data;
      $template = $('#course-slot-view-tmpl').html();
      compiled = H.compile($template);
      data = this.model.toJSON();
      data.Course = this.model.courseModel.toJSON();
      data.start_time = data.start_time.substr(0, 5);
      data.end_time = data.end_time.substr(0, 5);
      this.$el.html(compiled(data));
      this.$('.schedule-course-slot-wrapper').css({
        backgroundColor: this.model.courseModel.get('color').background,
        borderColor: this.model.courseModel.get('color').border
      });

      return this;
    },

    afterInsert: function() {
      this.$el.tooltip({
        tooltip: '.tooltip-content'
      });
    }
  });

  PaginationView = Backbone.View.extend ({
    tagName: 'div',

    events: {
      'click .paginator-next': 'updateUrlNext',
      'click .paginator-previous': 'updateUrlPrevious'
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
        this.$('.paginator-next').addClass('paginator-disabled');
      }
      if (+this.collection.page === 1) {
        this.$('.paginator-previous').addClass('paginator-disabled');
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

}(jQuery, Backbone, _, Handlebars, amplify));
