<?php
    $pagetitle = "Payment Cashout";
    // Including the dashboards navigation bar
    require_once "Resources/dashboard_nav.php";


    // Include database connection
    require_once "Resources/db_connect.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["approve"])) {
            // Approve button clicked
            $payoutID = $_POST["payout_id"];

            // Retrieve payout details
            $payout_query = "SELECT * FROM PayoutHistory WHERE PayoutID = ?";
            $stmt = $conn->prepare($payout_query);
            $stmt->bind_param("i", $payoutID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $payout = $result->fetch_assoc();
                $userID = $payout["UserID"];
                $payoutAmount = $payout["PayoutAmount"];

                // Retrieve user's current balance
                $balance_query = "SELECT Balance FROM Wallets WHERE UserID = ?";
                $stmt = $conn->prepare($balance_query);
                $stmt->bind_param("i", $userID);
                $stmt->execute();
                $balance_result = $stmt->get_result();

                if ($balance_result->num_rows == 1) {
                    $wallet = $balance_result->fetch_assoc();
                    $currentBalance = $wallet["Balance"];

                    if ($currentBalance >= $payoutAmount) {
                        // Update user's balance
                        $newBalance = $currentBalance - $payoutAmount;
                        $update_balance_query = "UPDATE Wallets SET Balance = ? WHERE UserID = ?";
                        $stmt = $conn->prepare($update_balance_query);
                        $stmt->bind_param("di", $newBalance, $userID);
                        $stmt->execute();

                        // Update payout history status to approved
                        $update_payout_query = "UPDATE PayoutHistory SET PayoutType = 'approved' WHERE PayoutID = ?";
                        $stmt = $conn->prepare($update_payout_query);
                        $stmt->bind_param("i", $payoutID);
                        $stmt->execute();

                        echo "Payout approved successfully.";
                    } else {
                        echo "Insufficient balance.";
                    }
                } else {
                    echo "Error: User's balance not found.";
                }
            } else {
                echo "Error: Payout record not found.";
            }
        } elseif (isset($_POST["decline"])) {
            // Decline button clicked
            $payoutID = $_POST["payout_id"];

            // Update payout history status to failed
            $update_payout_query = "UPDATE PayoutHistory SET PayoutType = 'failed' WHERE PayoutID = ?";
            $stmt = $conn->prepare($update_payout_query);
            $stmt->bind_param("i", $payoutID);
            $stmt->execute();

            echo "Payout declined successfully.";
        } else {
            echo "Invalid request.";
        }
    } else {
        echo "Invalid request method.";
    }

    // Close connection
    $conn->close();
?>
