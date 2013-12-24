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
  // Needs courseInputCollection and courseCollection
  //
  App.Router = Backbone.Router.extend({
    courseInputCollection: null,
    courseCollection: null,

    url: '',

    routes: {
      'c/:hash': 'handleUrl'
    },

    // Reads the url, calls courseInputCollection to update inputs
    // and courseCollection to fetch new courses
    handleUrl: function(hash) {

      // Handle woopra and google analytics
      window.woopraTracker.trackPageview({
        url: '/c/' + hash,
        title: document.title
      });
      window._gaq.push(['_trackPageview', '/c/' + hash]);

      var data = [],
          models,
          array,
          days,
          page,
          joinedCourses = [],
          url;

      // url formatted as such:
      // {page}_{days separated by '-'}_{subject-number-crn}
      data = hash.split('_');
      page = data.splice(0, 1);
      page = page[0];
      days = data.splice(0, 1);
      days = days[0].split('-');
      _.each(data, function(item, key) {
        array = item.split('-');
        data[key] = {
          subject_id: array[0],
          number: array[1],
          crn: array[2]
        };
      });

      this.url = hash;

      // Fire events here
      this.courseInputCollection.update(data);
      this.courseCollection.fetchNew(data);
    }
  });

}(Backbone, _));
