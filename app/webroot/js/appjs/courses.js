(function ($, Ember, Em, undef) {
  var resolve = Ember.RSVP.resolve;

  App.ScheduleRoute = Ember.Route.extend({

    model: function(params) {
      this.controllerFor('inputs').hash(params.hash);
      return { hash: params.hash };
    }
  });

  App.CoursesRoute = Ember.Route.extend({
    hash: null,

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

    model: function(params) {
      var hash = this.modelFor('schedule').hash;

      this.set('hash', hash);

      return resolve(Ember.$.ajax({
        url: window.config.webroot + 'courses/fetch/' + hash + '.json',
        dataType: 'json'
      }));
    },

    setupController: function(controller, model) {

      var errors = this.get('errorMessages');

      controller.setProperties({
        page: +model.pagination.page,
        maxPage: model.pagination.total,
        model: model.content,
        hash: this.get('hash'),
        errorMessage: errors[Math.floor(Math.random()*errors.length)]
      });
    },

    actions: {
      fetch: function(v) {
        var controller = this.controllerFor('inputs'),
            page = controller.get('page'),
            maxPage = this.get('controller.maxPage'),
            hash;

        if (v === 'next' && page < maxPage) {
          page++;
        } else if (v === 'previous' && page > 1) {
          page--;
        }

        if (page === controller.get('page')) {
          return
        }

        controller.set('page', page);
        hash = controller.hash();
        this.transitionTo('courses', { hash: hash });
      }
    }
  });

  App.CoursesController = Ember.ArrayController.extend({
    errorMessage: null,
    needs: ['calendar'],
    page: Ember.computed.alias('controllers.calendar.page'),
    maxPage: Ember.computed.alias('controllers.calendar.maxPage'),
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

    days: function() {
      var days = [[], [], [], [], [], [], []],
          colors = this.get('colors'),
          color;

      this.forEach(function(course, index) {

        color = colors[index%colors.length];

        course.CourseSlot.forEach(function(slot){
          days[slot.day - 1].pushObject({
            color: color,
            startTime: slot.start_time,
            endTime: slot.end_time,
            building: slot.building,
            instructor: slot.Instructor.name,
            course: {
              crn: course.Course.crn,
              code: course.Subject.code,
              subject: course.Subject.name,
              number: course.Course.number,
              title: course.Course.title
            }
          });
        });
      });
      return days;
    }.property('@each')
  });

  App.CoursesView = Ember.View.extend({
    tagName: false,

    didInsertElement: function() {
      var self = this;

      Em.$('body').on('keyup.schedule-' + this.get('elementId'), function(e){
        if (e.which === 39) {
          self.get('controller').send('fetch', 'next');
        } else if (e.which === 37) {
          self.get('controller').send('fetch', 'previous');
        }
      });
    },

    willDestroyElement: function() {
      Ember.$('body').off('.schedule-' + this.get('elementId'));
    },
  });

}(jQuery, Ember, Ember));
