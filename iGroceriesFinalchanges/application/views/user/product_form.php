<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Stripe Gateway Integration | Codeigniter</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />

    <link rel="stylesheet" href="<?php echo base_url(); ?>/assets/css/style.css" />   

    <!-- jQuery is used only for this example; it isn't required to use Stripe -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" />

    <!-- Stripe JavaScript library -->
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>    
    <!-- <script type="text/javascript" src="https:">-->
    <script type="text/javascript">
        //set your publishable key
        Stripe.setPublishableKey('pk_test_mXgaMV1XBnXTke3iq3TtJzhP00mib398zP');
        
        //callback to handle the response from stripe
        function stripeResponseHandler(status, response) {
            if (response.error) {
                //enable the submit button
                $('#payBtn').removeAttr("disabled");
                //display the errors on the form
                // $('#payment-errors').attr('hidden', 'false');
                $('#payment-errors').addClass('alert alert-danger');
                $("#payment-errors").html(response.error.message);
            } else {
                var form$ = $("#paymentFrm");

                var token = response['id'];
                //insert the token into the form
                form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
                //submit form to the server
                form$.get(0).submit();
            } 
        }

        //callback to handle the response from stripe
        /*
            if(response.error){
                var form$ = response['id'];
                form$.append("<input type='hodden' name='stripeToken' value='" + token + "' />");
                form$.get(0).submit();
            }
            $('#payBtn').removeAttr("disabled);
        */

        $(document).ready(function() {
            //on form submit
            $("#paymentFrm").submit(function(event) {
                //disable the submit button to prevent repeated clicks
                $('#payBtn').attr("disabled", "disabled");
                
                //create single-use token to charge the user
                Stripe.createToken({
                    number: $('#card_num').val(),
                    cvc: $('#card-cvc').val(),
                    exp_month: $('#card-expiry-month').val(),
                    exp_year: $('#card-expiry-year').val()
                }, stripeResponseHandler);
                
                //submit from callback
                return false;
            });
        });
    </script>
</head>
<body style="height:100%;width:100%;">

<div class="container">
	<div class="row" style="margin-left: 375px;width: 100%;">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">Buy now <?php foreach($product as $row) { ?> <?php echo $row->item_name; ?>  </div>
                <div class="card-body bg-light">
                    <?php if (validation_errors()): ?>
                        <div class="alert alert-danger" role="alert">
                            <strong>Oops!</strong>
                            <?php echo validation_errors() ;?> 
                        </div>  
                    <?php endif ?>
                    <div id="payment-errors"></div>  
                     <form method="post" id="paymentFrm" enctype="multipart/form-data" action="<?php echo base_url(); ?>Shop/check">
                     <input type="hidden" name="item_number" value="<?php echo $row->item_id;?>">
                        <input type="hidden" name="item_name" value="<?php echo $row->item_name;?>">
                        <input type="hidden" name="item_price" value="<?php echo number_format($row->item_price);?>00">
                     <?php } ?>
                        <div class="form-group">
                            <input type="text" name="name" class="form-control" placeholder="Name" value="<?php echo set_value('name'); ?>" required>
                        </div>  

                        <div class="form-group">
                            <input type="email" name="email" class="form-control" placeholder="email@you.com" value="<?php echo set_value('email'); ?>" required />
                        </div>

                         <div class="form-group">
                            <input type="number" name="card_num" id="card_num" class="form-control" placeholder="Card Number" autocomplete="off" value="<?php echo set_value('card_num'); ?>" required>
                        </div>
                        
                        <div class="row">

                            <div class="col-sm-8">
                                 <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" name="exp_month" maxlength="2" class="form-control" id="card-expiry-month" placeholder="MM" value="<?php echo set_value('exp_month'); ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" name="exp_year" class="form-control" maxlength="4" id="card-expiry-year" placeholder="YYYY" required="" value="<?php echo set_value('exp_year'); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input type="text" name="cvc" id="card-cvc" maxlength="3" class="form-control" autocomplete="off" placeholder="CVC" value="<?php echo set_value('cvc'); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="text" name="full_address" class="form-control" placeholder="Full Address" required>
                        </div>  
                        <div class="form-group">
                            <input type="text" name="pincode" class="form-control" placeholder="Pincode" required>
                        </div>  
                        <div class="form-group">
                            <input type="text" name="city" class="form-control" placeholder="City"  required>
                        </div>  
                        <div class="form-group">
                            <input type="text" name="state" class="form-control" placeholder="State" required>
                        </div>  
                        
                        <div class="form-group"><input type="text" name="state" >
                        <div class="form-group">
                            <input type="text" name="country" class="form-control" placeholder="Country"  required>
                        </div>  
                        <div class="form-group text-right">
                          <button class="btn btn-secondary" type="reset">Reset</button>
                          <button type="submit" id="payBtn" class="btn btn-success">Submit Payment</button>
                        </div>
                    </form>     
                </div>
            </div>
                 
        </div>   
    </div>
</div>
<!-- Footer -->
<!-- <footer class="footer">
  <div class="container">
    Copyright &copy; <?php //echo date('Y'); ?>  
        <span class="float-right">Coded with Love &hearts;  : <a href="https://facebook.com/anburocky3" target="_blank">Anbuselvan Rocky</a></span>
  </div>
</footer> 
<footer><span class="flat-right">coded with love &hearts</footer>-->

</body>
</html>