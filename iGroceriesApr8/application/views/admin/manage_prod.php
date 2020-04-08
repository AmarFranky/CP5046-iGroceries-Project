<?php include('header.php' )?>
<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="<?php echo base_url();?>Admin/dashboard" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Dashboard</a> <a href="#" class="current">Manage Products</a> </div>
    <h1>Products</h1>
  </div>
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title">
             <span class="icon"><i class="icon-th"></i></span> 
            <h5>Data table (Products)</h5>
          </div>
          <div class="widget-content nopadding">
            <table class="table table-bordered data-table">
              <thead>
                <tr>
                  <th>Item Name</th>
                  <th>Item Price</th>
                  <th>Item Category</th>
                  <th>Item Description</th>
                  <th>Item Stock</th>
                  <th>Item Image</th>
                  <th>Item Edit</th>
                  <th>Item Delete</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach($products as $row){ ?>
                <tr class="gradeX">
                    <td ><?php echo $row->item_name; ?></td>
                    <td><?php echo $row->item_price; ?></td>
                    <td><?php echo $row->item_category; ?></td>
                    <td><?php echo $row->item_description; ?></td>
                    <td><?php echo $row->item_stock; ?></td>
                    <td><?php echo $row->item_image; ?></td>
                    <td><a href="<?php echo base_url()?>Admin/loadEditProduct?id=<?php echo $row->item_id; ?>">Edit</a></td>
                    <td><a href="<?php echo base_url()?>Admin/del_prod?id=<?php echo $row->item_id; ?>">Delete</a></td>
                </tr>
              <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      
    </div>
  </div>
</div>

<script src="<?php echo base_url(); ?>assetsAdmin/js/jquery.min.js"></script> 
<script src="<?php echo base_url(); ?>assetsAdmin/js/jquery.ui.custom.js"></script> 
<script src="<?php echo base_url(); ?>assetsAdmin/js/bootstrap.min.js"></script> 
<script src="<?php echo base_url(); ?>assetsAdmin/js/jquery.uniform.js"></script> 
<script src="<?php echo base_url(); ?>assetsAdmin/js/select2.min.js"></script> 
<script src="<?php echo base_url(); ?>assetsAdmin/js/jquery.dataTables.min.js"></script> 
<script src="<?php echo base_url(); ?>assetsAdmin/js/maruti.js"></script> 

</body>
</html>
