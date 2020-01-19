
<!DOCTYPE html>
<html>
<head>
<title>iGroceries</title>
<!-- for-mobile-apps -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);
function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- //for-mobile-apps -->
<link href="<?php echo base_url();?>assetsUser/css/bootstrap.css" rel='stylesheet' type='text/css' />
<!-- Custom Theme files -->
<link href="<?php echo base_url();?>assetsUser/css/style.css" rel='stylesheet' type='text/css' />
<!-- js -->
   <script src="<?php echo base_url();?>assetsUser/js/jquery-1.11.1.min.js"></script>
<!-- //js -->
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
<!-- start-smoth-scrolling -->
<link href="<?php echo base_url();?>assetsUser/css/font-awesome.css" rel="stylesheet"> 
<link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Noto+Sans:400,700' rel='stylesheet' type='text/css'>
<!--- start-rate---->
<script src="<?php echo base_url();?>assetsUser/js/jstarbox.js"></script>
	<link rel="stylesheet" href="<?php echo base_url();?>assetsUser/css/jstarbox.css" type="text/css" media="screen" charset="utf-8" />
		<script type="text/javascript">
			jQuery(function() {
			jQuery('.starbox').each(function() {
				var starbox = jQuery(this);
					starbox.starbox({
					average: starbox.attr('data-start-value'),
					changeable: starbox.hasClass('unchangeable') ? false : starbox.hasClass('clickonce') ? 'once' : true,
					ghosting: starbox.hasClass('ghosting'),
					autoUpdateAverage: starbox.hasClass('autoupdate'),
					buttons: starbox.hasClass('smooth') ? false : starbox.attr('data-button-count') || 5,
					stars: starbox.attr('data-star-count') || 5
					}).bind('starbox-value-changed', function(event, value) {
					if(starbox.hasClass('random')) {
					var val = Math.random();
					starbox.next().text(' '+val);
					return val;
					} 
				})
			});
		});
		</script>
<!---//End-rate---->

</head>
<body>
<!--<a href="<?php echo base_url();?>assetsUser/offer.html"><img src="<?php echo base_url();?>assetsUser/images/download.png" class="img-head" alt=""></a>-->
<div class="header">

<div class="container">
    
<div class="logo">
    <h1 ><a href="#"><img src="<?php echo base_url();?>assetsUser/images/iglogo.png" style="width:100px;height:100px;"></h1>
</div>
<div class="head-t">
    <ul class="card">

        <li><a href="#" ><i class="fa fa-user" aria-hidden="true"></i>Login</a></li>
        <li><a href="#" ><i class="fa fa-arrow-right" aria-hidden="true"></i>Register</a></li>
    </ul>	
</div>

<div class="header-ri">
    <ul class="social-top">
        <li><a href="#" class="icon facebook"><i class="fa fa-facebook" aria-hidden="true"></i><span></span></a></li>
        <li><a href="#" class="icon twitter"><i class="fa fa-twitter" aria-hidden="true"></i><span></span></a></li>
     
    </ul>	
</div>


    <div class="nav-top">
        <nav class="navbar navbar-default">
        
        <div class="navbar-header nav_2">
            <button type="button" class="navbar-toggle collapsed navbar-toggle1" data-toggle="collapse" data-target="#bs-megadropdown-tabs">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            

        </div> 
        <div class="collapse navbar-collapse" id="bs-megadropdown-tabs">
            <ul class="nav navbar-nav ">
                <li class=" active"><a href="#" class="hyper "><span>Home</span></a></li>	
                
                <li class="dropdown ">
                    <a href="<?php echo base_url();?>assetsUser/#" class="dropdown-toggle  hyper" data-toggle="dropdown" ><span> Beverages <b class="caret"></b></span></a>
                    <ul class="dropdown-menu multi">
                    <p>Items Coming soon</p>
                    </ul>
                </li>
                <li class="dropdown">
                
                    <a href="<?php echo base_url();?>assetsUser/#" class="dropdown-toggle hyper" data-toggle="dropdown" ><span> Bread & Bakery <b class="caret"></b></span></a>
                    <ul class="dropdown-menu multi multi1">
                    <p>Items Coming soon</p>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="<?php echo base_url();?>assetsUser/#" class="dropdown-toggle hyper" data-toggle="dropdown" ><span> Breakfast & Cereal <b class="caret"></b></span></a>
                    <ul class="dropdown-menu multi multi2">
                    <p>Items Coming soon</p>

                    </ul>
                </li>
                
                <li><a href="#" class="hyper"> <span>About</span></a></li>
                <li><a href="#" class="hyper"><span>Contact Us</span></a></li>
            </ul>
        </div>
        </nav>
            <div class="cart" >
        
            <span class="fa fa-shopping-cart my-cart-icon"><span class="badge badge-notify my-cart-badge"></span></span>
        </div>
        <div class="clearfix"></div>
    </div>
        
    </div>			
</div>
  <!---->
