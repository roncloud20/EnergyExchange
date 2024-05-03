<?php
    $pagetitle = "View Product";
    // Including the dashboards navigation bar
    require_once "Resources/dashboard_nav.php";

    // Include database connection
    require_once "Resources/db_connect.php";

    // Check if product ID is provided in the URL
    if(isset($_GET['id'])) {
        $productId = $_GET['id'];

        // Query to fetch product details
        $sql = "SELECT * FROM Products WHERE ProductID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        // Display product details
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            echo "<div class='product-details' style='text-align: center'>";
            echo "<img src='" . $row["ProductPicture"] . "' alt='" . $row["ProductName"] . "'><br>";
            echo "<h2>" . $row["ProductName"] . "</h2>";
            echo "<p>Description: " . $row["Description"] . "</p>";
            echo "<p>Quantity: " . number_format($row["QuantityBalance"], 2, '.', ',') . " " . $row["UnitOfMeasure"] . "</p>";
            echo "<p>Selling Price: $" . number_format($row["SellingPrice"], 2, '.', ',') . "</p>";

            // Form to enter quantity and purchase
            echo "<form action='add_to_cart.php' method='post'>";
            echo "<input type='hidden' name='product_id' value='" . $row["ProductID"] . "'>";
            echo "<label for='quantity'>Quantity:</label>";
            echo "<input type='number' id='quantity' name='quantity' min='1' value='1' max='" . $row["QuantityBalance"] . "' required>";
            echo "<button type='submit'>Add to Cart</button>";
            echo "</form>";

            echo "</div>";
        } else {
            echo "Product not found";
        }
    } else {
        echo "Product ID not provided";
    }

    // Close connection
    $conn->close();

?>
