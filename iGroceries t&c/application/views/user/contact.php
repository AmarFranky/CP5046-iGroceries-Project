<div class="products-breadcrumb" style="background:#062a06;">
		<div class="container">
			<ul>
			<li><i style="color:white;" class="fa fa-home" aria-hidden="true"></i><a style="color:white;" href="<?php echo base_url(); ?>">Home</a><span style="color:white;">|</span></li>
				<li style="color:white;">Contact Us</li>
 			</ul>
		</div>
	</div>
<!-- mail -->
<?php if($this->session->flashdata('success')){ ?>
					<div class="alert alert-success" role="alert" id="success-alert">
					<strong id="myWish"><?php echo $this->session->flashdata('success');?></strong> 
					</div>
<?php } ?>
		<div class="mail">
			<h3>Contact Us</h3>
			<div class="agileinfo_mail_grids">
				<div class="col-md-4 agileinfo_mail_grid_left">
					<ul>
						<li><i class="fa fa-home" aria-hidden="true"></i></li>
						<li>address<span>Unit 2, 57 Latham Street,
Chermside QLD- 4032 (AU).</span></li>
					</ul>
					<ul>
						<li><i class="fa fa-envelope" aria-hidden="true"></i></li>
						<li>email<span><a href="mailto:info@example.com">info@iGroceries.com</a></span></li>
					</ul>
					<ul>
						<li><i class="fa fa-phone" aria-hidden="true"></i></li>
						<li>call to us<span>(+123) 111 222 3333</span></li>
					</ul>
				</div>
				<div class="col-md-8 agileinfo_mail_grid_right">
					<form action="<?php echo base_url();?>Shop/ins_contact" method="post">
						<div class="col-md-6 wthree_contact_left_grid">
							<input type="text" name="uname" placeholder="Name" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Name*';}" required="">
							<input type="email" name="uemail" placeholder="Email" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Email*';}" required="">
						</div>
						<div class="col-md-6 wthree_contact_left_grid">
							<input type="text" name="ucontact" placeholder="Contact number" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Telephone*';}" required="">
							<input type="text" name="usubject" placeholder="Subject" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Subject*';}" required="">
						</div>
						<div class="clearfix"> </div>
						<textarea  name="umessage" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Message...';}" required="">Message...</textarea>
						<input type="submit" value="Submit">
						<input type="reset" value="Clear">
					</form>
				</div>
				<div class="clearfix"> </div>
			</div>
		</div>
<!-- //mail -->
		</div>
		<div class="clearfix"></div>
	</div>
<!-- //banner -->
<!-- map -->
	<div class="map">
		<iframe src="https://maps.google.com/maps?q=Unit%202%2C%2057%20Latham%20Street%2C%20Chermside%20QLD-%204032%20(AU)&t=&z=13&ie=UTF8&iwloc=&output=embed" style="border:0"></iframe>
	</div>
<!-- //map -->