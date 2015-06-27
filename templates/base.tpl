<!DOCTYPE html>
<html>
<head>
	<script src="{$c.url}js/core.js"></script>
	<script src="{$c.url}js/proto/Element.js"></script>
	<script src="{$c.url}js/proto/String.js"></script>
	<script src="{$c.url}js/Entity.js"></script>
	<script src="{$c.url}js/entity/Address.js"></script>
	<script src="{$c.url}js/entity/User.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
	{$block.scriptCollector}
	<script>
		Core.url = '{$c.url}';
		Ajax.request({
			url: Core.url + "person/checkForActivePerson?ajax=1",
			success: Person.onActivatePerson,
			type: 'post',
			response: 'json'
		});

	</script>
	<link rel="stylesheet" type="text/css" href="{$c.url}css/core.css">
	<link rel="stylesheet" type="text/css" href="{$c.url}css/project.css">
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