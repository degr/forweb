<!DOCTYPE html>
<html>
<head>
	<script src="{$c.url}js/core.js"></script>
	<script src="{$c.url}js/proto/Element.js"></script>
	<script src="{$c.url}js/proto/String.js"></script>
	<script src="{$c.url}js/Entity.js"></script>
	<script src="{$c.url}js/entity/Address.js"></script>
	<script src="{$c.url}js/entity/User.js"></script>
	<link rel="stylesheet" type="text/css" href="{$c.url}css/core.css">
</head>
<body class="container">
<div class="wrapper row">
	<div class="block col-lg-12 header">
		{$block.header}
	</div>
	<div class="block col-lg-9 col-md-8 col-sm-7 col-xs-6 content">
		{$block.content}
	</div>
	<div class="block col-lg-3 col-md-4 col-sm-5 col-xs-6 sidebar">
		{$block.sidebar}
	</div>
</div>
</body>
</html>