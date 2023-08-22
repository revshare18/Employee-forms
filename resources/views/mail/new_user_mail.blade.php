<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email new user</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>
<body>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Login Details</h5>
            <p class="card-text">Name :  {{$data['name']}}</p>
            <p class="card-text">Password : {{$data['password']}}</p>
        </div>
    </div>
</body>
</html>