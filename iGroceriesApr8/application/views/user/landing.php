<!-- BEGIN PHP Live! HTML Code [V3] -->
<span style="color: #0000FF; text-decoration: underline; line-height: 0px !important; cursor: pointer; position: fixed; bottom: 0px; right: 20px; z-index: 20000000;" id="phplive_btn_1579256171"></span>
<script data-cfasync="false" type="text/javascript">

(function() {
var phplive_href = encodeURIComponent( location.href ) ;
var phplive_e_1579256171 = document.createElement("script") ;
phplive_e_1579256171.type = "text/javascript" ;
phplive_e_1579256171.async = true ;
phplive_e_1579256171.src = "https://t2.phplivesupport.com/igroceries/js/phplive_v2.js.php?v=0%7C1579256171%7C2%7C&r="+phplive_href ;
document.getElementById("phplive_btn_1579256171").appendChild( phplive_e_1579256171 ) ;
if ( [].filter ) { document.getElementById("phplive_btn_1579256171").addEventListener( "click", function(){ phplive_launch_chat_0() } ) ; } else { document.getElementById("phplive_btn_1579256171").attachEvent( "onclick", function(){ phplive_launch_chat_0() } ) ; }
})() ;

</script>
<!-- END PHP Live! HTML Code [V3] -->
<?php if($this->session->flashdata('success')){ ?>
					<div class="alert alert-danger" role="alert" id="success-alert">
					<strong id="myWish"><?php echo $this->session->flashdata('success');?></strong> 
					</div>
<?php } ?>	
<!--<section>-->
<section class="slider">
	<div class="flexslider">
		<ul class="slides">
			<li>
				<div class="ig_banner_nav_right_banner1">
					<div class="more">
						<a href="#" style="margin-top: 250px;" class="button--saqui button--round-l button--text-thick" data-text="Shop now">Shop now</a>
					</div>
				</div>
			</li>
			<li>
				<div class="ig_banner_nav_right_banner2">
					<div class="more">
						<a href="#" style="margin-top: 250px;" class="button--saqui button--round-l button--text-thick" data-text="Shop now">Shop now</a>
					</div>
				</div>
			</li>
			<li>
				<div class="ig_banner_nav_right_banner3">
					<div class="more">
						<a href="#" style="margin-top: 250px;" class="button--saqui button--round-l button--text-thick" data-text="Shop now">Shop now</a>
					</div>
				</div>
			</li>
			<li>
				<div class="ig_banner_nav_right_banner4">
					<div class="more">
						<a href="#" style="margin-top: 250px;" class="button--saqui button--round-l button--text-thick" data-text="Shop now">Shop now</a>
					</div>
				</div>
			</li>
			<li>
				<div class="ig_banner_nav_right_banner5">
					<div class="more">
						<a href="#" style="margin-top: 250px;" class="button--saqui button--round-l button--text-thick" data-text="Shop now">Shop now</a>
					</div>
				</div>
			</li>
			<li>
				<div class="ig_banner_nav_right_banner6">
					<div class="more">
						<a href="#" style="margin-top: 250px;" class="button--saqui button--round-l button--text-thick" data-text="Shop now">Shop now</a>
					</div>
				</div>
			</li>
			<li>
				<div class="ig_banner_nav_right_banner7">
					<div class="more">
						<a href="#" style="margin-top: 250px;" class="button--saqui button--round-l button--text-thick" data-text="Shop now">Shop now</a>
					</div>
				</div>
			</li>
			<li>
				<div class="ig_banner_nav_right_banner8">
					<div class="more">
						<a href="#" style="margin-top: 250px;" class="button--saqui button--round-l button--text-thick" data-text="Shop now">Shop now</a>
					</div>
				</div>
			</li>
			
		</ul>
	</div>
</section>
	<div class="top-brands">
		<div class="container">
			<h3>New Arrivals(Demo Products)</h3>
			<div class="agile_top_brands_grids">
			<?php foreach($new as $row){ ?>
				<div class="col-md-3 top_brand_left">
					<div class="hover14 column">
						<div class="agile_top_brand_left_grid">
							
							<div class="agile_top_brand_left_grid1">
								<figure>
									<div class="snipcart-item block" >
										<div class="snipcart-thumb">
											<a href="#"><img title=" " alt=" " class="img-responsive" src="<?php echo base_url()?>uploads/<?php echo $row->item_image; ?>" /></a>		
											<p><?php echo $row->item_name; ?></p>
											<h4><?php echo $row->item_price; ?></h4>
										</div>
										
									</div>
								</figure>
							</div>
						</div>
						
					</div>
				
				</div>
				<?php } ?>
			</div>
		</diV>
	</div>

	<section class="slider">
				<div class="flexslider">
					<ul class="slides">
						<li>
							<div class="ig_banner_nav_right_banner" style="margin-bottom:0px;">
							</div>
						</li>
					</ul>
				</div>
				
			</section>
<!-- //top-brands -->
<!-- flexSlider -->
<link rel="stylesheet" href="<?php echo base_url();?>assetsUser/css/flexslider.css" type="text/css" media="screen" property="" />
	<script defer src="<?php echo base_url();?>assetsUser/js/jquery.flexslider.js"></script>
	<script type="text/javascript">
	$(window).load(function(){
		$('.flexslider').flexslider({
		animation: "slide",
		slideshowSpeed: 7000,
        animationSpeed: 600,
		start: function(slider){
			$('body').removeClass('loading');
		}
		});
	});
	</script>
<!-- //flexSlider -->