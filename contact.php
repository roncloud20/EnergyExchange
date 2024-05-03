<!DOCTYPE html>
<?php
  $pagetitle = "Contact Us";
  require_once "Resources/header.php";
?>
<script src="./home.js"></script>
<section id="contact">
      <div class="contact-text">
        <h3 class="s-header">Get in Touch</h3>
        <p>
          Lorem ipsum dolor sit amet consectetur adipisicing elit. Odio incidunt
          quo architecto.
        </p>
      </div>
      <div class="contact-container">
        <div class="form-container">
          <h2>Send Us A Message</h2>
          <form action="">
            <label for="name">Your Name</label>
            <input type="text" name="name" id="name" placeholder="john doe" />
            <label for="number">Phone Number</label>
            <input
              type="text"
              name="number"
              id="number"
              placeholder="123-456-789"
            />
            <label for="message">Message</label>
            <textarea
              type="text"
              placeholder="Hi i just want to know if you can do delivery"
            ></textarea>
            <label for="name">Your Email</label>
            <input
              type="email"
              name="email"
              id="email"
              placeholder="johndoe@gmail.com"
            />
            <input type="submit" value="Send Us Message" />
          </form>
        </div>
        <div class="contact-information">
          <div class="contact-information-container">
            <h3 class="s-header ">Contact Information</h3>
          <div class="location-box">
            <img src=".\Assets\location.png" alt="location logo" />
            <p>20 WASHINTHGON, <br>NEW YORK USA.</p>
          </div>
          <div class="location-box">
            <img src=".\Assets\phone.png" alt="photo logo" />
            <p>123-456-789 (001)</p>
          </div>
          <div class="location-box">
            <img src=".\Assets\email.png" alt="email logo" />
            <p>jondoe@gmail.com</p> </div>
           
          <div class="icons">
          <div class="icon-border"><img src=".\Assets\facebook-logo.png" alt="facebook icon" /></div>
          <div class="icon-border"><img src=".\Assets\twiiter-logo.png" alt="twitter icon" /></div>
          <div class="icon-border"><img src=".\Assets\linkedin-logo.png" alt="linkedin icon" /></div>
          <div class="icon-border"><img src=".\Assets\instagram-logo.png" alt="instagram icon" /></div>
          </div>
        </div>
        </div>
          
      </div>
    </section>
   
<section class="white-background"></section>

<?php
  require_once "Resources/footer.php";
?>