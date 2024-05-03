<?php
    $pagetitle = "Successfully Payment";
    require_once "Resources/dashboard_nav.php";

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Retrieve user ID, total amount, and location ID from session
    $userID = $_SESSION['user_id'];
    $locationID = $_SESSION['location_id'];
    $totalAmount = $_SESSION['total_amount'];
    $totalProfit = $_SESSION['total_profit'];
    $uniqueID = $_SESSION['unique_id'];
    // $uniqueID = "$userID" . substr(abs(crc32(uniqid())), -8);
    $orderID = $userID;

    // Insert new order
    $insertOrderSql = "INSERT INTO Orders (UserID, TotalAmount, TotalProfit, UniqueOrderID, LocationID) VALUES (?, ?, ?, ?, ?)";
    $insertOrderStmt = $conn->prepare($insertOrderSql);
    $insertOrderStmt->bind_param("iddii", $userID, $totalAmount, $totalProfit, $uniqueID, $locationID);
    $insertOrderStmt->execute();
    $orderID = $insertOrderStmt->insert_id;

    // Define commission rates
    $commissionRates = [
        'seller' => 0.10, // 10% to the seller
        'direct_sponsor' => 0.05, // 5% to the direct sponsor
        'grand_sponsor' => 0.03, // 3% to the grand sponsor
        'above_grand' => 0.02, // 2% to sponsors above the grand sponsor (shared equally)
    ];

    // Function to insert commission record into database
    function distributeCommission($conn, $userID, $orderID, $amount, $commissionType) {
        if ($userID != null) {
            $insertCommissionSql = "INSERT INTO Commissions (UserID, OrderID, CommissionType, Amount) VALUES (?, ?, ?, ?)";
            $insertCommissionStmt = $conn->prepare($insertCommissionSql);
            $insertCommissionStmt->bind_param("iisd", $userID, $orderID, $commissionType, $amount);
            $insertCommissionStmt->execute();
        }
    }

    // Function to calculate and distribute commissions
    function distributeCommissions($conn, $userID, $orderID, $totalProfit, $commissionRates) {
        $userIDs = [$userID]; // Start with the seller
        $currentUser = $userID;

        // Retrieve upline users
        while ($currentUser) {
            $getSponsorSql = "SELECT SponsorID FROM MLMStructure WHERE UserID = ?";
            $getSponsorStmt = $conn->prepare($getSponsorSql);
            $getSponsorStmt->bind_param("i", $currentUser);
            $getSponsorStmt->execute();
            $getSponsorResult = $getSponsorStmt->get_result();

            if ($getSponsorResult->num_rows > 0) {
                $sponsorRow = $getSponsorResult->fetch_assoc();
                $currentUser = $sponsorRow['SponsorID'];
                if ($currentUser != null) {
                    $userIDs[] = $currentUser; // Add the sponsor ID to the array for commission distribution
                }
            } else {
                // If there is no sponsor, exit the loop
                break;
            }
        }

        // Calculate and distribute commissions
        // Seller's commission
        $sellerCommission = $totalProfit * $commissionRates['seller'];
        distributeCommission($conn, $userID, $orderID, $sellerCommission, 'personal_sales');

        // Direct sponsor's commission
        if (isset($userIDs[1])) {
            $directSponsorCommission = $totalProfit * $commissionRates['direct_sponsor'];
            distributeCommission($conn, $userIDs[1], $orderID, $directSponsorCommission, 'override_bonus');
        }
        
        // Grand sponsor's commission
        if (isset($userIDs[2])) {
            $grandSponsorCommission = $totalProfit * $commissionRates['grand_sponsor'];
            distributeCommission($conn, $userIDs[2], $orderID, $grandSponsorCommission, 'override_bonus');
        }
        
        // Above grand sponsors' commission
        if (count($userIDs) > 3) {
            $aboveGrandSponsorCount = count($userIDs) - 3;
            $aboveGrandSponsorCommissionTotal = $totalProfit * $commissionRates['above_grand'];
            $aboveGrandSponsorCommission = $aboveGrandSponsorCommissionTotal / $aboveGrandSponsorCount;

            for ($i = 3; $i < count($userIDs); $i++) {
                distributeCommission($conn, $userIDs[$i], $orderID, $aboveGrandSponsorCommission, 'override_bonus');
            }
        }
    }

    // Execute commission distribution
    distributeCommissions($conn, $userID, $orderID, $totalProfit, $commissionRates);

    // Update the orderStatus in OrderDetails table to 'Complete'
    $updateStatusSql = "UPDATE OrderDetails SET orderStatus = 'Complete', UniqueOrderID = ?  WHERE OrderID = ?";
    $updateStatusStmt = $conn->prepare($updateStatusSql);
    $updateStatusStmt->bind_param("ii", $uniqueID, $userID);
    $updateStatusStmt->execute();

    echo "Payment was successfully processed.";
    echo "<br> $uniqueID";
    unset($_SESSION['unique_id']);

    $conn->close(); // Close the connection at the end of the script
?>

