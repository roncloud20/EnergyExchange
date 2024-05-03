<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <link rel="shortcut icon" href="Assets/exchange.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="Resources/style.css"/>
    <script src="../home.js" defer></script>
    <title><?= $pagetitle?></title>
  </head>
  <body>
    <header>
      <a href="./index.php"><img src="Assets/whiteexchangee.png" alt="Logo" width="200px"/></a>
      <nav>
        <ul id="menu">
          <li><a href="./index.php">HOME</a></li>
          <li><a href="./about.php">ABOUT US</a></li>
          <li><a href="#product">PRODUCTS</a></li>
          <li><a href="./service.php">SERVICES</a></li>
          <li><a href="./newsletter.php">NEWS & EVENTS</a></li>
          <li><a href="./signin.php"><span id="signup-span">SIGN IN</span></a></li>
        </ul>
        <div class="hamburger-container">
          <img class="hamburger" src="./Assets/Menu.png" alt="" />
        </div>
      </nav>
    </header>
