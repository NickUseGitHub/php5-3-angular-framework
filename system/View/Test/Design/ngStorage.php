<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drawme Tshirt | CMS</title>

    {% block css %}

		<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">

		{% if css %}
			{% for cs in css %}
				<link rel="stylesheet" type="text/css" href="{{ cs | site_url }}">
			{% endfor %}
		{% endif %}

	{% endblock %}

</head>
<body data-ng-app="app" data-ng-controller="MainController">

	<div class="container">
        {{ "{{test_model | json}}" }}
        <br/>

        <button data-ng-click="setValueIntoStorage()">set value into storage</button>
        <button data-ng-click="getValueFromStorage()">get value and sent into TestModel</button>

	</div>

	<script type="text/javascript">
    
		function site_url(url) {
			return "{{ "" | site_url}}" + url;
		}

	</script>
	{% block js %}

		{% if js %}
			{% for script_js in js %}
	            <script type="text/javascript" src="{{ script_js | site_url }}"></script>
	        {% endfor %}
    	{% endif %}

	{% endblock %}
</body>
</html>