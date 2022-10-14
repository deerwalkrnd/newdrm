<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DRM - @yield('title')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- css file -->
    <link rel="stylesheet" href="/assets/css/styles.css">

    <!-- fontawesome icon -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">

    <link href="/assets/images/login/favicon.gif" rel="shortcut icon">

    <!-- Select 2 assets -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Data-Table assets -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    
    <!-- sweet alert -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.min.css">
</head>

<body>
    <!-- navigation bar start -->
    @include('layouts.hr.navbar')
    <!-- navigation bar end -->

    <div class="container-fluid dashboard_container my-4">
        @yield('content')
    </div>

    <script src="/assets/js/main.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>

    <!-- sweet alert script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.all.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
    @yield('scripts')
    <script>
        function currentTime() {
            let date = new Date();
            let hh = date.getHours();
            let mm = date.getMinutes();
            let ss = date.getSeconds();
            let session = "AM";

            if (hh == 0) {
                hh = 12;
            }
            if (hh > 12) {
                hh = hh - 12;
                session = "PM";
            }

            hh = (hh < 10) ? "0" + hh : hh;
            mm = (mm < 10) ? "0" + mm : mm;
            ss = (ss < 10) ? "0" + ss : ss;

            let time = hh + ":" + mm + ":" + ss + " " + session;

            document.getElementById("clock").innerText = time;
            document.getElementById("clock1").innerText = time;
            let t = setTimeout(function() {
                currentTime()
            }, 1000);
        }

        currentTime();
    </script>

    <!-- check for res and display the response message -->
    <script>
        //check if res is received
        @if(Session::get('res'))
        Swal.fire(
            "{{ Session::get('res')['title'] }}",
            "{{ Session::get('res')['message'] }}",
            "{{ Session::get('res')['icon'] }}",
        );
        @endif

         $('#confirmForcePunchOut').click(function(event) {
                event.preventDefault();
                Swal.fire({
                    title: `Are you sure you want to make forced punch in today for everyone?`,
                    text: "If you force punch in now, punch out datetime will be set to now for those who have not punch out today.",
                    icon: "warning",
                    showCancelButton: true,
                   
                })
                .then((isConfirm) => {
                    if(isConfirm.value == true)
                        $.ajax({
                            url:"/force-punch-out",
                            type: 'GET',
                            dataType: 'JSON',
                            success: function(results){
                                if(results.success === true){
                                    swal.fire("Done!", results.message, "success");
                                    setTimeout(function(){
                                        location.reload();
                                    },2000);
                                } else {
                                    console.log("'here");
                                    swal.fire("Error!", results.message, "error");
                                }
                            }
                        })
                });
            });
    </script>
</body>

</html>