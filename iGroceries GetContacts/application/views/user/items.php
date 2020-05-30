<!-- products-breadcrumb -->
<div class="products-breadcrumb" style="background:#062a06;">
		<div class="container">
			<ul>
			<li><i style="color:white;" class="fa fa-home" aria-hidden="true"></i><a style="color:white;" href="<?php echo base_url(); ?>">Home</a><span style="color:white;">|</span></li>
				<li style="color:white;">Products</li>
 			</ul>
		</div>
	</div>
<!-- //products-breadcrumb -->
<!-- banner -->
	<div class="banner">
	
		<div class="igl_banner_nav_right">
			<div class="igls_igl_banner_nav_right_grid">
            <h3 style="text-align:center;"><?php echo $cat; ?></h3>
            <?php if($items){ ?>
				<div class="igls_igl_banner_nav_right_grid1">
                <?php foreach($items as $row) { ?>
					<div class="col-md-3 igls_igl_banner_left">
						<div class="hover14 column">
						<div class="agile_top_brand_left_grid igl_agile_top_brand_left_grid">
						
                        
							<div class="agile_top_brand_left_grid1">
                           
								<figure>
                               
									<div class="snipcart-item block">
										<div class="snipcart-thumb">
											<a href="<?php echo base_url();?>Shop/prod_details?id=<?php echo $row->item_id; ?>"><img src="<?php echo base_url();?>uploads/<?php echo $row->item_image;?>" alt=" " style="width:100px;height:150px;" class="img-responsive" /></a>
											<p><?php echo $row->item_name;?></p>
											<h4><?php echo $row->item_price;?> </h4>
										</div>
			
									</div>
                       
								</figure>
                 
						  <a class="btn btn-primary" href="<?php echo base_url("Shop/add/".$row->item_id);?>" class="hvr-bounce-to-bottom" style="background:#84c639;color:white">Add To Cart</a>
			
							</div>
                        
						</div>
						</div>
					</div>
                    <?php } ?>
                <?php } else { ?><h3 style="text-align:center;">No product found</h3>
                <?php } ?>
                
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
<!-- //banner -->
