<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
        <!-- FontAwesome link -->
        <script src="https://use.fontawesome.com/releases/v5.0.8/js/all.js"></script>

        <!-- custom css -->
        <link href="/assets/css/style.css" rel="stylesheet" type="text/css">    
        <link href="/assets/images/login/favicon.gif" rel="shortcut icon">
        <title>DRM</title>
        
        <!-- Select 2 assets -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    </head>

    <body>
        <div class="container-fluid no-x-padding">
            <div class="row no-gutters">
                <div class="col-2 bg-dark pg-1 sidebar">@include('layouts.admin.section-a')</div>
                <div class="col-lg-10 bg-custom">@include('layouts.admin.section-b')</div>
            </div>
        </div>

        <!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script> -->
        <!-- <script src="https://code.jquery.com/jquery-3.6.0.js"></script> -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
    

        <!-- select 2 -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

         <!-- sweet alert -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.min.css">
        
        <script>
            $('.manager-livesearch').select2({
                
                ajax: {
                    url: '/employee/search',
                    data: function (params) {
                        var query = {
                            q: params.term,
                        }
                         // Query parameters will be ?search=[term]
                        return query;
                    },
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                let full_name = (item.middle_name === null) ? item.first_name + " " + item.last_name : item.first_name + " " + item.middle_name + " " + item.last_name;
                                return {
                                    text: full_name,
                                    id: item.id
                                }
                            })
                        };
                    },
                    cache: true
                }
            });


            $('.district-livesearch').select2({
                ajax: {
                    url: '/district/search',
                    data: function (params) {
                        var query = {
                            q: params.term,
                            p: $('#permanent_address').val()  
                        }
                         // Query parameters will be ?search=[term]
                        return query;
                    },
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.district_name ,
                                    id: item.id
                                }
                            })
                        };
                        
                        // console.log(query);
                    },
                    cache: false
                }
            });
        </script>

        <!-- ckeditor -->
        <script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $('.ckeditor').ckeditor;
            });
        </script>
    </body>
</html>