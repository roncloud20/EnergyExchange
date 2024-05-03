<?php
    $pagetitle = "Becoming a Member";
    require_once "Resources/header.php";  
    require_once "Resources/db_connect.php";
?>
<script src="./home.js" defer></script>

<style>
    h2 {
        color: #273a89;
        text-align: center;
    }

    #image-label{
        position: relative;
        width: 200px;
        height: 200px;
        background: #fff;
        background-position: center;
        background-repeat: no-repeat;
        background-size: contain;
        display:flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0px 1px 7px rgba(105, 110, 232, 0.54);
        border-radius: 10px;
        flex-direction: column;
        gap: 15px;
        user-select: none;
        cursor: pointer;
        color: #207ed1;
        transition: all 1s;
   }
   #image-label:hover{
        color: #18ac1c;
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

    div.container {
        margin: 50px 100px;
        border-radius: 5px;
        background-color: #f2f2f2;
        padding: 20px;
    }
    span.error {
        color: red;
        font-style: italic;
    }
</style>

<?php 
    // Making the varibles empty
    $firstName = $middleName = $lastName = $email = $password = $cpassword = $sponsorID = "";

    // Making the errors varibles empty
    $mlm = $msg = $dperr = $emerr = $pwerr = $sperr = "";

    // Using the post method to capture user information
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Capturing user information
        $firstName = htmlspecialchars($_POST['firstName']);
        $middleName = htmlspecialchars($_POST['middleName']);
        $lastName = htmlspecialchars($_POST['lastName']);
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);
        $cpassword = htmlspecialchars($_POST['cpassword']);
        $sponsorID = htmlspecialchars($_POST['sponsorID']);


        // Check if a file is uploaded
        if (!empty($_FILES['profile_picture']['name'])) {
            // Code to handle the uploaded image
            $targetDirectory = "profile_dp/";

            // Construct a unique filename based on firstname, lastname, and a unique number
            $uniqueNumber = uniqid();
            $targetFile = $targetDirectory . $firstName . '_' . $lastName . '_' . $uniqueNumber . '.' . pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);

            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Validate file type (you can add more validation if needed)
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                $dperr = "Invalid file type. Only JPG, JPEG, and PNG files are allowed.";
                $uploadOk = 0;
            }
        } else {
            // Use default image (brand logo) if no file is uploaded
            $defaultImageURL = "profile_dp/default.png";
            $dpURL = $defaultImageURL;
        }

        // Validating the email address
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Using prepared statement to avoid SQL injection
            $sql = "SELECT * FROM users WHERE email=?";
            $stmt = mysqli_prepare($conn, $sql);
            // Bind the parameter
            mysqli_stmt_bind_param($stmt, "s", $email);
            // Execute the statement
            mysqli_stmt_execute($stmt);
            // Get the result
            $result = mysqli_stmt_get_result($stmt);
            // Check if the email already exists
            if (mysqli_num_rows($result) > 0) {
                $emerr = "Email Address already exists";
            }

            // Close the statement
            mysqli_stmt_close($stmt);
        } else {
            $emerr = "Invalid Email Address";
        }

        // Validating the password and cpassword
        if(!empty($password) || !empty($cpassword)) {
            if ($password == $cpassword) {
                $hashpass = password_hash($password, PASSWORD_DEFAULT); // or use a specific cost parameter
            } else {
                $pwerr = "Passwords do not match";
            }
        } else {
            $pwerr = "Either Password or Confirm Password is empty";
        }

        // Generate a unique verification token
        $verificationToken = bin2hex(random_bytes(32));

        // Validating the SponsorID 
        $stmt = $conn->prepare("SELECT UserID FROM Users WHERE UserID = ?");
        $stmt->bind_param("i", $sponsorID);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!($result->num_rows > 0)) {
            $sperr = "Invalid Sponsor ID. Member not found";
        } else {
            $sperr = "";
            // Making sure that the sponers do not exceed binary size
            $stmt = $conn->prepare("SELECT SponsorID FROM Users WHERE SponsorID = ?");
            $stmt->bind_param("i", $sponsorID);
            $stmt->execute();
            $response = $stmt->get_result();
            if($response->num_rows >= 2) {
                $sperr = "$sponsorID has reached the limit";
            } else {
                $sperr = "";
            }
            
        }

        // Populating the database table
        if($emerr == "" && $pwerr == "" && $sperr == "" && $dperr =="") {
            // Check if file upload is successful
            if ($uploadOk == 1) {
                move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile);
                $dpURL = $targetFile;
            }

            // Insert user data into the database
            $sql = "INSERT INTO Users (FirstName, MiddleName, LastName, Email, Password, ProfilePicture, VerificationToken, SponsorID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("sssssssi", $firstName, $middleName, $lastName, $email, $hashpass, $dpURL, $verificationToken, $sponsorID);
                
                if ($stmt->execute()) {
                    $msg = "<p style='color: green;' class='btn'>Registration successful.<br> <a href='signin.php'>Click here to login</a></p>";
                    // You can redirect the user to a login page or another location here

                    // Populating the MLMStructure Table
                    $userID = mysqli_insert_id($conn); // Fetching database user ID generated

                    $find = $conn->prepare("SELECT UserID FROM MLMstructure WHERE UserID = ?");
                    $find->bind_param("i", $userID);
                    $find->execute();
                    $res = $find->get_result();

                    if ($res->num_rows > 0) {
                        $mlm = "User already exists";
                    } else {
                        // Insert user data into the database
                        $sql = "INSERT INTO mlmstructure (UserID, SponsorID) VALUES (?, ?)";
                        $stmt = $conn->prepare($sql);
                        
                        if ($stmt) {
                            $stmt->bind_param("ii", $userID, $sponsorID);
                            
                            if ($stmt->execute()) {
                                $stmt = $conn->prepare("SELECT * FROM MLMstructure WHERE UserID = ?");
                                $stmt->bind_param("i", $sponsorID);
                                $stmt->execute();
                                $res = $stmt->get_result();
                                if ($res->num_rows == 1) {
                                    $sponser = $res->fetch_assoc();
                                    $rightLegID = $sponser['RightLegID'];
                                    $leftLegID = $sponser['LeftLegID'];
                                    if($rightLegID == NULL){
                                        $sql = "UPDATE mlmstructure SET RightLegID = $userID WHERE UserID = $sponsorID";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->execute();
                                    } else if ($leftLegID == NULL){
                                        $sql = "UPDATE mlmstructure SET LeftLegID = $userID WHERE UserID = $sponsorID";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->execute();
                                    } else {
                                        $mlm = "Sponsor has exceeded maximum allowed";
                                    }
                                } else {
                                    $mlm = "Sponsor not found";
                                }
                            } else {
                                $mlm = "<p style='color: red;'>Error: " . $stmt->error . "</p>";
                            }

                            // $stmt->close();
                        }
                    }
                } else {
                    $msg = "<p style='color: red;'>Error: " . $stmt->error . "</p>";
                }

                // Populating the Wallet table daatabase
                $walletsql = "INSERT INTO wallets (UserID) VALUES (?)";
                $walletstmt = $conn->prepare($walletsql);
                $walletstmt->bind_param("i", $userID);
                $walletstmt->execute();

                // Making the varibles empty
                $firstName = $middleName = $lastName = $email = $password = $cpassword = $sponsorID = "";
            } else {
                $msg = "<p style='color: red;'>Error: Unable to prepare statement.</p>";
            }

            // Close the database connection
            // $conn->close();
        } else {
            $msg = "<p style='color: red;'>Registration Failed</p>";
        }

        
        $stmt->close();
    }
