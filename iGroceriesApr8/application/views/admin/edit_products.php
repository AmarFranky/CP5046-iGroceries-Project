<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="#" title="Products" class="tip-bottom"><i class="icon-shopping-bag"></i> <b> Add Products</b> </a></div>
  </div>
<div class="container-fluid">
    <div class="row-fluid">
<div class="span6" style="margin-top: 60px;">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
            <h5>Products Form</h5>
          </div>
          <?php foreach($product as $row ){ ?>
          <div class="widget-content nopadding">
            <form action="<?php echo base_url()?>Admin/updateProduct?id=<?php echo $row->item_id; ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
            <div class="control-group">
     
                <label class="control-label">Item Name</label>
                <div class="controls">
                  <input type="text" name="item_name" class="span11" value="<?php echo $row->item_name; ?>" required/>
                </div>
              </div>
              
              <div class="control-group">
                <label class="control-label">Item Description</label>
                <div class="controls">
                  <textarea name="item_description" value="<?php echo $row->item_description; ?>" class="span11" required ><?php echo $row->item_description; ?></textarea>
                </div>
              </div>
             
              <div class="control-group">
                <label class="control-label">Item Price</label>
                <div class="controls">
                  <input type="number" name="item_price" value="<?php echo $row->item_price; ?>" class="span11" required>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label">Item Stock</label>
                <div class="controls">
                  <input type="number" name="item_stock" value="<?php echo $row->item_stock; ?>" class="span11" required>
                </div>
              </div>
              <div class="form-actions">
                <button type="submit" class="btn btn-success">Update</button>
              </div>
                  <?php } ?>
            </form>
          </div>
        </div>
      </div>
    </div>
    </div>
    </div>