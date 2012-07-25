<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>SIScode</title>
	<link rel="stylesheet/less" type="text/css" href="<?php echo $this->request->webroot . 'css/style.less' ?>">
	<?php echo $this->HtmlLogic->css('chosen'); ?>
	<?php echo $this->HtmlLogic->script('less-1.3.0.min'); ?>
	<!--[if lt IE 9]>
		<?php echo $this->HtmlLogic->script('html5shiv');?>
	<![endif]-->
</head>
<body data-cache_key="6">
	<?php echo $this->fetch('content'); ?>
	<?php echo $this->HtmlLogic->script('jquery-1.7.2.min'); ?>
	<?php echo $this->HtmlLogic->script('jquery.tooltip'); ?>
	<?php echo $this->HtmlLogic->script('underscore-min'); ?>
	<?php echo $this->HtmlLogic->script('chosen.jquery.min'); ?>
	<?php echo $this->HtmlLogic->script('handlebars-1.0.0.beta.6'); ?>
	<?php echo $this->HtmlLogic->script('backbone-min'); ?>
	<?php echo $this->HtmlLogic->script('course-input-module'); ?>
</body>
</html>