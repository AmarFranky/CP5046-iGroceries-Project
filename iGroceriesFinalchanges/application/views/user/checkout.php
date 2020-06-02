<link rel="stylesheet" href="<?php echo base_url();?>AssetsUser/checkout_css.css">
<head>
<style>
.noHover{
    pointer-events: none;
}
</style>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


    <!-- Stripe JavaScript library -->
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>    
    <!-- <script type="text/javascript" src="https:">-->
    <script type="text/javascript">
        //set your publishable key
        Stripe.setPublishableKey('pk_test_ZhDZS2H0j6oWf5blwtud6yxm00PyAGspb9');
        
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
<!-- products-breadcrumb -->
<div class="products-breadcrumb" style="background:#062a06;">
		<div class="container">
			<ul>
			<li><i style="color:white;" class="fa fa-home" aria-hidden="true"></i><a style="color:white;" href="<?php echo base_url(); ?>">Home</a><span style="color:white;">|</span></li>
				<li style="color:white;">Product Checkout</li>
 			</ul>
		</div>
	</div>
<!-- //products-breadcrumb -->
<body>
<!-- partial:index.partial.html -->
<!-- about -->

<!-- end about -->

<div class="daily">
 
   <div class="buttons"><button class="btn btn-open"><span>Credit Card Checkout</span></button></div>
</div>

<section class="modal open">
   <div class="wrapper">
      <div class="container">
    
         <div class="left">
            <div class="details">
               <article>
                  <h2 class="title">Details</h2>
               </article>
               
               <?php foreach($items as $row) { ?>  
<!-- Slider main container -->
<div class="swiper-container">
    <!-- Additional required wrapper -->
    <div class="swiper-wrapper">
        <!-- Slides -->
        <div class="swiper-slide"><img src="<?php echo base_url();?>uploads/<?php echo $row->item_image; ?>" alt=""></div>
             
    </div>
   <div class="navigation">
    <!-- If we need navigation buttons -->
  
      </div>

</div>

                  
             
               <article>
                  <h3 class="product"><?php echo $row->item_name; ?></h3>
                  <p class="type"><?php echo $row->item_description?></p>
                  <p class="quantity">Quantity: 1</p>
                  <p class="total">Total</p>
                  <p class="price">$<?php echo $row->item_price; ?></p>
               </article>
            
            </div>
         </div>
         <div class="right">
            <div class="form">
               <h2 class="title">Checkout</h2>
               <form method="post" id="paymentFrm" enctype="multipart/form-data" action="<?php echo base_url(); ?>Shop/check">
               <input type="hidden" name="item_number" value="<?php echo $row->item_id;?>">
               <input type="hidden" name="item_name" value="<?php echo $row->item_name;?>">
               <input type="hidden" name="item_price" value="<?php echo number_format($row->item_price);?>00">
            <?php } ?>

                  <div class="input-wrapper">
                     <label for="">Name</label>
                     <input type="text" name="name" class="form-control" placeholder="Name" value="<?php echo set_value('name'); ?>" required>
                  </div>

                  <div class="input-wrapper">
                     <label for="">Email</label>
                     <input type="email" name="email" class="form-control" placeholder="email@you.com" value="<?php echo set_value('email'); ?>" required />
                  </div>

                  <div class="input-wrapper">
                     <label for="number">Card Number</label>
                     <input type="number" name="card_num" id="card_num" class="form-control" placeholder="Card Number" autocomplete="off" value="<?php echo set_value('card_num'); ?>" required>
                  </div>

                  <div class="double">
                     <label for="date">Expiration Month/Year</label>

                     <div class="double-input">
                        <div class="input-wrapper">
                        <input type="text" name="exp_month" maxlength="2" class="form-control" id="card-expiry-month" placeholder="MM" value="<?php echo set_value('exp_month'); ?>" required>
                        </div>

                        <div class="input-wrapper">
                        <input type="text" name="exp_year" class="form-control" maxlength="4" id="card-expiry-year" placeholder="YYYY" required="" value="<?php echo set_value('exp_year'); ?>">
                        </div>
                     </div>
                  </div>

                  <div class="double">
                     <label for="date">CVC</label>

                     <div class="double-input">
                        <div class="input-wrapper">
                        <input type="text" name="cvc" id="card-cvc" maxlength="3" class="form-control" autocomplete="off" placeholder="CVC" value="<?php echo set_value('cvc'); ?>" required>
                        </div>

                     </div>
                  </div>
                  <div class="double">
                     <label for="date">Address</label>

                     <div class="double-input">
                        <div class="input-wrapper">
                        <input type="text" name="full_address" class="form-control" placeholder="Full Address" required>
                        </div>

                     </div>
                  </div>
                  <div class="double">
                     <label for="date">Pincode</label>

                     <div class="double-input">
                        <div class="input-wrapper">
                        <input type="text" name="pincode" class="form-control" placeholder="Pincode" required>
                        </div>

                     </div>
                  </div>
                  <div class="double">
                     <label for="date">City</label>

                     <div class="double-input">
                        <div class="input-wrapper">
                        <input type="text" name="city" class="form-control" placeholder="City"  required>
                        </div>

                     </div>
                  </div>

                      <div class="double">
                     <label for="date">State</label>

                     <div class="double-input">
                        <div class="input-wrapper">
                        <input type="text" name="state" class="form-control" placeholder="State" required>
                        </div>

                     </div>
                  </div>    <div class="double">
                     <label for="date">Country</label>

                     <div class="double-input">
                        <div class="input-wrapper">
                        <input type="text" name="country" class="form-control" placeholder="Country"  required>
                        </div>

                     </div>
                  </div>
                 <input type="submit" value="Confirm Order">
                 <input type="reset" value="Clear Fields">

               </form>
         
            </div>
         </div>
      </div>
   </div>
</section>
<!-- partial -->
  <script src='https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.0/js/swiper.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.0/js/swiper.min.js'></script><script  src="./script.js"></script>
<script>
var inputField = document.querySelectorAll("input"),
    inputWrapper = document.querySelectorAll(".input-wrapper"),
    iconClose = document.querySelector(".icon-close"),
    btnOpen = document.querySelector(".btn-open"),
    modal = document.querySelector(".modal");

//Events
btnOpen.addEventListener("click", openModal);
iconClose.addEventListener("click", closeModal);

// Event to Anime Input
inputField.forEach(function(el) {
   el.addEventListener("focus", animeInput);
   el.addEventListener("focusout", removeAnimeInput);
});

// To anime input
function animeInput(event) {
   event.target.closest(".input-wrapper").classList.add("active");
}
function removeAnimeInput(event) {
   if (event.target.value == "") {
      event.target.closest(".input-wrapper").classList.remove("active");
   }
}

   
 

   
// Open Modal

function openModal() {
   modal.classList.add("open");

}

function closeModal() {
   modal.classList.add("close");

   setTimeout(function() {
      modal.classList.remove("open");
      modal.classList.remove("close");
   }, 1500);

}
  
      //Swiper


   var mySwiper = new Swiper(".swiper-container", {
      // Optional parameters
      direction: "horizontal",
      loop: true,
      effect: "coverflow",
      centeredSlides: true,
      speed: 800,
      coverflowEffect: {
         rotate: 60,
         stretch: 10,
         depth: 150,
         modifier: 2,
         slideShadows: false
      },

      // If we need pagination
      pagination: {
         el: ".swiper-pagination",
         clickable: true
      },

      // Navigation arrows
      navigation: {
         nextEl: ".swiper-button-next",
         prevEl: ".swiper-button-prev"
      }
   });
</script>
</body>