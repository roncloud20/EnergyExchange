<?php
    $pagetitle = "Select Delivery Location";
    // Including the dashboards navigation bar
    require_once "Resources/dashboard_nav.php";

    // Include database connection
    require_once "Resources/db_connect.php";

    // Check if session is started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Check if user is logged in
    if(isset($_SESSION['user_id'])) {
        // Fetch user's delivery locations from the DeliveryLocations table
        $userID = $_SESSION['user_id'];
        $sql = "SELECT * FROM DeliveryLocations WHERE UserID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();

        // Display delivery locations
        if ($result->num_rows > 0) {
            ?>
            <div class='delivery-locations'>
                <h2>Select Delivery Location</h2>
                <form action="checkout.php" method="post">
                    <?php
                    while($row = $result->fetch_assoc()) {
                        ?>
                        <div class="location">
                            <label>
                                <input type="radio" name="location_id" value="<?php echo $row['LocationID']; ?>" required>
                                <strong><?php echo $row["CompanyName"]; ?></strong><br>
                                <?php echo $row["Address"] . ", " . $row["City"] . ", " . $row["State"] . ", " . $row["Country"] . ", " . $row["PostalCode"]; ?><br>
                                Contact: <?php echo $row["ContactName"] . " (" . $row["ContactNumber"] . ")"; ?>
                            </label>
                        </div>
                        <?php
                    }
                    ?>
                    <button type="submit">Continue to Checkout</button>
                </form>
            </div>
            <?php
        } else {
            echo "<p>No delivery locations found. Please register a delivery location <a href='delivery_registration.php'>here</a>.</p>";
        }
    } else {
        echo "<p>Please log in to select a delivery location.</p>";
    }

    // Close connection
    $conn->close();
?>
