<style>
.video {
    position: relative;
    padding-bottom: 56.25%;
    height: 0;
    overflow: hidden;
    margin-bottom: 4em;
}
.video iframe,
.video object,
.video embed {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}
</style>
<div class="products-breadcrumb" style="background:#062a06;">
		<div class="container">
			<ul>
			<li><i style="color:white;" class="fa fa-home" aria-hidden="true"></i><a style="color:white;" href="<?php echo base_url(); ?>">Home</a><span style="color:white;">|</span></li>
				<li style="color:white;">About Us</li>
 			</ul>
		</div> 
	</div>
<!-- about -->
<!-- signup -->
<!-- login -->
<div class="ig_login">
			
			<div class="ig_login_module">
				<div class="module form-module">
				  <div class="toggle"><i class="fa fa-times fa-pencil"></i>
			
				  </div>
				
				  <div class="form">
					<h2>Create an account</h2>
					<form action="<?php echo base_url();?>Shop/add_customer" method="post" enctype="multipart/form-data"> 
					  <input type="text" name="first_name" placeholder="First Name" required=" ">
                      <input type="text" name="last_name" placeholder="Last Name" required=" ">
                      <input type="text" name="address" placeholder="Address" required=" ">
                      <select name="gender">
                         <option value="Male">Male</option>
                         <option value="Female">Female</option>
                      </select>
                      <input type="text" name="contact" placeholder="Contact Number" required=" ">
                      <input type="email" name="email" placeholder="Email Address" required=" ">
					  <input type="password" name="password" id="txtPassword" placeholder="Password" required=" ">
					  <input type="password" name="conf_password" id="txtConfirmPassword" placeholder="Confirm Password" required=" ">
				
					  <input type="file" name="file" required=" ">
					  <input type="submit" id="btnSubmit" value="Create Account">
					</form>
				  </div>
				  <div class="cta"><a href="#">Already have an account?</a></div>
				</div>
			</div>
			<script>
				$('.toggle').click(function(){
				  // Switches the Icon
				  $(this).children('i').toggleClass('fa-pencil');
				  // Switches the forms  
				  $('.form').animate({
					height: "toggle",
					'padding-top': 'toggle',
					'padding-bottom': 'toggle',
					opacity: "toggle"
				  }, "slow");
				});
			</script>
			
		</div>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript">
    $(function () {
        $("#btnSubmit").click(function () {
            var password = $("#txtPassword").val();
            var confirmPassword = $("#txtConfirmPassword").val();
            if (password != confirmPassword) {
                alert("Passwords do not match.");
                return false;
            }
            return true;
        });
    });
</script>
<!-- //login -->
<!-- end signup -->
<!-- //testimonials -->
</body>