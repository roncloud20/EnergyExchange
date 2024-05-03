<?php
    $pagetitle = "Checkout";
    // Including the dashboards navigation bar
    require_once "Resources/dashboard_nav.php";

    // Include database connection
    require_once "Resources/db_connect.php";
?>
<div class="container">
    <h2>Delivery Registration</h2>
    <form action="" method="POST">
        <input type="text" id="company_name" name="company_name" placeholder="Company's Name: (Eg.EnergyXchange LLC)" required><br><br>

        <input type="text" id="address" name="address" placeholder="Delivery Location" required><br><br>

        <input type="text" id="city" name="city" placeholder="Delivery City" required><br><br>

        <input type="text" id="state" name="state" placeholder="Delivery State" required><br><br>

        <input type="text" id="country" name="country" placeholder="Delivery Country" required><br><br>

        <input type="text" id="postal_code" name="postal_code" placeholder="Postal Code: (Eg. 100001)" required><br><br>

        <input type="text" id="contact_name" name="contact_name" placeholder="Delivery Contact Name (Eg. Mr John Doe)" required><br><br>

        <input type="number" id="contact_number" name="contact_number" placeholder="Delivery Contact Phone Number" required><br><br>

        <input type="submit" value="Register Delivery Location">
    </form>
</div>

<?php
    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $userID = $_SESSION['user_id']; // Assuming the user is logged in
        $companyName = $_POST["company_name"];
        $address = $_POST["address"];
        $city = $_POST["city"];
        $state = $_POST["state"];
        $country = $_POST["country"];
        $postalCode = $_POST["postal_code"];
        $contactName = $_POST["contact_name"];
        $contactNumber = $_POST["contact_number"];

        // Prepare and bind SQL statement to insert data into DeliveryLocations table
        $sql = "INSERT INTO DeliveryLocations (UserID, CompanyName, Address, City, State, Country, PostalCode, ContactName, ContactNumber) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssssss", $userID, $companyName, $address, $city, $state, $country, $postalCode, $contactName, $contactNumber);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Delivery location registered successfully.";
            header("Location: checkout.php");
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    } else {
        echo "Invalid request";
    }

    // Close connection
    $conn->close();
?>