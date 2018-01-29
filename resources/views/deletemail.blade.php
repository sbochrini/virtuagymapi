<!DOCTYPE html>

<html>
  <haed>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  </head>
    <title></title>
    <body style="background-image:url(https://static.virtuagym.com/v2952934/images/background1.png);">
      <div class="container">
        <nav class="navbar navbar-dark bg-dark">
          <span class="navbar-brand mb-0 h1">Virtuagym</span>
        </nav>
        <div class="alert alert-danger mt-3" role="alert">
          <h4 class="alert-heading">Hello {{$user->firstname}} {{$user->lastname}}!</h4>
          <p>Your workout plan {{$plan->plan_name}} has been deleted!</p>
          <hr>
        </div>
    </div>
    </body>
</html>
