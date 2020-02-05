<!DOCTYPE html>
<html>
<head>
<title>iGroceries</title>
<!-- for-mobile-apps -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);
		function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- //for-mobile-apps -->
<link href="<?php echo base_url();?>assetsUser/css/bootstrap.css" rel="stylesheet" type="text/css" media="all" />
<link href="<?php echo base_url();?>assetsUser/css/style.css" rel="stylesheet" type="text/css" media="all" />
<!-- font-awesome icons -->
<link href="<?php echo base_url();?>assetsUser/css/font-awesome.css" rel="stylesheet" type="text/css" media="all" /> 
<!-- //font-awesome icons -->
<!-- js -->
<script src="<?php echo base_url();?>assetsUser/js/jquery-1.11.1.min.js"></script>
<!-- //js -->
<link href='//fonts.googleapis.com/css?family=Ubuntu:400,300,300italic,400italic,500,500italic,700,700italic' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>
<!-- start-smoth-scrolling -->
<script type="text/javascript" src="<?php echo base_url();?>assetsUser/js/move-top.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assetsUser/js/easing.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$(".scroll").click(function(event){		
			event.preventDefault();
			$('html,body').animate({scrollTop:$(this.hash).offset().top},1000);
		});
	});
</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- start-smoth-scrolling -->
</head>
	
<body style="margin-top:-59px;background:#f6fef6">
<!-- header -->
	
<!-- script-for sticky-nav -->
	<script>
	$(document).ready(function() {
		 var navoffeset=$(".agileits_header").offset().top;
		 $(window).scroll(function(){
			var scrollpos=$(window).scrollTop(); 
			if(scrollpos >=navoffeset){
				$(".agileits_header").addClass("fixed");
			}else{
				$(".agileits_header").removeClass("fixed");
			}
		 });
		 
	});
	</script>
<!-- //script-for sticky-nav -->
	<div class="logo_products" style="margin-bottom:0px;">
		<div class="container">
			<div class="w3ls_logo_products_left">
				<h1><a href="<?php echo base_url();?>"><img src="<?php echo base_url();?>assetsUser/images/iglogo1.png" style="width:100px;height:100px;"></a></h1>
			</div>
			<div class="w3ls_logo_products_left1">
				<ul class="special_items">
					<li><a href="#">Products</a><i></i></li>
					<li><a href="#">About Us</a><i></i></li>
					<li><a href="#">Contact Us</a><i></i></li>
					<li><a href="#">Sign Up</a></li>
                   
				</ul>
			</div>
			<div class="w3ls_logo_products_left1">
				<ul class="phone_email">
					<li><i class="fa fa-phone" aria-hidden="true"></i>1232 234 567</li>
					<li><i class="fa fa-envelope-o" aria-hidden="true"></i><a href="<?php echo base_url();?>assetsUser/mailto:store@grocery.com">shop@iGroceries.com</a></li>
                  <a href="#">  <i class="fa fa-shopping-cart"></i></a>
                </ul>
			</div>
         <!--   <div class="product_list_header ">  
			<form action="#" method="post" class="last">
                <fieldset>
                    <input type="hidden" name="cmd" value="_cart" />
                    <input type="hidden" name="display" value="1" />
                    <input type="submit" name="submit" value="View your cart" class="fa fa-shopping-cart" />
                </fieldset>
            </form>
		</div>-->
			<div class="clearfix"> </div>
		</div>
	</div>
<!-- //header -->
<!-- banner -->
<div class="banner">
		<div class="w3l_banner_nav_left">
			<nav class="navbar nav_bottom">
			 <!-- Brand and toggle get grouped for better mobile display -->
			  <div class="navbar-header nav_2">
				  <button type="button" class="navbar-toggle collapsed navbar-toggle1" data-toggle="collapse" data-target="#bs-megadropdown-tabs">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				  </button>
			   </div> 
			   <!-- Collect the nav links, forms, and other content for toggling -->
				<div class="collapse navbar-collapse" id="bs-megadropdown-tabs">
					<ul class="nav navbar-nav nav_1">
                    <li><a href="#" style="background:#95d100;">Product Categories</a></li>
						<li><a href="#">Beverages </a></li>
						<li><a href="#">Bread/Bakery</a></li>
						<li><a href="#">Frozen Foods</a></li>
						<li><a href="#">Cleaners  </a></li>
						<li><a href="#">Pet Food</a></li>
                        <li><a href="#">Canned/Jarred Goods</a></li>
						<li><a href="#">Frozen Foods</a></li>
                        <li><a href="#">Dry/Baking Goods</a></li>
                        <li><a href="#">Dairy </a></li>
                        <li><a href="#">Personal Care</a></li>
                        <li><a href="$">Others</a></li>
					</ul>
				 </div><!-- /.navbar-collapse -->
			</nav>
		</div>