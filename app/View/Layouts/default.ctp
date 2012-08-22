<!DOCTYPE html>
<html>
<head>
	<meta name="description" content="SIScode is a free web application designed to help AUB students build their schedule without worrying about facing time conflicts during registration" />
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=no,maximum-scale=1" />
	<title>siscode</title>
	<?php echo $this->HtmlLogic->css('chosen'); ?>
	<?php echo $this->HtmlLogic->less('style', 'less-1.3.0.min');?>
	<!--[if lt IE 9]>
		<?php echo $this->HtmlLogic->script('html5shiv');?>
	<![endif]-->
</head>
<body data-cache_key="6" data-webroot="<?php echo $this->webroot; ?>">
	<?php echo $this->fetch('content'); ?>
	<?php //echo $this->HtmlLogic->script('http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');?>
	<script>window.jQuery || document.write('<script type="text/javascript" src="<?php echo $this->webroot;?>js/jquery-1.7.2.min.js">\x3C/script>');</script>
	
	<?php echo $this->HtmlLogic->script('amplify.min'); ?>
	
	<?php echo $this->HtmlLogic->script(array(
		'jquery.tooltip',
		'underscore-min',
		'chosen.jquery.min',
		'handlebars-1.0.0.beta.6',
		'backbone-min',
		'course-input-module'
	), array('combined' => true));?>
</body>
</html>