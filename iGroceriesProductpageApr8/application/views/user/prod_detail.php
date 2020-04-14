<!-- products-breadcrumb -->
<div class="products-breadcrumb" style="background:#062a06;">
		<div class="container">
			<ul>
			<li><i style="color:white;" class="fa fa-home" aria-hidden="true"></i><a style="color:white;" href="<?php echo base_url(); ?>">Home</a><span style="color:white;">|</span></li>
				<li style="color:white;">Product Details</li>
 			</ul>
		</div>
	</div>
<!-- //products-breadcrumb -->

            <?php foreach($details as $row){ ?>
            <div class="w3l_banner_nav_right">
		
			<div class="agileinfo_single">
				<h5><?php echo $row->item_name; ?></h5>
				<div class="col-md-4 agileinfo_single_left">
					<img id="example" src="<?php echo base_url();?>uploads/<?php echo $row->item_image; ?>" alt=" " class="img-responsive" />
				</div>
				<div class="col-md-8 agileinfo_single_right">
					<div class="rating1">
						<span class="starRating">
							<input id="rating5" type="radio" name="rating" value="5">
							<label for="rating5">5</label>
							<input id="rating4" type="radio" name="rating" value="4">
							<label for="rating4">4</label>
							<input id="rating3" type="radio" name="rating" value="3" checked>
							<label for="rating3">3</label>
							<input id="rating2" type="radio" name="rating" value="2">
							<label for="rating2">2</label>
							<input id="rating1" type="radio" name="rating" value="1">
							<label for="rating1">1</label>
						</span>
					</div>
					<div class="w3agile_description">
						<h4>Description :</h4>
						<p><?php echo $row->item_description; ?></p>
					</div>
					<div class="snipcart-item block">
						<div class="snipcart-thumb agileinfo_single_right_snipcart">
							<h4>$<?php echo $row->item_price; ?></h4>
						</div>
						<button class="btn btn-submit" style="color:white;background:maroon">Buy Now</button>
                        <button class="btn btn-submit" style="color:white;background:#84c639">Add to cart</button>
					</div>
				</div>
				<div class="clearfix"> </div>
			</div>
		</div>
        <?php } ?>
