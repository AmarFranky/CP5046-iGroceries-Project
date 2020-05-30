<?php include('header.php'); ?>

<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="#" title="Admin Home" class="tip-bottom"><i class="icon-home"></i> <b>Dashboard</b> </a></div>
  </div>
  <div class="container-fluid">
   	<div class="quick-actions_homepage">
    <ul class="quick-actions">
          <li> <a href="#"> <i class="icon-dashboard"></i> My Dashboard </a> Admin  </li>
          <li> <a href="<?php echo base_url();?>Admin/loadAddProducts"> <i class="icon-shopping-bag"></i> Total Products</a> <?php echo $products;?> </li>
          <li> <a href="#"> <i class="icon-money"></i> Total Orders </a><?php echo $orders;?> </li>
          <li> <a href="#"> <i class="icon-people"></i> Total Customers </a><?php echo $customers; ?> </li>
     
        </ul>
   </div>

<?php include('footer.php'); ?>