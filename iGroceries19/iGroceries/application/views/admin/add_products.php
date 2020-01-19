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
          <div class="widget-content nopadding">
            <form action="<?php echo base_url()?>Admin/insertProducts" method="post" class="form-horizontal" enctype="multipart/form-data">
            <div class="control-group">
                <label class="control-label">Item Name</label>
                <div class="controls">
                  <input type="text" name="item_name" class="span11" placeholder="Enter Product Name" required/>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label">Item Category</label>
                <div class="controls">
                  <select required name="item_category">
                    <option> Beverages </option>
                    <option> Bread & Bakery </option>
                    <option> Breakfast & Cereal</option>
                    <option> Oils </option>
                    <option> Snacks </option>
                  </select>
                </div>
              </div>
              
              <div class="control-group">
                <label class="control-label">Item Description</label>
                <div class="controls">
                  <textarea name="item_description" class="span11" required ></textarea>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label">Item Image</label>
                <div class="controls">
                  <input type="file" name="userfile" />
                </div>
              </div>
              <div class="control-group">
                <label class="control-label">Item Price</label>
                <div class="controls">
                  <input type="number" name="item_price" placeholder="Enter Price" class="span11" required>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label">Item Stock</label>
                <div class="controls">
                  <input type="number" name="item_stock" placeholder="Enter Number of items" class="span11" required>
                </div>
              </div>
              <div class="form-actions">
                <button type="submit" class="btn btn-success">Save</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    </div>
    </div>