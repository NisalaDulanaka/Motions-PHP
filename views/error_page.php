<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Page</title>
    <style>
        body{
            background-color: #001734;
            color: #fff;
        }

        .error-background{
            margin-top: 20px;
            margin-bottom: 20px;
            background-color: rgb(17, 77, 0);
            color: red;
            padding: 15px 40px;
        }
    </style>
</head>
<body>
    <h1>Status 500: internal server error</h1>
    <h3>Sorry there was an error in your code.</h3>
    <div class="error-background">
        <?php echo $errorMessage; ?>
    </div>
</body>
</html>