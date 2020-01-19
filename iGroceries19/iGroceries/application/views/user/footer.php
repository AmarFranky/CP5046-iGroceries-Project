
<!--footer-->
<div class="footer" style="margin-bottom:-95px;">
	<div class="container">
		
		<div class="clearfix"></div>
			<div class="footer-bottom">
				<marquee direction="right"><h2 ><a href="#"><img src="<?php echo base_url();?>assetsUser/images/iglogo.png" style="width:100px;height:100px;"></a></h2></marquee>
				<p class="fo-para">The iGroceries’ main aim is to import and sell a variety of international groceries to a diverse population of the country who migrated from different parts of the world and lived here in Australia.</p>
				<ul class="social-fo">
					<li><a href="#" class=" face"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
					<li><a href="#" class=" twi"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
			
				</ul>
				<div class=" address">
					<div class="col-md-4 fo-grid1">
							<p><i class="fa fa-home" aria-hidden="true"></i>Unit 2, 57 Latham Street,Chermside QLD- 4032.</p>
					</div>
					<div class="col-md-4 fo-grid1">
							<p><i class="fa fa-phone" aria-hidden="true"></i>+123 123 123 , +1111 222 333</p>	
					</div>
					<div class="col-md-4 fo-grid1">
						<p><a href="#"><i class="fa fa-envelope-o" aria-hidden="true"></i>info@igroceries.com</a></p>
					</div>
					<div class="clearfix"></div>
					
					</div>
			</div>
		
	</div>
</div>
<!-- //footer-->

<!-- smooth scrolling -->
	<!--<script type="text/javascript">
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
	<a href="#" id="toTop" style="display: block;"> <span id="toTopHover" style="opacity: 1;"> </span></a>-->
<!-- //smooth scrolling -->
<!-- for bootstrap working -->
		<script src="<?php echo base_url();?>assetsUser/js/bootstrap.js"></script>
<!-- //for bootstrap working -->
<script type='text/javascript' src="<?php echo base_url();?>assetsUser/js/jquery.mycart.js"></script>
  <script type="text/javascript">
  $(function () {

    var goToCartIcon = function($addTocartBtn){
      var $cartIcon = $(".my-cart-icon");
      var $image = $('<img width="30px" height="30px" src="<?php echo base_url();?>assetsUser/' + $addTocartBtn.data("image") + '"/>').css({"position": "fixed", "z-index": "999"});
      $addTocartBtn.prepend($image);
      var position = $cartIcon.position();
      $image.animate({
        top: position.top,
        left: position.left
      }, 500 , "linear", function() {
        $image.remove();
      });
    }

    $('.my-cart-btn').myCart({
      classCartIcon: 'my-cart-icon',
      classCartBadge: 'my-cart-badge',
      affixCartIcon: true,
      checkoutCart: function(products) {
        $.each(products, function(){
          console.log(this);
        });
      },
      clickOnAddToCart: function($addTocart){
        goToCartIcon($addTocart);
      },
      getDiscountPrice: function(products) {
        var total = 0;
        $.each(products, function(){
          total += this.quantity * this.price;
        });
        return total * 1;
      }
    });

  });
  </script>
  
 
</body>
</html>