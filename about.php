<!DOCTYPE html>
<?php
  $pagetitle = "About Us";
  require_once "Resources/header.php";
?>
<script src="./home.js"></script>
<section id="about" class="about-container">
  <div class="about-text">
    <h2 class="s-header">Our Vision</h2>
    <p>
      Our Vision is to transform the energy sector by empowering individuals, promoting sustainability and creating financial opportunities.
    </p>
    <br />
    <p>
      We envision a world where energy trading is efficient, transparent, and accessible to all.  <br />
      <br />
      By leveraging the cutting-egde technology, fostering collaboration, and priotizing enviromental stewardship, we aim to drive positive change in the industry. Join us on this transformative journey toward a greener, more equitable energy future.
    </p>
  </div>
  <div class="about-img">
    <img src="./Assets/oil 1.png" alt="oil draining machine" />
  </div>
</section>
<section id="our-approach">
  <!-- <div class="icons">
    <img src="./Assets/Ringing Phone.png" alt="phone icon" />
    <img src="./Assets/Mobile Email.png" alt="" />
  </div> -->
  <div class="approach-container">
    <img src="./Assets/oil-sample.png" alt="oil" />
    <div class="approach-text">
      <h2 class="">Our Approach</h2>
      <p>
        At Energyxchange, we deliver what you need, precisely when you need it. Our Approach is Highly customizable to meet each client's unique requirements.
      </p>
      <br />
      <p>
        Energyxchange believes in collaboration. We actively partner with energy companies,sustainability organizations, and research institutions.
      </p>
      <br />
      <p>
      We continuosly explore new technologies to enhance user experience and optimize energy transactions.
      </p>
    </div>
  </div>
</section>
<section id="our-process">
  <div class="process-container">
    <div class="process-text">
      <h2 class="">Our Process</h2>
      <p>
        Energyxchange priotizes transparency throughout the trading process. We provide real-time Data, and clear insights, Our Users can make informed, decisions based on accurate information.
      </p>
      <br />
      <p>
        Our platform streamlines energy transactions. From bid submission to settlement, we optimize the process.
      </p>
      <br />
      <p>
        Energy markets involve low risks.Energyxchange helps users manage these risks effectively. We offer risk assessment tools, hedging strategies, and scenario modeling.
      </p>
    </div>
    <img src=".\Assets\dispenser.jpg" alt="oil" />
    
  </div>
</section>
<section class="register">
  <div class="text">
    <h3>Start Trading Energy Today!</h3>
    <p>Join our platform to start trading energy with ease.</p>
  </div>
  <div class="sign-btn">
    <a href="./signup.html"
      ><button class="signup-button">Sign Up</button></a
    >
    <a href="#"><button class="sign-more">Learn More</button></a>
  </div>
</section>

<?php
  require_once "Resources/footer.php";
?>