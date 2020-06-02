<!DOCTYPE html>
<html lang="en">
<head>
<title>iGroceries::Admin</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assetsAdmin/css/bootstrap.min.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assetsAdmin/css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assetsAdmin/css/fullcalendar.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assetsAdmin/css/style.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assetsAdmin/css/media.css" class="skin-color" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body style="background:#f0f0f0;">
<!--<h2>iGroceries::Admin</h2>-->
<!--Header-part-->
<div id="header">
  <h1><a href="#">iGroceries Admin</a></h1>
</div>

<!--close-Header-part--> 

<!--top-Header-messaages-->
<div class="btn-group rightzero"> <a class="top_message tip-left" title="Manage Files"><i class="icon-file"></i></a> <a class="top_message tip-bottom" title="Manage Users"><i class="icon-user"></i></a> <a class="top_message tip-bottom" title="Manage Comments"><i class="icon-comment"></i><span class="label label-important">5</span></a> <a class="top_message tip-bottom" title="Manage Orders"><i class="icon-shopping-cart"></i></a> </div>
<!--close-top-Header-messaages--> 

<!--top-Header-menu-->
<div id="user-nav" class="navbar navbar-inverse">
  <ul class="nav">
    <li class="" ><a title="" href="#"><i class="icon icon-user"></i> <span class="text">Welcome Back! <?php echo $this->session->userdata('fname');?> <?php echo $this->session->userdata('lname');?></span></a></li>
    <li class=" dropdown" id="menu-messages"><a href="#" data-toggle="dropdown" data-target="#menu-messages" class="dropdown-toggle"><i class="icon icon-envelope"></i> <span class="text">Messages</span> <b class="caret"></b></a>
      <ul class="dropdown-menu">
        <li><a class="sInbox" title="" href="#">inbox</a></li>

      </ul>
    </li>
    <li class=""><a title="" href="<?php echo base_url(); ?>Admin/logout"><i class="icon icon-share-alt"></i> <span class="text">Logout</span></a></li>
  </ul>
</div>

<!--close-top-Header-menu-->


<div id="sidebar" style="background:grey;">
  <div id="search">

  </div>
  <a href="#" class="visible-phone"><i class="icon icon-th-list"></i> Common Elements</a><ul>
    <li class="active"><a href="<?php echo base_url();?>Admin/dashboard"> <span>Dashboard</span></a> </li>
    <li> <a href="<?php echo base_url();?>Admin/loadAddProducts"> <span> Add Products </span></a> </li>
    <li> <a href="<?php echo base_url();?>Admin/loadManageProduct"> <span> Manage Products </span></a> </li>
    <li><a href="#"> <span> Manage Transactions </span></a></li>
    <li><a href="<?php echo base_url();?>Admin/manageCust"> <span> Manage Customers</span></a></li>
    <li><a href="<?php echo base_url();?>Admin/get_contacts"><span> Get Contacts </span></a></li>
    <li><a href="<?php echo base_url();?>Admin/get_orders"><span> Manage Orders </span></a></li>
    </li>
    </li>
  </ul>
</div>
  </div>
</div>
</div>
</div>