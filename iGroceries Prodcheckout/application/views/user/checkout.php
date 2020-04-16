<link rel="stylesheet" href="<?php echo base_url();?>AssetsUser/checkout_css.css">
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
               <?php } ?>
            </div>
         </div>
         <div class="right">
            <div class="form">
               <h2 class="title">Checkout</h2>
               <form>

                  <div class="input-wrapper">
                     <label for="">Payment Method</label>
                     <select name="cards">
  <option value="visa">Visa</option>
  <option value="mastercard">Mastercard</option>
  <option value="americanexpress">American Express</option>

</select>
                  </div>

                  <div class="input-wrapper">
                     <label for="">Cardholder's name</label>
                     <input id="name" type="text" placeholder="Name">
                  </div>

                  <div class="input-wrapper">
                     <label for="number">Card Number</label>
                     <input id="number" type="text" placeholder="XXXX-XXXX-XXXX-XXXX">
                  </div>

                  <div class="double">
                     <label for="date">Expiration Date</label>

                     <div class="double-input">
                        <div class="input-wrapper">
                           <input id="date" type="number" placeholder="Month">
                        </div>

                        <div class="input-wrapper">
                           <input id="date" type="number" placeholder="Year">
                        </div>
                     </div>
                  </div>

                  <div class="double">
                     <label for="date">Security Code</label>

                     <div class="double-input">
                        <div class="input-wrapper">
                           <input id="date" type="number" placeholder="Code" maxlength="3" minlength="0">
                        </div>

                     </div>
                  </div>
               </form>
               <button class="btn"><span>Confirm Order</span></button>
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