<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
        <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

        <!-- Links -->
        <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">

        <style>
            .title h1 {
                text-align: center;
                padding: 34px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            
            <div class="content">
                
                <div class="title">
                    <h1>{{ config('app.name') }}</h1>
                </div>

                <div id="app">
                    <ticket-generator></ticket-generator>
                </div>
            </div>
        </div>

        <script src="{{ mix('/js/app.js') }}"></script>
    </body>
</html>
