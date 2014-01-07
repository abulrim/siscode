/*jslint browser:true,devel:true,white:true,nomen:true,bad_new:true*/
/*globals jQuery*/ 

(function($){
	"use strict";
	
  var methods = {
		init: function(options) {
			options = $.extend({
				tooltip: '.tooltip'
			}, options);
			
			var $self,
				height,
				left;
			
			$(this).each(function() {
				var timeout, $tooltipContent;
				$tooltipContent = $(this).find(options.tooltip);
				height = $tooltipContent.height();
				left = ($(this).width() - 16 - $tooltipContent.width())/2;
				$tooltipContent.css({
					top: (-height - 20) + 'px',
					left: left + 'px'
				}).hide();
				
				timeout = null;
				$(this).on('mouseenter', function() {
					clearTimeout(timeout);
					$tooltipContent.fadeIn();
				})
				.on('mouseleave', function() {
					timeout = setTimeout(function() {
						$tooltipContent.fadeOut();
					}, 300);
					
				});
			});
			return this;
		}
	};

  $.fn.tooltip = function(method) {
    
    if (methods[method]) {
      return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
    } else if (typeof method === 'object' || !method) {
      return methods.init.apply(this, arguments);
    } else {
      $.error('Method ' +  method + ' does not exist on jQuery.tooltip');
    }
  };

})(jQuery);