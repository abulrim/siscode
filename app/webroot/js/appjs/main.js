/*jslint browser:true,devel:true,white:true,nomen:true,bad_new:true*/
/*globals jQuery,Backbone,Handlebars,_, undefined, FB*/

(function ($, Backbone, _, H, amplify, undef) {
  "use strict";

  var App = window.App,
      webroot = $('body').data('webroot'),
      institutions = $('#institutions-data').data('institutions');

  // init router
  var router = new App.Router();

  // init app
  var appView = new App.AppView({ router: router });

  // init course input module
  var courseInputListView = new App.CourseInputListView({
    router: router,
    institutions: institutions
  });

  // init course input module
  var courseScheduleView = new App.CourseScheduleView({
    webroot: webroot,
    router: router
  });

  // init save module
  var savedCollectionView = new App.SavedCollectionView({
    courseCollection: courseScheduleView.collection,
    webroot: webroot,
    router: router
  });

  Backbone.history.start({pushState: true, root: webroot});

}(jQuery, Backbone, _, Handlebars, amplify));
