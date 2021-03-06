<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>

<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.1/dist/jquery.validate.min.js"></script>

<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>
<script>
$( function() {
$( "#dob" ).datepicker();
} );
</script>
<style>
.error{
	color: red
}

* {
  box-sizing: border-box;
}

body {
  background-color: #f1f1f1;
}

#regForm {
  background-color: #ffffff;
  margin: 100px auto;
  font-family: Raleway;
  padding: 40px;
  width: 70%;
  min-width: 300px;
}

h1 {
  text-align: center;  
}

input {
  padding: 10px;
  width: 100%;
  font-size: 17px;
  font-family: Raleway;
  border: 1px solid #aaaaaa;
}

/* Mark input boxes that gets an error on validation: */
input.invalid {
  background-color: #ffdddd;
}

/* Hide all steps by default: */
.tab {
  display: none;
}

button {
  background-color: #04AA6D;
  color: #ffffff;
  border: none;
  padding: 10px 20px;
  font-size: 17px;
  font-family: Raleway;
  cursor: pointer;
}

button:hover {
  opacity: 0.8;
}

#prevBtn {
  background-color: #bbbbbb;
}

/* Make circles that indicate the steps of the form: */
.step {
  height: 15px;
  width: 15px;
  margin: 0 2px;
  background-color: #bbbbbb;
  border: none;  
  border-radius: 50%;
  display: inline-block;
  opacity: 0.5;
}

.step.active {
  opacity: 1;
}

/* Mark the steps that are finished and valid: */
.step.finish {
  background-color: #04AA6D;
}
</style>
<body>
<?php //var_dump(Session::all()); exit;?>
<form id="regForm">
	@csrf
  <h1>Registration:</h1>
  <span class="error" id="error-msg"></span>
  <!-- One "tab" for each step in the form: -->
  <div class="tab">First Name:
    <p><input placeholder="First name..." oninput="this.className = ''" id="fname" name="fname" class="form-control" value="{{ $first_name }}"></p>
    Last Name:
	<p><input placeholder="Last name..." oninput="this.className = ''" id="lname" name="lname" class="form-control" value="{{ $last_name }}"></p>
	Email:
    <p><input placeholder="E-mail..." oninput="this.className = ''" id="email" name="email" class="form-control" value="{{ $email }}"></p>
	Date Of Birth:
	<p><input oninput="this.className = ''" name="dob" id="dob" class="form-control" value="{{ $dob }}"></p>
	State:
    <p><input placeholder="State..." oninput="this.className = ''" id="state" name="state" class="form-control" value="{{ $state }}"></p>
	City:
    <p><input placeholder="City..." oninput="this.className = ''" id="city" name="city" class="form-control" value="{{ $city }}"></p>	
  </div>
  <div class="tab">Login Info:
    <p><input placeholder="Username..." oninput="this.className = ''" id="uname" name="uname" class="form-control" value="{{ $user_name }}"></p>
    <p><input placeholder="Password..." oninput="this.className = ''" id="pass" name="pass" type="password" class="form-control"></p>
    <p><input placeholder="RePassword..." oninput="this.className = ''" id="confirm_pass" name="confirm_pass" type="password" class="form-control"></p>
  </div>
  <div style="overflow:auto;">
    <div style="float:right;">
      <button type="button" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
      <button type="button" id="nextBtn" onclick="nextPrev(1)">Next</button>
    </div>
  </div>
  <!-- Circles which indicates the steps of the form: -->
  <div style="text-align:center;margin-top:40px;">
    <span class="step"></span>
    <span class="step"></span>
  </div>
</form>

<script>
var currentTab = <?php echo (($active_tab == 'tab_2')? 1 : 0 )?>; // Current tab is set to be the first tab (0)

