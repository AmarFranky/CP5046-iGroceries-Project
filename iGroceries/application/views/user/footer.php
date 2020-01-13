<!-- footer -->
<div class="footer" style="background:#062a06;">
		<div class="container">
			<div class="col-md-3 w3_footer_grid">
				<h3>Pages</h3>
				<ul class="w3_footer_grid_list">
					<li><a href="#">Events</a></li>
					<li><a href="#">About Us</a></li>
					<li><a href="#">Best Deals</a></li>
					<li><a href="#">Services</a></li>
					<li><a href="#">Short Codes</a></li>
				</ul>
			</div>
			<div class="col-md-3 w3_footer_grid">
				<h3>Account</h3>
				<ul class="w3_footer_grid_list">
					<li><a href="#">Register</a></li>
					<li><a href="#">Login</a></li>
					<li><a href="#">Wishlist</a></li>
				</ul>
			</div>
			<div class="col-md-3 w3_footer_grid">
				<h3>Categories</h3>
				<ul class="w3_footer_grid_list">
					<li><a href="#">Pet Food</a></li>
					<li><a href="#">Frozen Snacks</a></li>
					<li><a href="#">Kitchen</a></li>
					<li><a href="#">Branded Foods</a></li>
					<li><a href="#">Households</a></li>
				</ul>
			</div>
	
			<div class="col-md-3 w3_footer_grid">
				<h3>Contact</h3>
				<ul class="w3_footer_grid_list1">
				<li>Address: Unit 2, 57 Latham Street,Chermside QLD- 4032 </li>
				<li>Contact Number: +1232 234 567 </li>
					</ul>
			</div>
			<div class="clearfix"> </div>
			
			<div class="wthree_footer_copy">
				<p>© 2020 iGroceries All rights reserved</p>
			</div>
		</div>
	</div>
<!-- //footer -->

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
<!-- here stars scrolling icon -->
	<script type="text/javascript">
		$(document).ready(function() {
			/*
				var defaults = {
				containerID: 'toTop', // fading element id
				containerHoverID: 'toTopHover', // fading element hover id
				scrollSpeed: 1200,
				easingType: 'linear' 
				};
			*/
								
			$().UItoTop({ easingType: 'easeOutQuart' });
								
			});
	</script>
<!-- //here ends scrolling icon -->
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
				alert('The minimum order quantity is 3. Please add more to your shopping cart before checking out');
				evt.preventDefault();
			}
		});

	</script>
</body>
</html>