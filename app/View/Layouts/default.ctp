<!DOCTYPE html>
<!--[if lt IE 9]><html lang="en-us" class="ie8"><![endif]-->
<!--[if gt IE 8]><!--><html lang="en-us"><!--<![endif]-->
<head>
  <meta name="description" content="SIScode is a free web application designed to help university students build their schedule without worrying about facing time conflicts during registration" />
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=no,maximum-scale=1" />
  <title>siscode</title>

  <?php echo $this->HtmlLogic->css('chosen'); ?>
  <?php echo $this->HtmlLogic->less('style', 'less-1.3.0.min');?>

  <!--[if lt IE 9]>
    <?php echo $this->HtmlLogic->script('html5shiv');?>
    <script src="https://raw.github.com/scottjehl/Respond/master/respond.min.js"></script>
  <![endif]-->

  <?php if (Configure::read('debug') == 0): ?>
    <script type="text/javascript">
      function woopraReady(tracker) {
        tracker.setDomain('siscode.me');
        tracker.setIdleTimeout(300000);
        tracker.track();
        window.woopraTracker = tracker;
        return false;
      }
      (function() {
        var wsc = document.createElement('script');
        wsc.src = document.location.protocol+'//static.woopra.com/js/woopra.js';
        wsc.type = 'text/javascript';
        wsc.async = true;
        var ssc = document.getElementsByTagName('script')[0];
        ssc.parentNode.insertBefore(wsc, ssc);
      })();

      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-23314622-1']);
      _gaq.push(['_trackPageview']);
      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
    </script>
  <?php else: ?>
    <!-- fake woopra and google -->
    <script type="text/javascript">
      var _gaq = [];
      var woopraTracker = {
        pushEvent: function() {},
        trackPageview: function() {}
      };
    </script>
  <?php endif; ?>

</head>
<body data-cache_key="<?php echo Configure::read('CacheKey'); ?>" >
  <?php echo $this->fetch('content'); ?>

  <!-- fb code -->
  <div id="fb-root"></div>
  <script>(function(d, s, id) {var js, fjs = d.getElementsByTagName(s)[0];if (d.getElementById(id)) return;js = d.createElement(s); js.id = id;js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";fjs.parentNode.insertBefore(js, fjs);}(document, 'script', 'facebook-jssdk'));</script>

  <!-- twitter code -->
  <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

  <?php // echo $this->HtmlLogic->script('http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'); ?>
  <script>//window.jQuery || document.write('<script type="text/javascript" src="<?php echo $this->webroot;?>js/vendor/jquery-1.10.2.min.js">\x3C/script>');</script>

  <?php echo $this->HtmlLogic->script(array(
    'vendor/jquery-1.10.2.min',
    'vendor/chosen.jquery.min',
    'vendor/handlebars-v1.3.0.min',
    'vendor/ember',
    'vendor/amplify.min',

    'appjs/application',
    'appjs/components',
    'appjs/inputs',
    'appjs/footer',
    'appjs/courses'
  ), array('combined' => true));?>
</body>
</html>
