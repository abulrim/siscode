(function ($, Ember, Em, amplify, undef) {

  App.FooterController = Em.ObjectController.extend({
    needs: ['courses'],
    tagName: false,
    isOpen: false,

    uniqueId: function() {
      return (new Date()).getTime();
    },

    savedCombinations: function() {
      return [];
    }.property(),

    init: function() {
      this._super();
      this.set('savedCombinations', amplify.store("savedCombinations"));
    },

    sync: function() {
      amplify.store("savedCombinations", this.get('savedCombinations'));
    },

    actions: {
      toggleOpen: function() {
        this.toggleProperty('isOpen');
      },

      remove: function(combination) {
        var combinations = this.get('savedCombinations');
        combinations.removeObject(combination);
        this.sync();
      },

      edit: function(newCombination) {
        var combinations = this.get('savedCombinations'),
            combination = combinations.findBy('id', newCombination.id);

        combination.name = newCombination.name;
        this.sync();
      },

      addNew: function() {
        var combinations = this.get('savedCombinations'),
            controller = this.get('controllers.courses'),
            hash = controller.get('hash'),
            name = '';

        controller.forEach(function(course, index){
          if (index !== 0) {
            name += ' - ';
          }
          name += course.Course.crn + ' (' + course.Subject.code + ' ' + course.Course.number + ')';
        });

        if (!combinations.findBy('url', hash)) {
          combinations.pushObject({
            id: this.uniqueId(),
            name: name.trim(),
            url: hash
          });
          this.set('isOpen', true);
        }
        this.sync();
      }
    }
  });

  App.FooterInputComponent = Ember.Component.extend({
    remove: 'remove',
    edit: 'edit',
    classNames: ['foot-bar-combination-name-wrapper'],
    isEdit: false,

    editRecord: function() {
      if (this.get('isEdit')) {
        this.sendAction('edit', this.get('model'));
      }
      this.toggleProperty('isEdit');
    },

    actions: {
      remove: function() {
        this.sendAction('remove', this.get('model'));
      },

      edit: function() {
        this.editRecord();
      }
    }
  });

}(jQuery, Ember, Ember, amplify));
