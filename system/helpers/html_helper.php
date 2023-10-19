<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if (!defined('VERSION')){
$heda = '<!doctype html> 

<html>

<head>

<meta charset="utf-8">

<style type="text/css">

body {
	font: 100%/1.4 Verdana, Arial, Helvetica, sans-serif;
	background-color: #555555;
	margin: 0;
	padding: 0;
	color: #000;
  padding: 0;
  -webkit-background-size: 100% 100%;   
  -moz-background-size: 100% 100%;   
  -o-background-size: 100% 100%;   
  background-size: 100% 100%;
  background-size: cover;
  background-repeat:no-repeat;
  background-attachment:fixed;
}


a:link {
	color:#414958;
	text-decoration: underline;
	font-weight: bold;
}
a:visited {
	color: #4E5869;
	text-decoration: underline;
}
a:hover, a:active, a:focus {
	text-decoration: none;
}

.container {
	width: 100%;
	min-width: 780px;
	margin: 0 auto; 
	
}

.box {
	width: 60%;
	background-color: white;
	border: solid 3px #469;
	border-radius: 7px;
	padding: 20px;
	margin: 0 auto;	
	margin-top: 40px;
	-webkit-box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
	   -moz-box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
	        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);	
	border-radius: 7px;
}


.message {

	width: 80%;

	background-color: transparent;

	border: solid 1px transparent;

	border-radius: 7px;

	padding: 10px;

	margin: 0 auto;	

	font-size: 18px;

	text-align: center;

	margin-bottom: 10px;

}



.success {

	background-color: #CFC;

	color: #030;	

	border-color: #093;

}



.error {

	background-color: #FCC;

	color: #900;	

	border-color: #C93;

}



.info {

	background-color: #E8F1F9;

	color: #003;	

	border-color: #69F;

}



.content {

	padding: 10px 0;

}



.submit, .buy {

	width: 200px;

	height: 45px;

	font-size: 20px;

	color: white;

	font-weight: bold;

	border: solid 1px #000;	

	border-radius: 5px;

}



.submit:hover, .buy:hover {

	background-color: black;

}



.submit {

	background-color: #063;	

}



.buy {

	background-color: #066;	

}



#email, #key {

	width: 80%;

	height: 40px;

	border: 1px solid #777;

	border-radius: 5px;

	margin: 0 auto;

	margin-top: 10px;

	display: block;	

	padding-left: 10px;

	font-size: 18px;

	text-transform: uppercase;

}



p.in {

	text-align: center;

	margin-top: 10px;

	margin-bottom: 20px;	

}

</style>

</head>

';



$incorectLicense = '

<body>

<div class="container">

  <div class="content">

	<div class="box">

        	<div class="message error">

            	The Product Key you entered is incorrect. Please check your key and try again

            </div>

            

            <form action="" method="POST">

  
            	<input name="key" type="text" id="key" required value="Your Product Key" onfocus="if(this.value  == \'Your Product Key\') { this.value = \'\'; } " onblur="if(this.value == \'\') { this.value = \'Your Product Key\'; } ">

              
             <p class="in"> <button type="submit" class="submit" name="submit_key">Validate Key</button> <a target="_blank" href="http://sendroidultimate.ynetinteractive.com"><button class="buy" type="button" name="buy_key">Buy</button></a></p>

            </form>

        </div>

  <!-- end .content --></div>

  <!-- end .container --></div>

</body>

</html>


';



$cls = '

<body>

<div class="container">

  <div class="content">

	<div class="box">

        	<div class="message success">

            	Congratulations! <br>Your Product Key has been validated. Click the button below to start using your new software

            </div>

            

             <p class="in">  <a href="index.php"><button class="submit" name="buy_key">Proceed</button></a></p>

        </div>

  <!-- end .content --></div>

  <!-- end .container --></div>

</body>

</html>

';



$tls = '

<body>

<div class="container">

  <div class="content">

	<div class="box">

        	<div class="message success">

            	Congratulations! <br>Your Trial key has been validated. You will be allowed to use this product for a trial period of <b>3 Days</b>, after which you will be required to purchase a Product Key.<br> Click the button below to start trying your new software

            </div>

            

             <p class="in">  <a href="index.php"><button class="submit" name="buy_key">Proceed</button></a></p>

        </div>

  <!-- end .content --></div>

  <!-- end .container --></div>

</body>

</html>

';



$uls = '

<body>

