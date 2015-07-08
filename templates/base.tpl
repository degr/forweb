<!DOCTYPE html>
<html>
<head>
	<script src="{$c.url}js/core.js"></script>
	<script>
		Core.url = '{$c.url}';
	</script>
	<script src="{$c.url}js/proto/Element.js"></script>
	<script src="{$c.url}js/proto/String.js"></script>

	<script src="{$c.url}js/Entity.js"></script>
	<script src="{$c.url}js/Forms.js"></script>

	<script src="{$c.url}js/Autocode.js"></script>
	<!--script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script-->

		{$block.scriptCollector}
	<link rel="stylesheet" type="text/css" href="{$c.url}css/compilled.css">
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
	</footer>

</div>
</body>
</html>