?>

<div class="container">
    <h2>User Registration</h2>
    <?=$msg."<br/>" ?>
    <?=$mlm."<br/>" ?>
    <?=$dperr ?>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" id="image-file" name="profile_picture" accept="image/x-png, image/jpeg" style="display: none"/>
        <label id="image-label" for="image-file">Upload image</label>
        <script>
            const input_file = document.getElementById('image-file');
            const input_label = document.getElementById('image-label');

            const convert_to_base64 = file => new Promise((resolve) => {
                const file_reader = new FileReader();
                file_reader.readAsDataURL(file);
                file_reader.onload = () => resolve(file_reader.result);
            });

            input_file.addEventListener('change', async function () {
                const file = this.files[0];  // Use 'this.files[0]' to directly access the first file
                const my_image = await convert_to_base64(file);

                // Set background image
                input_label.style.backgroundImage = `url(${my_image})`;

                // Update label text (optional)
                input_label.innerText = `Image: ${file.name}`;
            });
        </script>

        <input type="text" name="firstName" placeholder="FirstName:" required/>

        <input type="text" name="middleName" placeholder="Middle Name"/>

        <input type="text" name="lastName" placeholder="Last Name" required/>

        <input type="email" name="email" placeholder="Email Address" required/>
        <span class="error"><?= $emerr ?></span>

        <input type="password" name="password" placeholder="Password" required/>
        <span class="error"><?= $pwerr ?></span>

        <input type="password" name="cpassword" placeholder="Confirm Password" required/>
        <span class="error"><?= $pwerr ?></span>

        <input type="text" name="sponsorID" placeholder="Sponsor ID" required/>
        <span class="error"><?= $sperr ?></span>

        <input type="Submit" value="Register"/>
    </form>
    <p style='color: black; text-align: center;'>Already have an account: <a href='signin.php'>click here to login</a></p>
</div>

<?php 
    require_once "Resources/footer.php";
?>