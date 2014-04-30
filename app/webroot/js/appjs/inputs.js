(function ($, Ember, Em, undef) {
  App.InputsController = Em.ObjectController.extend({
    needs: ['calendar'],

    page: Em.computed.alias('controllers.calendar.page'),

    institutions: [null].concat(window.config.institutions),
    selectedInstitution: null,

    courses: function(){
      return [];
    }.property(),
    coursesHash: function(val) {
      if (val) {
        this.set('courses', []);
        var self = this;

        val.split('_').forEach(function(courseHash){
          var props = courseHash.split('-');

          if (props.length === 3) {
            self.get('courses').pushObject({
              subject: props[0],
              number: props[1],
              crn: props[2]
            });
          }
        });
        return val;
      }

      var hash = [];

      this.get('courses').forEach(function(course){
        var subject = course.subject || '',
            number = course.number || '',
            crn = course.crn || '';

        if ((!Em.isEmpty(subject) && !Em.isEmpty(number)) || !Em.isEmpty(course.crn)) {
          hash.push(subject + '-' + number + '-' + crn);
        }
      });

      return hash.join('_');
    },

    weekdays: function() {
      return [{
        label: 'M',
        checked: true
      },{
        label: 'T',
        checked: true
      },{
        label: 'W',
        checked: true
      },{
        label: 'R',
        checked: true
      },{
        label: 'F',
        checked: true
      },{
        label: 'S',
        checked: true
      },{
        label: 'U',
        checked: true
      }];
    }.property(),
    weekdaysHash: function(val) {
      var weekdays = this.get('weekdays'),
          checkedDays = [];

      if (val) {
        weekdays.setEach('checked', false);
        val.split('-').forEach(function(day){
          weekdays[day-1].checked = true;
        });
        return val;
      }

      weekdays.forEach(function(day, index){
        if (day.checked) {
          checkedDays.push(index+1);
        }
      });
      return checkedDays.join('-');
    },

    subjects: function() {
      var selectedInstitution = this.get('selectedInstitution');

      if (selectedInstitution) {
        return [null].concat(this.get('selectedInstitution.subjects'));
      } else {
        return [];
      }
    }.property('selectedInstitution.subjects'),

    institutionObserver: function() {
      this.set('courses', []);
    }.observes('selectedInstitution'),

    hash: function(val) {
      if (val) {
        props = val.split('_');

        var institutions = this.get('institutions');

        this.set('selectedInstitution', institutions.findBy('id', props.splice(0, 1)[0]));
        this.set('page', props.splice(0, 1)[0]);
        this.weekdaysHash(props.splice(0, 1)[0]);
        this.coursesHash(props.join('_'));
        return val;
      }

      return [
        this.get('selectedInstitution.id'),
        this.get('page'),
        this.weekdaysHash(),
        this.coursesHash(),
      ].join('_');
    },

    actions: {
      addCourse: function() {
        this.get('courses').pushObject({
          subject: null,
          number: null,
          crn: null
        });
      },
      removeCourse: function(model) {
        this.get('courses').removeObject(model);
      },
      submit: function() {
        this.set('page', 1);
        this.transitionToRoute('courses', { hash: this.hash() });
      }
    }
  });
}(jQuery, Ember, Ember));
