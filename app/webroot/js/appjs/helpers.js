/*jslint browser:true,devel:true,white:true,nomen:true,bad_new:true*/
/*globals Handlebars, undefined*/

(function (H) {
  "use strict";

  //Handlebars 'equal' helper, enables us to select an option
  H.registerHelper('equal', function(lvalue, rvalue, options) {
    if (arguments.length < 3) {
      throw new Error("Handlebars Helper equal needs 2 parameters");
    }
    if(+lvalue !== +rvalue ) {
      return options.inverse(this);
    } else {
      return options.fn(this);
    }
  });

}(Handlebars));
