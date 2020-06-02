
<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="<?php echo base_url();?>Admin/dashboard" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Dashboard</a> <a href="#" class="current">Manage Orders</a> </div>
    <h1>Orders</h1>
  </div>
  <!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title> Orders Table</title>
  <link rel='stylesheet' href='https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css'>
<link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css'><link rel="stylesheet" href="./style.css">
<style>
label{
  display: block;
margin-bottom: 5px;
margin-left: 919px;
}
</style>

</head>
<body>
<!-- partial:index.partial.html -->
<table id="example" class="display nowrap" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Customer name</th>
                <th>Email</th>
                <th>Card Number</th>
                <th>Item Name</th>
                <th>Item Number</th>
                <th>Item Price</th>
                <th>Full Address</th>
                <th>Pincode</th>
                <th>City</th>
               
                <th>Country</th>
                <th>Paid Amount</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($orders as $row){ ?>
            <tr>
                <td><?php echo $row->name; ?></td>
                <td><?php echo $row->email; ?></td>
                <td><?php echo $row->card_num; ?></td>
                <td><?php echo $row->item_name; ?></td>
                <td><?php echo $row->item_number; ?></td>
                <td><?php echo $row->item_price; ?></td>
                <td><?php echo $row->full_address; ?></td>
                <td><?php echo $row->pincode; ?></td>
                <td><?php echo $row->city; ?></td>
             
                <td><?php echo $row->country; ?></td>
                <td><?php echo $row->paid_amount; ?></td>
            </tr>
        <?php } ?>
           
           
        </tbody>
    </table>
<!-- partial -->
  <script src='https:////code.jquery.com/jquery-1.12.4.js'></script>
<script src='https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js'></script>
<script src='https://cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js'></script>
<script src='https://cdn.datatables.net/buttons/1.2.4/js/buttons.flash.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js'></script>
<script src='https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js'></script>
<script src='https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js'></script>
<script src='https://cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js'></script>
<script src='https://cdn.datatables.net/buttons/1.2.4/js/buttons.print.min.js'></script>
<script src='https://cdn.datatables.net/buttons/1.2.4/js/buttons.colVis.min.js'></script><script  src="<?php echo base_url();?>assetsAdmin/script.js"></script>

</body>
</html>


</body>
</html>
