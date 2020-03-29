<!DOCTYPE html>
<html>
<head>
<style>
/* Clear floats after the columns */
.row:after {
  content: "";
  display: table;
  clear: both;
}
</style>
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
		/*	<!-- $('html,body').animate({scrollTop:$(this.hash)});--> */
		});
	});
</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- start-smoth-scrolling -->
</head>
<body style="background:#f6fef6">
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Product Categories</h4>
        </div>
        <div class="modal-body">
		<div class="row">
		<div class="list-group">
  <a href="#" class="list-group-item list-group-item-action">Beverages</a>
  <a href="#" class="list-group-item list-group-item-action">Bread/Bakery</a>
  <a href="#" class="list-group-item list-group-item-action">Frozen Foods</a>
  <a href="#" class="list-group-item list-group-item-action disabled">Cleaners</a>
  <a href="#" class="list-group-item list-group-item-action disabled">Canned/Jarred Goods</a>
  <a href="#" class="list-group-item list-group-item-action disabled">Dry/Baking Goods</a>
  <a href="#" class="list-group-item list-group-item-action disabled">Dairy</a>
  <a href="#" class="list-group-item list-group-item-action disabled">Personal Care</a>
  <a href="#" class="list-group-item list-group-item-action disabled">Others</a>
  <!--<a href="#" class="list-group-item">-->
  <!--<a href="#" class="list-group-item list-group-item-action disabled">-->
 <!-- <a href="#">-->
  </div>
</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>
</div>
<div class="modal fade" id="myModal2" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Product Categories</h4>
        </div>
        <div class="modal-body">
		<div class="row">
		<div class="list-group">
		<div class="mapouter"><div class="gmap_canvas"><iframe width="838" height="550" id="gmap_canvas" src="https://maps.google.com/maps?q=Unit%202%2C%2057%20Latham%20Street%2CChermside%20QLD-%204032%20&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe></div><style>.mapouter{position:relative;text-align:right;height:550px;width:838px;}.gmap_canvas {overflow:hidden;background:none!important;height:550px;width:838px;}</style></div>
		</div>
  </div>
</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>
</div>
<div class="agileits_header" style="background:#062a06;">
		<div class="ig_offers">
			<a href="<?php echo base_url(); ?>"><b>International Groceries</b></a>
		</div>
		<div class="ig_search">
			<form action="#" method="post">
				<input type="text" name="Product" value="Search a product...(coming soon)" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Search a product...';}" required="">
				<input type="submit" value=" ">
			</form>
		</div>
		<div class="product_list_header">  
			<form action="#" method="post" class="last">
                <fieldset>
                    <input type="hidden" name="cmd" value="_cart" />
                    <input type="hidden" name="display" value="1" />
                    <button style="background:#81b107;width: 50px;height: 40px;"  type="submit" name="submit" /><i class="fa fa-shopping-cart"></i></button>
                </fieldset>
            </form>
		</div>
		<div class="ig_header_right">
			<ul>
				<li class="dropdown profile_details_drop" >
					<a href="#" class="dropdown-toggle" style="color:#81b107;" data-toggle="dropdown"><i class="fa fa-user" aria-hidden="true"></i><span class="caret"></span></a>
					<div class="mega-dropdown-menu">
						<div class="igs_vegetables">
							<ul class="dropdown-menu drp-mnu">
								<li><a href="#">Login</a></li> 
								<li><a href="#">Sign Up</a></li>
							</ul>
						</div>                  
					</div>	
				</li>
			</ul>
		</div>
	<!--	<div class="" -->
		<div class="ig_header_right1">
			<h2><a href="#" data-toggle="modal" data-target="#myModal2" >Locate Us</a></h2>
		</div>
		<div class="clearfix"> </div>
	</div>
	<!--<div class="iggi" >
	</div>-->
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
<div class="logo_products" style="margin-bottom: -5px;">
<div class="container">
<div class="igs_logo_products_left" style="font-size:18px">
	<h1><a href="<?php echo base_url();?>"><img src="<?php echo base_url();?>assetsUser/images/iglogo1.png" style="width:85px;height:85px;"></a></h1>
</div>
<div class="igs_logo_products_left1">
	<ul class="special_items" style="margin:-7px;font-size:18px">
	<li><a href="<?php echo base_url();?>" style="padding-right: 3.6em;margin: 1em 0;">Home</a></li>
		<li><a href="<?php echo base_url();?>Shop/about" style="padding-right: 2.6em;margin: 1em 0;">About Us</a></li>
	<!--	<li class="dropdown profile_details_drop" style="padding-right: .6em;margin: 1em 0;" >
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">Products</span></a>
		<div class="mega-dropdown-menu">
			<div class="igs_vegetables">
				<ul class="dropdown-menu drp-mnu">
				<li><a href="#">Beverages </a></li>
				<li><a href="#">Bread/Bakery</a></li>
				<li><a href="#">Frozen Foods</a></li>
				<li><a href="#">Cleaners  </a></li>
				<li><a href="#">Pet Food</a></li>
				<li><a href="#">Canned/Jarred Goods</a></li>
				<li><a href="#">Dry/Baking Goods</a></li>
				<li><a href="#">Dairy </a></li>
				<li><a href="#">Personal Care</a></li>
				<li><a href="$">Others</a></li>
				</ul>
			</div>                  
		</div>	
	</li>-->
	<li><a href="#" style="margin-left:35px;" data-toggle="modal" data-target="#myModal">Products</a><i></i></li>
		<li><a href="#" style="padding-right: .6em;margin: 1em 0;">Contact Us</a><i></i></li>
	<!--<li><i class="fa fa-phone" aria-hidden="true"></i>1232 234 567</li>
		<li><i class="fa fa-envelope-o" aria-hidden="true"></i><a href="mailto:store@grocery.com">shop@iGroceries.com</a></li>	-->	
	</ul>
</div>	
<!--<div class="product_list_header">  
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
<!--<div class="ig_banner_nav_left">
		<nav class="navbar nav_bottom">
		<nav class="navbar1 nav_buttom1">
			Brand and toggle get grouped for better mobile display 
			<div class="navbar-header nav_2">
				<button type="button" class="navbar-toggle collapsed navbar-toggle1" data-toggle="collapse" data-target="#bs-megadropdown-tabs">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				</button>
			</div> 
			Collect the nav links, forms, and other content for toggling -->
			<!--<div class="collapse navbar-collapse" id="bs-megadropdown-tabs">
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
				</ul>-->
				</div><!-- /.navbar-collapse -->
			</nav>
		</div>