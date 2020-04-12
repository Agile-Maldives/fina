<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Customer Frontend</title>
	<!-- Mobile Specific Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<!-- Font-->
	<link rel="stylesheet" type="text/css" href="{{asset('css/opensans-font.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('fonts/line-awesome/css/line-awesome.min.css')}}">
	<!-- Jquery -->
	<link rel="stylesheet" href="https://jqueryvalidation.org/files/demo/site-demos.css">
	<!-- Main Style Css -->
    <link rel="stylesheet" href="{{asset('css/style.css')}}"/>
</head>
<body class="form-v4">
	<div class="page-content">
		<h1 class="page-title">CUSTOMER</h1>
	</div>
	<div class="page-content">
		<div class="form-v4-content">
			<div class="form-left">
				<h2>TOTALS</h2>
				<p class="text-2"><span>Total Issued: &nbsp;</span> {{$totalIssued}}</p>
				<p class="text-2"><span>Total Due: &nbsp;</span> {{$totalDue}}</p>
				<hr/>
				<h2>INFORMATION</h2>
				<p class="text-2"><span>Name: &nbsp;</span> {{$account->account_name}}</p>
				<p class="text-2"><span>Account ID Card: &nbsp;</span> {{$account->account_id_card}}</p>
			</div>
			<form class="form-detail" action="#" method="post" id="myform">
				<h2><u>BORROWINGS</u></h2>
				<hr/>
				@foreach ($loans as $loan)
				    <p>Borrowing ID:{{$loan->id}} </p>
				    <dl>
				    	<p class="text-2"><b>Loan Amount: &nbsp;</b>{{$loan->loan_amount}} {{$loan->currency}}</p>
				    	<p class="text-2"><b>Balance Due: &nbsp;</b>{{$loan->balance_due}} {{$loan->currency}}</p>
				    	<p class="text-2"><b>Date Start: &nbsp;</b>{{$loan->date_loan_start}}</p>
				    	<p class="text-2"><b>Agreement: &nbsp;</b><a href="http://api.fina.agilemaldives.com/api/{{$loan->agreement_attachment}}">Download</a></p>
				    </dl>
				    <hr/>
				@endforeach

			</form>
		</div>
	</div>
	<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
	<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
	<script>
		// just for the demos, avoids form submit
		jQuery.validator.setDefaults({
		  	debug: true,
		  	success:  function(label){
        		label.attr('id', 'valid');
   		 	},
		});
		$( "#myform" ).validate({
		  	rules: {
			    password: "required",
		    	comfirm_password: {
		      		equalTo: "#password"
		    	}
		  	},
		  	messages: {
		  		first_name: {
		  			required: "Please enter a firstname"
		  		},
		  		last_name: {
		  			required: "Please enter a lastname"
		  		},
		  		your_email: {
		  			required: "Please provide an email"
		  		},
		  		password: {
	  				required: "Please enter a password"
		  		},
		  		comfirm_password: {
		  			required: "Please enter a password",
		      		equalTo: "Wrong Password"
		    	}
		  	}
		});
	</script>
</body><!-- This templates was made by Colorlib (https://colorlib.com) -->
</html>
