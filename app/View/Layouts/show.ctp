<!DOCTYPE html>
<!--[if lt IE 7]>			 <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>				 <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>				 <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
		<head>
				<?php echo $this->Html->charset(); ?>
				<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
				<title>
					<?php echo $title_for_layout; ?>
				</title>
				<meta name="description" content="">
				<meta name="viewport" content="width=device-width">

				<?php echo $this->Html->css('bootstrap.min'); ?>
				<style>
						body {
								padding-top: 60px;
								padding-bottom: 40px;
						}
				</style>
<?php
	echo $this->Html->css('bootstrap-responsive.min');
	echo $this->Html->css('main.css');

	echo $this->Html->script('vendor/modernizr-2.6.2-respond-1.1.0.min');

	echo $this->fetch('meta');
	echo $this->fetch('css');
	echo $this->fetch('script');
?>
		</head>
		<body>

				<!-- This code is taken from http://twitter.github.com/bootstrap/examples/hero.html -->

				<div class="container">

			<?php echo $this->Session->flash(); ?>

			<?php echo $this->fetch('content'); ?>
			</div>

						<hr>

						<footer>
								<p>&copy; Company 2012</p>
						</footer>

				</div> <!-- /container -->
				<?php echo $this->Html->script('//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min'); ?>
				<script>window.jQuery || document.write('<script src="/js/vendor/jquery-1.8.3.min.js"><\/script>')</script>

				<?php echo $this->Html->script('vendor/bootstrap.min'); ?>

				<?php echo $this->Html->script('vendor/plugins'); ?>
				<?php echo $this->Html->script('vendor/main'); ?>

				<script>
						var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
						(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
						g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
						s.parentNode.insertBefore(g,s)}(document,'script'));
				</script>
		</body>
</html>
