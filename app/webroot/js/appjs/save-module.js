/*jslint browser:true,devel:true,white:true,nomen:true,bad_new:true*/
/*globals jQuery,Backbone,Handlebars,_, undefined, FB*/

(function ($, Backbone, _, H, amplify, undef) {
  "use strict";

  /**** third module  ****/
  SavedModel = Backbone.Model.extend({
    defaults: {
      url: null,
      name: null
    },

    sync: function(method, model, options) {
      var combinations, createdModel, modelId, index;
      if (method === 'create') {
        combinations = amplify.store( "savedCombinations");
        if (!combinations) {
          combinations = [];
        }
        createdModel = this.toJSON();
        createdModel.id = _.uniqueId();
        combinations.push(createdModel);
        amplify.store("savedCombinations", combinations);
        options.success(createdModel);
      } else if(method === 'delete') {
        modelId = model.get("id");
        combinations = amplify.store("savedCombinations", combinations);
        index = null;
        _.each(combinations, function(combination, key) {
          if (combination.id === modelId) {
            index = key;
            return false;
          }
        });
        if (index !== null) {
          combinations.splice(index, 1);
        }
        amplify.store( "savedCombinations", combinations);
      } else if (method === 'update') {
        modelId = model.get("id");
        combinations = amplify.store("savedCombinations", combinations);
        index = null;
        _.each(combinations, function(combination, key) {
          if (combination.id === modelId) {
            combinations[key] = model.toJSON();
            return false;
          }
        });
        amplify.store( "savedCombinations", combinations);
      }
    },

    clear: function() {
      this.destroy();
    }
  });

  SavedView = Backbone.View.extend({
    tagName: 'li',

    editMode: false,

    $template: H.compile($('#foot-bar-combination-tmpl').html()),


    events: function() {
        var event = {};
        event[clickOrTouch + ' a'] = 'goToSavedUrl';
        event[clickOrTouch + ' .foot-bar-remove'] = 'removeCombination';
        event[clickOrTouch + ' .foot-bar-edit'] = 'editCombination';
        event['keypress input'] = 'updateCombination';
        return event;
    },

    editCombination: function() {
      if (this.editMode) {
        this.update();
      } else {
        this.editMode = true;
        this.$('.foot-bar-combination-name').hide();
        this.$('input').show().focus().select();
      }
      return false;
    },

    updateCombination: function(e) {
      if (e.keyCode === 13) {
        this.update();
      }
    },

    update: function() {
      var newName;
      this.$('.foot-bar-combination-name').show();
      newName = this.$('input').hide().val();
      this.model.save({name: newName});
      this.editMode = false;

      if(window.woopraTracker) {
        window.woopraTracker.pushEvent({
          name: 'edit',
          url: window.location.origin + webroot + 'c/' + this.model.get('url'),
          title: newName
        });
      }
    },

    removeCombination: function() {
      if(window.woopraTracker) {
        window.woopraTracker.pushEvent({
          name: 'delete',
          url: window.location.origin + webroot + 'c/' + this.model.get('url'),
          title: this.model.get('name')
        });
      }
      this.model.clear();
      return false;
    },

    goToSavedUrl: function() {
      MyAppRouter.navigate('c/' + this.model.get("url"), {trigger: true});
      return false;
    },

    initialize: function() {
      this.model.on('destroy', this.remove, this);
      this.model.on('change:name', this.render, this);
    },

    render: function() {
      this.$el.html(this.$template(this.model.toJSON()));
      return this;
    }
  });

  SavedCollection = Backbone.Collection.extend({
    model: SavedModel,

    sync: function(method, collection, options) {
      if (method === 'read') {
        var savedCombinations;
        savedCombinations = amplify.store( "savedCombinations");
        options.success(savedCombinations);
      }
    }
  });

  SavedCollectionView = Backbone.View.extend({
    el: '.foot-bar',
    toggled: false,

    events: function() {
      var events = IS_MOBILE ? {
          'touchstart .foot-bar-toggle': 'toggleFoot',
          'touchstart .foot-bar-add': 'save'
        }:{
          'click .foot-bar-toggle': 'toggleFoot',
          'click .foot-bar-add': 'save'
      };
      return events;
    },

    initialize: function() {
      this.collection.on('add', this.addOne, this);
      this.collection.on('reset', this.addAll, this);
    },

    addAll: function(collection) {
      var self;
      self = this;
      _.each(collection.models, function(model) {
        self.addSaved(model, true);
      });
    },

    addOne: function(model) {
      this.addSaved(model);
    },

    addSaved: function(model, ignore) {
      var view, $viewEl;
      view = new SavedView({model:model});
      view.render();
      $viewEl = view.$el;
      this.$('.foot-bar-combination').prepend($viewEl);
      if (!ignore) {
        view.editCombination();
        if (!this.toggled) {
          this.toggleFoot();
        }
      }
    },

    toggleFoot: function() {
      var arrow;
      arrow = this.$('.foot-bar-arrow');
      if (this.toggled === false) {
        this.$el.addClass('toggled');
        arrow.addClass('down');
        this.toggled = true;
      } else {
        this.$el.removeClass('toggled');
        arrow.removeClass('down');
        this.toggled = false;
      }
      return false;
    },

    save: function() {
      if (this.collection.where({url: MyAppRouter.url}).length === 0 && MyAppRouter.url.length !== 0) {
        var link = '';

        _.each(MyCourseCollection.models, function(model) {
          link = link + model.get("crn") + ' (' + model.get("subject_code") + ' ' + model.get("number") + ')' + ' - ';
        });
        link = link + '(page: ' + MyCourseInputListView.page + ')';

        if(window.woopraTracker) {
          window.woopraTracker.pushEvent({
            name: 'save',
            url: window.location.href,
            title: link
          });
        }

        this.collection.create({url: MyAppRouter.url, name: link});
      }
    }
  });

  MySavedCollection = new SavedCollection();
  MySavedCollectionView = new SavedCollectionView({collection:MySavedCollection});
  MySavedCollection.fetch();
  /***** End of the third module ****/

}(jQuery, Backbone, _, Handlebars, amplify));
