
<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="<?php echo base_url();?>Admin/dashboard" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Dashboard</a> <a href="#" class="current">Manage Products</a> </div>
    <h1>Contacts</h1>
  </div>
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title">
             <span class="icon"><i class="icon-th"></i></span> 
            <h5>Data table (Contacts)</h5>
          </div>
          <div class="widget-content nopadding">
            <table class="table table-bordered data-table">
              <thead>
                <tr>
                  <th>Sender Name</th>
                  <th>Sender Email</th>
                  <th>Subject</th>
                  <th>Message</th>
                  <th>Sender Contact</th>
                  
                </tr>
              </thead>
              <tbody>
              <?php foreach($contacts as $row){ ?>
                <tr class="gradeX">
                    <td style="text-align:center;"><?php echo $row->uname; ?></td>
                    <td style="text-align:center;"><?php echo $row->uemail; ?></td>
                    <td style="text-align:center;"><?php echo $row->usubject; ?></td>
                    <td style="text-align:center;"><?php echo $row->umessage; ?></td>
                    <td style="text-align:center;"><?php echo $row->ucontact; ?></td>
      
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
