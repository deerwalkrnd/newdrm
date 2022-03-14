<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">

    <title>DRM - Login</title>
    <link href="/assets/images/login/favicon.gif" rel="shortcut icon">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/js/all.min.js" integrity="sha512-YSdqvJoZr83hj76AIVdOcvLWYMWzy6sJyIMic2aQz5kh2bPTd9dzY3NtdeEAzPp/PhgZqr4aJObB3ym/vsItMg==" crossorigin="anonymous"></script>
    <style type="text/css">
    	*{
    		margin: 0;
    		padding: 0;
    	}

    	.bg-deerwalk{
    		background: #0a3e6a;
    	}

    	footer{
    		background: url({{ asset('assets/images/login/footer.jpeg') }}) repeat-x scroll left top transparent;
    		line-height: 0.9;
			
    	}

    	.login-header{
    		background: url({{ asset('assets/images/login/loginheader.png') }}) repeat-x scroll left top transparent;
    		color: #ffffff;
    		font-weight: bold;
    		font-size: 18px;
			border-radius: 2px;
    	}
		
    	input{
    		width: 100%;

    	}

    	.title{
    		color: #666666;
    		font-weight: 700;
    	}

    	.box-footer{
    		background-color: #F6f5f9;
			
    	}

    	.forgot-password{
    		font-size: 14px;
    	}

    	.btn-custom{
    		line-height: 1;
    		width: 90%;
    		font-weight: 600;
    	}
		.btn-custom:hover{
    	color: white;
    	background: linear-gradient(to bottom, #176317, #53ad26 );
		}
    	.border-1{
    		border-width: 1px !important;
    	}

    	.custom-input{
    		 border-top-right-radius: 0px;
    		 border-bottom-right-radius: 0px;
			 border-radius: px;
    	}
		.form-icons{
			border-radius: 5px;
			border-top-left-radius: 0px;
    		border-bottom-left-radius: 0px;
		}
		.mainfooter{
			position:absolute;
   			bottom:0;
   			width:100%;
   			height:65px
		}
		
    </style>
  </head>
  <body>
	<!-- Image and text -->
	<nav class="navbar navbar-light bg-deerwalk">
	  <a class="navbar-brand" href="#">
	    <img src="{{ asset('assets/images/login/logo.png') }}" width="200" height="40" class="d-inline-block align-top" alt="">
	  </a>
	</nav>

	<div class="container mt-3">
		<div class="row justify-content-center">
		
			<div class="col-md-8 col-xl-5 col-lg-6 col-sm-10 col-10 border border-dark login">
				<div class="row login-header">
					<div class="col-md-12 my-2">
						<span>Reset Password<span>
					</div>
				</div>
				<!-- row 1 -->

				<div class="row box-content m-1 p-3">
					<div class="col-md-12 col-lg-12">
					@if(session('icon') != null)
						<p class="{{ 'text-'.session('icon') }}">{{ session('message') }}</p>
					@endif
					<form action="{{ route('password.request') }}" method="POST">
					{{ csrf_field() }}
					@if($errors->any())
						<div class="alert alert-danger">
							<p class="text-danger">{{ $errors->first('email') }}</p>
						</div>
					@endif
                    <p class="text-success"></p>
						<div class="row">
							<div class="col-md-3 col-lg-3 col-sm-4 col-6 mt-1 text-right">
								<span class="title">Email: </span>
							</div>	
							<div class="col-md-6 col-lg-6 col-sm-6 col-5 p-0">
								<input type="email" name="email" class="border-right-0 border-1 form-control border-dark custom-input" autocomplete="on">
							</div>
							<div class="col-md-1 col-sm-1 col-1 text-left border p-0 border-dark text-center border-left-0 form-icons">
								<i class="fas fa-user-tie align-bottom"></i>
							</div>
						</div>
						<!-- email -->
					</div>	
				</div>
				<!-- row 2 -->

				<div class="row box-footer py-3">
					<div class="col-md-12 col-lg-12 col-sm-12">
						<div class="row justify-content-end">
							<div class="col-md-4 col-sm-5 col-5 text-right login">
								<button type="submit" class="btn btn-primary btn-custom">Reset</button>
							</div>	
						</div>
					</div>
				</div>
	</form>
				<!-- row 3 -->
			</div>

		</div>
	</div>

	<footer class="fixed-bottom mainfooter">
      <div class="container text-center">
      	<div class="footer-text text-muted mt-3">
      		<p>© Copyright 2022 Deerwalk Inc. | All Rights Reserved.<br>Deerwalk Resources Manager Ver. 2.0.1</p>
      	</div>
      </div>
    </footer>

    <!-- Option 1: jQuery and Bootstrap Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF" crossorigin="anonymous"></script>

  </body>
</html>