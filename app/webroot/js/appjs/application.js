(function ($, Ember, Em, undef) {
  window.App = Ember.Application.create();

  App.Router.reopen({
    rootURL: window.config.webroot,
    location: 'auto'
  });

  App.Router.map(function() {
    this.resource('calendar', { path: '/' }, function() {
      this.resource('schedule', { path: 'c/:hash' }, function() {
        this.resource('courses', { path: '/'});
      });
    });
  });


  App.CalendarController = Em.ObjectController.extend({
    page: 1,
    maxPage: 1
  });
}(jQuery, Ember, Ember));
