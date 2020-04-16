<!DOCTYPE html>
<html lang="en">
    
<head>
        <title>Admin Login</title><meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>assetsAdmin/css/bootstrap.min.css" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>assetsAdmin/css/bootstrap-responsive.min.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>assetsAdmin/css/login.css" />
    </head>
    <body>
        <div id="loginbox">            
            <form id="loginform" class="form-vertical" action="<?php echo base_url(); ?>Admin/isAdminLogin" method="post">
				 <div class="control-group normal_text" style="background:green;"> <h3><img src="<?php echo base_url(); ?>assetsAdmin/img/iglogo.png" style="height:120px;width:120px;" alt="Logo" /></h3></div>
                <div class="control-group" >
                    <div class="controls">
                        <div class="main_input_box">
                            <span class="add-on"><i class="icon-user"></i></span><input type="email" name="email" placeholder="Admin Email" required/>
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <div class="main_input_box">
                            <span class="add-on"><i class="icon-lock"></i></span><input type="password" name="password" placeholder="Admin Password" required/>
                        </div>
                    </div>
                </div>
               <div class="form-actions">
            <!--<span class="pull-left"><a href="<?php echo base_url(); ?>assetsAdmin/#" class="flip-link btn btn-inverse" id="to-recover">Lost password?</a></span>-->
                    <span class="pull-right"><input type="submit" class="btn btn-success" value="Login" /></span>
                </div>
               
            </form>

            <?php if($this->session->flashdata('failed')){ ?>
            <div class="alert alert-danger" role="alert">
             <?php echo $this->session->flashdata('failed'); ?>
            </div>
            <?php } ?>
            
               
               <!-- <div class="form-actions">
                    <span class="pull-left"><a href="<?php echo base_url(); ?>assetsAdmin/#" class="flip-link btn btn-inverse" id="to-login">&laquo; Back to login</a></span>
                    <span class="pull-right"><input type="submit" class="btn btn-info" value="Recover" /></span>
                </div>-->
            </form>
        </div>
 
        
        <script src="<?php echo base_url(); ?>assetsAdmin/js/jquery.min.js"></script>  
        <script src="<?php echo base_url(); ?>assetsAdmin/js/maruti.login.js"></script> 
    </body>

</html>