$(document).ready(function () {
	var active_tab = document.getElementsByClassName("tab");	
	jQuery.validator.addMethod('confirm_password', function (value, element) {
		var pass = document.getElementById("pass").value;
		if (pass != '' && value == pass) {
			return true;
		} else {
			return false;
		};
	});	
	jQuery.validator.addMethod('firstname', function (value, element) {
		if (/^[a-zA-Z0-9_-]+$/.test(value)) {
			return true;
		} else {
			return false;
		};
	});
	jQuery.validator.addMethod('email_rule', function (value, element) {
		if (/^([a-zA-Z0-9_\-\.]+)\+?([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/.test(value)) {
			return true;
		} else {
			return false;
		};
	});
	
	$('#regForm').validate({
		rules: {
			fname: {
				required: {
					depends: function(){
						if(currentTab == 0){
							return true;
						}
					}
				},
				firstname: true
			},
			lname: {
				required: {
					depends: function(){
						if(currentTab == 0){
							return true;
						}
					}
				},
				firstname: true
			},
			email: {
				required: {
					depends: function(){
						if(currentTab == 0){
							return true;
						}
					}
				},
				email_rule: true
			},
			dob: {
				required: {
					depends: function(){
						if(currentTab == 0){
							return true;
						}
					}
				}				
			},
			state: {
				required: {
					depends: function(){
						if(currentTab == 0){
							return true;
						}
					}
				}				
			},
			city: {
				required: {
					depends: function(){
						if(currentTab == 0){
							return true;
						}
					}
				}				
			},
			uname: {
				required: {
					depends: function(){
						if(currentTab == 1){
							return true;
						}
					}
				}				
			},
			pass: {
				required: {
					depends: function(){
						if(currentTab == 1){
							return true;
						}
					}
				}				
			},
			confirm_pass: {
				confirm_password: {
					depends: function(){
						if(currentTab == 1){
							return true;
						}
					}
				}				
			}
		},
		messages:{
			fname: {
				required: "First name field is required.",
				firstname: "Enter valid first name"
			},
			lname: {
				required: "Last name field is required.",
				lastname: "Enter valid last name"				
			},
			email: {				
				required: "Email field is required.",
				email_rule: "Enter valid Email address"
			},
			dob: {
				required: "Date of birth field is required."
			},
			state: {
				required: "State field is required."
			},
			city: {
				required: "City field is required."
			},
			uname: {
				required: "User name field is required."
			},
			pass: {
				required: "Password field is required."
			},
			confirm_pass: {
				confirm_password: "Enter correct password."
			}
		}
	});	
});

showTab(currentTab); // Display the current tab

function showTab(n) {
  // This function will display the specified tab of the form...
  var x = document.getElementsByClassName("tab");
  x[n].style.display = "block";
  //... and fix the Previous/Next buttons:
  if (n == 0) {
    document.getElementById("prevBtn").style.display = "none";
  } else {
    document.getElementById("prevBtn").style.display = "inline";
  }
  if (n == (x.length - 1)) {
    document.getElementById("nextBtn").innerHTML = "Submit";
  } else {
    document.getElementById("nextBtn").innerHTML = "Next";
  }
  //... and run a function that will display the correct step indicator:
  fixStepIndicator(n)
}

function nextPrev(n) {
	console.log('currentTab- '+currentTab);
  // This function will figure out which tab to display
  var x = document.getElementsByClassName("tab");
  // Exit the function if any field in the current tab is invalid:
  if (n == 1 && (!validateForm() == true)) return false;
  // Hide the current tab:
  x[currentTab].style.display = "none";
  // Increase or decrease the current tab by 1:
  currentTab = currentTab + n;
  // if you have reached the end of the form...

  if (currentTab >= x.length) {
    // ... the form gets submitted:
    // document.getElementById("regForm").submit();
	window.location.href="{{ url('/') }}";
    return false;
  }

  // Otherwise, display the correct tab:
  showTab(currentTab);
}

function validateForm() {
  // This function deals with validation of the form fields
  var x, y, i, valid = true;
  x = document.getElementsByClassName("tab");
  y = x[currentTab].getElementsByTagName("input");
  // A loop that checks every input field in the current tab:
  for (i = 0; i < y.length; i++) {
    // If a field is empty...
    if (y[i].value == "") {
		// console.log(y[i].value);
		// console.log(y[i]);
      // add an "invalid" class to the field:
      // y[i].className += " invalid";
      // and set the current valid status to false
      valid = false;
    }
		
  }
  // console.log($("#regForm").valid());
  // return false;
	//custom validation
	if($("#regForm").valid()){
		
		var FormVal = new Object();
		FormVal._token = "{{ csrf_token() }}";
		if(currentTab == 0){
			// Create variables from the form
			FormVal.first_name = $('input#fname').val(); 
			FormVal.last_name = $('input#lname').val(); 
			FormVal.email = $('input#email').val();  
			FormVal.dob = $('input#dob').val();
			FormVal.state = $('input#state').val();
			FormVal.city = $('input#city').val();
		}
		if(currentTab == 1){
			FormVal.user_name = $('input#uname').val(); 
			FormVal.pass = $('input#pass').val(); 
			FormVal.confirm_pass = $('input#confirm_pass').val(); 
		}
		$("#error-msg").html('');
		console.log('init currentTab = '+currentTab);
		// The AJAX
		$.ajax({  
			type: 'POST',
			url: "{{ url('/performvalidation')}}",
			data: FormVal,
			async: false,
			success: function(data) {
				console.log('currentTab = '+currentTab);
				// This is a callback that runs if the submission was a success.
				if(data.status == true) {
					valid = true;
					if( currentTab == 0){
						console.log('success: step 1');
						// console.log(': '+valid);
					} else {
						console.log('success: step 2');
						alert("Registration Successful");
					}
					//alert(data.message);
				} else {
					valid = false;
					$("#error-msg").html(data.message);
					$('html, body').animate({
						scrollTop: $("#error-msg").offset().top
					}, 500);					
					if( currentTab == 0){
						console.log('error: step 1');
					} else {
						console.log('error: step 2');
					}
					// alert(data.message);
					// var response = data.errors;
					// var errorString = '<ul>';
					// $.each( data.errors, function( key, value) {
						// errorString += '<li>' + value + '</li>';
					// });
					// errorString += '</ul>';					
					// alert(data.errorString);
				}
				// return false;
			},
			// error: function(data){
				// console.log(data.errors);
				// alert('Whoops! This didn\'t work. Please contact us.')
				// valid = false;
			// },
		});
		//valid = true;
	}
console.log('valid value '+valid);	
// return false;
// console.log('valida=' +valid);
	// if(currentTab == 0){
	// If the valid status is true, mark the step as finished and valid:
	  // if (valid) {
		// document.getElementsByClassName("step")[currentTab].className += " finish";
	  // }
	  // return valid; // return the valid status
	// }
	// if(currentTab == 0){
		// currentTab = 1;
		// showTab(currentTab); // Display the current tab		
	// }
return valid;
}

function fixStepIndicator(n) {
  // This function removes the "active" class of all steps...
  var i, x = document.getElementsByClassName("step");
  for (i = 0; i < x.length; i++) {
    x[i].className = x[i].className.replace(" active", "");
  }
  //... and adds the "active" class on the current step:
  x[n].className += " active";
}
</script>

</body>
</html>
