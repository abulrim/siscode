(function ($, Ember, Em, undef) {
  var get = Ember.get;

  App.ChosenSelectComponent = Ember.Component.extend({
    tagName: null,
    optionLabelPath: 'content',
    optionValuePath: 'content',

    didInsertElement: function() {
      var self = this;

      Em.run.scheduleOnce('afterRender', function() {
        self.$('select').chosen();
      });
    },

    valueObserver: function() {
      var self = this;

      Em.run.scheduleOnce('afterRender', function() {
        if (this.$()) {
          self.$('select').trigger("chosen:updated");
        }
      });
    }.observes('value', 'content.@each')
  });

  App.CourseInputComponent = Em.Component.extend({
    removeCourse: "removeCourse",

    subjects: function() {
      return [];
    }.property(),

    numbers: function() {
      var subject = this.get('subjects').findBy('id', this.get('model.subject'));

      if (subject) {
        return [null].concat(get(subject, 'numbers'));
      } else {
        return [];
      }
    }.property('model.subject'),

    actions: {
      removeCourse: function() {
        this.sendAction("removeCourse", this.get('model'));
      }
    }
  });

  App.CourseSlotComponent = Ember.Component.extend({
    classNames: ['schedule-course-slot-wrapper'],
    classNameBindings: ['isTooltipShown:is-tooltip-shown'],
    isTooltipShown: false,
    debounce: null,

    didInsertElement: function() {
      var topPos,
          startTime,
          endTime,
          height,
          color = this.get('model.color');

      startTime = this.fixTime(this.get('model.startTime'));
      endTime = this.fixTime(this.get('model.endTime'));
      topPos = 40 + (startTime * 40);
      height = 40 + (endTime * 40) - topPos;

      this.$().css({
        top: topPos + 'px',
        height: height + 'px',
        backgroundColor: color.background,
        borderColor: color.border
      });
    },

    fixTime: function(time) {
      time = time.split(':');
      time[0] = +time[0];
      time[1] = +time[1];
      time = (time[0] - 7) + time[1]/60;
      return time;
    },

    mouseEnter: function() {
      Ember.run.cancel(this.get('debounce'));
      this.set('isTooltipShown', true);
    },

    mouseLeave: function() {
      var self = this;

      debounce = Ember.run.debounce(function(){
        Ember.run.cancel(self.get('debounce'));
        self.set('isTooltipShown', false);
      }, 200);
      this.set('debounce', debounce);
    }
  });

  App.CoursePaginatorComponent = Ember.Component.extend({
    fetch: "fetch",
    page: null,
    maxPage: null,

    showNext: function() {
      return this.get('page') < this.get('maxPage');
    }.property('page', 'maxPage'),

    showPrevious: function() {
      return this.get('page') > 1;
    }.property('page'),

    actions: {
      fetch: function(v) {
        this.sendAction('fetch', v);
      }
    }
  });
}(jQuery, Ember, Ember));
