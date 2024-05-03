<?php
  $pagetitle = "Login";
  require_once "Resources/header.php";
  require_once "Resources/db_connect.php";
?>
<script src="./home.js" defer></script>

<style>
  main {
        display: flex;
        flex-direction: column;
        height:50vh;
        justify-content: center;
        align-items: center;
  }

  h2 {
    color: #273a89;
  }
  input[type=text], input[type=email], input[type=password], select {
    width: 100%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
  }

  input[type=submit] {
    width: 100%;
    background-color: #273a89;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }

  input[type=submit]:hover {
    background-color: #45a049;
  }

  div.container, form {
    border-radius: 5px;
    background-color: #f2f2f2;
    padding: 20px;
  }
  p,
    p > a {
        text-decoration: none;
        color: #273a89;
        font-weight: bold;
        transition: all 1s ease-in;
    }

    p > a:hover {
        font-size: 1.1rem;
        text-decoration: underline;
    }

    @media (max-width:700px ){
        main {
        display: flex;
        flex-direction: column;
        height: auto;
        justify-content: center;
        align-items: center;
   }
      main form {
        border-radius: 5px;
        background-color: #f2f2f2;
        padding: 3rem;
        margin-top : 2.5rem; 
    } 

    }
</style>

<?php
  // Check if the form is submitted
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate user credentials against the database
    $sql = "SELECT * FROM users WHERE Email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
      $user = $result->fetch_assoc();

      // Verify password
      if (password_verify($password, $user['Password'])) {
        // Password is correct, perform login actions
        if ($user['EmailVerified'] == 1) {
          session_start();
          $_SESSION['user_id'] = $user['UserID'];
          $_SESSION['user_email'] = $user['Email'];
          $_SESSION['user_level'] = $user['UserLevel'];
          // Redirect to a dashboard or home page
          header("Location: dashboard.php");
          // exit();
        } else {
          $error = "Email has not been verified";
        }
      } else {
        $error = "Invalid password";
      }
    } else {
      $error = "Invalid email address";
    }

    $stmt->close();
  }
?>
  <main>
<form action="" method="post">
  <h2>Login</h2>
  <input type="email" id="email" name="email" placeholder="Enter E-Mail Address" required>

  <input type="password" id="password" name="password" placeholder="Enter Password" required>
  <?php if (isset($error)) : ?>
    <p style="color: red;"><?php echo $error; ?></p>
  <?php endif; ?>

  <input type="submit" value="Login"/>

  <p style='color: black; text-align: center;'>Don't have an account: <a href='signup.php'>click here to Sign Up</a></p>
</form>
  </main>
<?php
  require_once "Resources/footer.php";
?>