<!DOCTYPE html>
<html>
<head>
	<title>DrawMe - CMS</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

    {% block css %}

        {% if css %}
            {% for cs in css %}
                <link rel='stylesheet' href='{{ cs | site_url }}' type='text/css'>
            {% endfor %}
        {% endif %}

    {% endblock %}

    <style type="text/css">
        
        input.ng-dirty.ng-invalid{border: 1px solid red;}

    </style>

</head>
<body data-ng-app="app" class="body-Login-back">

    <div class="container">
       
        <div class="row">
            <div class="col-md-4 col-md-offset-4 text-center logo-margin ">
            	<h1>Drawme CMS</h1>
            </div>
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">                  
                    <div class="panel-heading">
                        <h3 class="panel-title">Please Sign In</h3>
                    </div>
                    <div data-ng-controller="Login_Controller" class="panel-body">
                        <form name='formField' novalidate role="form">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="username" data-ng-model="username" focus="true" type="text" required />
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="password" data-ng-model="password" type="password" value="" user-password required />
                                </div>
                                <!-- <div class="checkbox">
                                    <label>
                                        <input name="remember" type="checkbox" value="Remember Me">Remember Me
                                    </label>
                                </div> -->
                                <!-- Change this to a button or input when using this as a form -->
                                <br/>
                                    <span style="color:red">{{message}}</span>
                                <br/>
                                <a href="#" data-ng-click="login()" class="btn btn-lg btn-success btn-block">Login</a>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        
        function site_url(url) {
            return "{{ '' | site_url }}" + url;
        }

    </script>

    {% if js %}
        {% for js_file in js %}
            <script type='text/javascript' src='{{ js_file | site_url }}'></script>
        {% endfor %}
    {% endif %}

</body>
</html>