/*jslint browser:true,devel:true,white:true,nomen:true,bad_new:true*/
/*globals Backbone,_,undefined*/

(function (Backbone, _, undef) {
  "use strict";

  var App = window.App || {};

  // ----------
  // App.Router
  // ----------

  // Our Router handles everything related to url change and fires corresponding
  // events.
  // Call App.Router.navigate(url) to change url and fire backend courses fetch
  // Needs courseInputListView and courseCollection
  //
  App.Router = Backbone.Router.extend({
    page: 1,
    maxPage: 1,
    institution: null,
    days: null,
    courses: null,

    url: '',

    routes: {
      'c/:hash': 'urlChanged'
    },

    nextPage: function() {
      if (+this.page !== +this.maxPage) {
        this.page++;
        this.handleUrl();
      }
    },

    previousPage: function() {
      if (+this.page !== 1) {
        this.page--;
        this.handleUrl();
      }
    },

    updateProps: function(options) {
      if (typeof options !== "undefined") {
        this.page = options.page;
        this.institution = options.institution;
        this.days = options.days;
        this.courses = options.courses;
      }
    },

    handleUrl: function(options) {
      var hash,
          coursesHash = [];

      this.updateProps(options);

      _.each(this.courses, function(course){
        coursesHash.push(course.subject + '-' + course.number + '-' + course.crn);
      });

      hash = [
        this.institution.id, 
        this.page, 
        this.days.join('-'),
        coursesHash.join('_')
      ].join('_');

      this.navigate('c/' + hash, { trigger: true });
    },

    // Reads the url, calls courseInputListView to update inputs
    // and courseCollection to fetch new courses
    urlChanged: function(hash) {
      this.trigger('urlChanged', hash);
      this.url = hash;

      // Handle woopra and google analytics
      window.woopraTracker.trackPageview({
        url: '/c/' + hash,
        title: document.title
      });
      window._gaq.push(['_trackPageview', '/c/' + hash]);
    }
  });
  
  window.App = App;

}(Backbone, _));
