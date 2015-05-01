<!DOCTYPE html>
<html>
<head>
	<script src="{$c.url}js/core.js"></script>
	<script src="{$c.url}js/proto/Element.js"></script>
	<script src="{$c.url}js/proto/String.js"></script>
	<script src="{$c.url}js/Entity.js"></script>
	<script src="{$c.url}js/entity/Address.js"></script>
	<script src="{$c.url}js/entity/User.js"></script>
	<script src="{$c.url}js/jquery-2.1.3.min.js"></script>
	<script src="{$c.url}js/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="{$c.url}css/forweb.css">
	<link rel="stylesheet" type="text/css" href="{$c.url}css/bootstrap.min.css">
</head>
<body class="container">
<div class="wrapper row">
	<header class="block col-lg-12 header">
		{$block.header}
	</header>
	<div class="block col-lg-3 col-md-4 col-sm-5 col-xs-6 sidebar">
		{$block.sidebar}
	</div>
	<div class="block col-lg-9 col-md-8 col-sm-7 col-xs-6 content">
		{$block.content}
	</div>
	<footer>
		<script>

			jQuery('[data-toggle="tooltip"]').tooltip().on('mouseleave', function(){
				this.show();
			});
		</script>
	</footer>

</div>
</body>
</html>