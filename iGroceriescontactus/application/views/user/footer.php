<!-- map -->
<!--<div class="map" id="map">
	<iframe src="https://maps.google.com/maps?q=Unit%202%2C%2057%20Latham%20Street%2CChermside%20QLD-%204032%20&t=&z=13&ie=UTF8&iwloc=&output=embed" style="border:0"></iframe>
</div>-->
<!-- //map -->
<!-- footer -->

<div class="footer" style="background:#062a06;">

		<div class="container">

		

			<div class="col-md-3 ig_footer_grid">
				<h3 style="margin-left:30px">Pages</h3>
				<ul class="ig_footer_grid_list">
					<li><a href="<?php echo base_url();?>">Home</a></li>
					<li><a href="#">About Us</a></li>
					<li><a href="#">Products</a></li> 
					<li><a href="#">Contact Us</a></li>
				
				</ul>
			</div>
			<div class="col-md-3 ig_footer_grid">
				<h3>Account</h3>
				<ul class="ig_footer_grid_list">
					<li><a href="#">Register</a></li>
					<li><a href="#">Login</a></li>
					<li><a href="#">Wishlist</a></li>
					
				</ul>
			</div>
			<div class="col-md-3 ig_footer_grid">
				<h3>Categories</h3>
				<ul class="ig_footer_grid_list">
					<li><a href="#">Pet Food</a></li>
					<li><a href="#">Frozen Snacks</a></li>
					<li><a href="#">Kitchen</a></li>
					<li><a href="#">Branded Foods</a></li>
					<li><a href="#">Households</a></li>
				</ul>
			</div>
			<div class="col-md-3 ig_footer_grid">
				<h3>Contact</h3>
				<ul class="ig_footer_grid_list1">
				<li>Address: Unit 2, 57 Latham Street,Chermside QLD- 4032 </li>
				<li>Contact Number: +1232 234 567 </li>
				</ul>
			</div>
			<div class="clearfix"> </div>
		
			<div class="col-md-3 w3_footer_grid agile_footer_grids_w3_footer" style="margin-left: 435px;">
					<div class="ig_footer_grid_bottom">
					
						<ul class="agileits_social_icons">
							<li><a href="#" class="facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
							<li><a href="#" class="twitter"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
							<li><a href="#" class="google"><i class="fa fa-google-plus" aria-hidden="true"></i></a></li>
							<li><a href="#" class="instagram"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
							<li><a href="#" class="dribbble"><i class="fa fa-dribbble" aria-hidden="true"></i></a></li>
						</ul>
					<!--	<ul class="agileits">	-->
					</div>
				</div>
<br>
				<div style="margin-top: 70px;

text-align: center;">
		<a href="#top" style="color:white;"><h3 style="color:#84c639;"><u>Back To Top<u><i style="margin-left: 10px;" class="fa fa-arrow-up"></i></h3></a>
		</div>
	
				<div class="wthree_footer_copy" style="margin-left: -80px;">
				<p style="margin-left:60px;">© 2020 iGroceries All rights reserved</p>
			</div>
		</div>
	</div>
<!-- //footer -->
<script>
$("a[href='#top']").click(function() {
  $("html, body").animate({ scrollTop: 0 }, "slow");
  return false;
});
</script>
<!-- Bootstrap Core JavaScript -->
<script src="<?php echo base_url();?>assetsUser/js/bootstrap.min.js"></script>
<script>
$(document).ready(function(){
    $(".dropdown").hover(            
        function() {
            $('.dropdown-menu', this).stop( true, true ).slideDown("fast");
            $(this).toggleClass('open');        
        },
        function() {
            $('.dropdown-menu', this).stop( true, true ).slideUp("fast");
            $(this).toggleClass('open');       
        }
    );
});
</script>
<script src="<?php echo base_url();?>assetsUser/js/minicart.js"></script>
<script>
		paypal.minicart.render();

		paypal.minicart.cart.on('checkout', function (evt) {
			var items = this.items(),
				len = items.length,
				total = 0,
				i;

			// Count the number of each item in the cart
			for (i = 0; i < len; i++) {
				total += items[i].get('quantity');
			}

			if (total < 3) {
				alert('');
				evt.preventDefault();
			}
		});

	</script>
</body>
</html>