<style>
    .purchase {
        width: fit-content;
        background-color: #273a89;
        color: white;
        padding: 14px 20px;
        margin: 8px 0;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        display: block;
}
</style>
<?php
    $pagetitle = "Inventory";
    // Including the dashboards navigation bar
    require_once "Resources/dashboard_nav.php";

    // Include database connection
    require_once "Resources/db_connect.php";

    // Query to fetch all products
    $sql = "SELECT * FROM Products";
    $result = $conn->query($sql);

    // Display products
    echo "<div class='productItems'>";
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<div class='card'>";
            echo "<img src='" . $row["ProductPicture"] . "' alt='" . $row["ProductName"] . "'><br>";
            echo "<div class='containerNew'>";
            echo "<h4>" . $row["ProductName"] . "</h4>";
            echo "<p>Description: " . $row["Description"] . "</p>";
            echo "<p>Quantity: " . number_format($row["QuantityBalance"], 2, '.', ',') . " " . $row["UnitOfMeasure"] . "</p>";
           
            echo "<p>Selling Price: $" . number_format($row["SellingPrice"], 2, '.', ',') . "</p>";

            echo "<a href='view_product.php?id=" . $row["ProductID"] . "' class='purchase'>Purchase</a>";

            // echo "<button onclick='purchase(" . $row["ProductID"] . ")'>Purchase</button>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "No products available";
    }
    echo "</div>";

    // Close connection
    $conn->close();

    

?>