<div class="container">

  <div class="content">

	<div class="box">

        	<div class="message error">

            	The Product Key you entered has been used for too many installations. Please obtain a new key and try again or contact your vendor for assistance.

            </div>

            

            <form action="" method="POST">


            	<input name="key" type="text" id="key" required value="Your Product Key" onfocus="if(this.value  == \'Your Product Key\') { this.value = \'\'; } " onblur="if(this.value == \'\') { this.value = \'Your Product Key\'; } ">

              

             <p class="in"> <button type="submit" class="submit" name="submit_key">Validate Key</button> <a target="_blank" href="http://sendroidultimate.ynetinteractive.com"><button class="buy" type="button" name="buy_key">Puy</button></a></p>


            </form>

        </div>

  <!-- end .content --></div>

  <!-- end .container --></div>

</body>

</html>

';



$checkFailed = '

<body>

<div class="container">

  <div class="content">

	<div class="box">

        	<div class="message error">

            	Unable to validate your code. Please check your internet connection and try again.

            </div>

            

            <form action="" method="POST">

    

            	<input name="key" type="text" id="key" required value="Your Product Key" onfocus="if(this.value  == \'Your Product Key\') { this.value = \'\'; } " onblur="if(this.value == \'\') { this.value = \'Your Product Key\'; } ">

              

             <p class="in"> <button type="submit" class="submit" name="submit_key">Validate Key</button> <a target="_blank" href="http://sendroidultimate.ynetinteractive.com"><button type="button" class="buy" name="buy_key">Buy</button></a></p>

            </form>

        </div>

  <!-- end .content --></div>

  <!-- end .container --></div>

</body>

</html>

';



$vle = '

<body>

<div class="container">


  <div class="content">

	<div class="box">

        	<div class="message error">

            	We are sorry but you can no-longer make use of this system due to illegal modifications to the software, which is a major violation to your license terms. <br>Kindly contact us for further instructions on how to resolve this.

            </div>

            

            <form action="" method="POST">             

             <p class="in">  <a href="http://sendroidultimate.ynetinteractive.com"><button type="button" class="submit" name="buy_key">Contact Support</button></a></p>

            </form>

        </div>

  <!-- end .content --></div>

  <!-- end .container --></div>

</body>

</html>

';



$ivl = '

<body>


<div class="container">

  <div class="content">

	<div class="box">

        	<div class="message error">

            	Your License key is not valid on this server. Please enter a new Product Key to continue

            </div>

            

            <form action="" method="POST">

    

            	<input name="key" type="text" id="key" required value="Your Product Key" onfocus="if(this.value  == \'YProduct Key\') { this.value = \'\'; } " onblur="if(this.value == \'\') { this.value = \'Product Key Code\'; } ">

              

             <p class="in"> <button type="submit" class="submit" name="submit_key">Validate Key</button> <a target="_blank" href="http://sendroidultimate.ynetinteractive.com"><button class="buy" name="buy_key">Buy</button></a></p>

			 
            </form>

        </div>

  <!-- end .content --></div>

  <!-- end .container --></div>

</body>

</html>

';



$enterKey = '

<body>

<div class="container">

  <div class="content">

	<div class="box">

        	<div class="message info">

            	Please Enter Your Product Key <br>(Make sure you are connected to the internet before you proceed)

            </div>

            

            <form action="" method="POST">

    

            	<input name="key" type="text" id="key" required value="Your Product Key" onfocus="if(this.value  == \'Your Product Key\') { this.value = \'\'; } " onblur="if(this.value == \'\') { this.value = \'Your Product Key\'; } ">

              

             <p class="in"> <button type="submit" class="submit" name="submit_key">Validate Key</button> <a target="_blank" href="http://sendroidultimate.ynetinteractive.com"><button type="button" class="buy" name="buy_key">Buy</button></a></p>

            </form>

        </div>

  <!-- end .content --></div>

  <!-- end .container --></div>

</body>

</html>

';


$expiredKey = '

<body>

<div class="container">

  <div class="content">

	<div class="box">

        	<div class="message info">

            	Your trial period has ended. Please enter your Product Key to continue <br>(Make sure you are connected to the internet before your proceed)

            </div>

            

            <form action="" method="POST">

    

            	<input name="key" type="text" id="key" required value="Your Purchase Code" onfocus="if(this.value  == \'Your Product Key\') { this.value = \'\'; } " onblur="if(this.value == \'\') { this.value = \'Your Product Key\'; } ">

              

             <p class="in"> <button type="submit" class="submit" name="submit_key">Validate License</button> <a target="_blank" href="http://sendroidultimate.ynetinteractive.com"><button type="button" class="buy" name="buy_key">Obtain License</button></a></p>

            </form>

        </div>

  <!-- end .content --></div>

  <!-- end .container --></div>

</body>

</html>

';
global $heda;
global $ivl;	
}
?>