@if (Sentinel::check() && Sentinel::getUser()->hasAccess(['admin.access']))
	<link rel="stylesheet" href="/packages/vis/builder/fontawesome-pro-5.12.0-web/css/all.min.css">
	<script src="/packages/vis/builder/js/froala.js"></script>
	<link rel="stylesheet" href="/packages/vis/builder/css/froala.css">
	<script src="/packages/vis/builder/js/quick_edit.js"></script>
@endif
