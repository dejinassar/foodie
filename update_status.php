<?php
include("connection/connect.php");
error_reporting(0);

// Check if database connection is valid
if (!$db) {
    die("Database connection failed");
}

// Check if the order_id is passed
if (isset($_POST['order_id']) && is_numeric($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);  // Ensure order_id is an integer

    // Fetch the current order status securely
    $query = $db->prepare("SELECT status FROM users_orders WHERE o_id = ?");
    $query->bind_param("i", $order_id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_status = strtolower(trim($row['status'])); // Normalize status comparison

        // Status transitions
        $status_map = [
            "dispatch" => "On the way",
            "on the way" => "Delivered",
            "delivered" => "Delivered"  // No update once delivered
        ];

        // Determine next status (default to "Dispatch" if status is invalid)
        $new_status = $status_map[$current_status] ?? "Dispatch";

        // Only update if the status is changing
        if ($current_status !== "delivered") {
            $update_query = $db->prepare("UPDATE users_orders SET status = ?, status_updated_at = NOW() WHERE o_id = ?");
            $update_query->bind_param("si", $new_status, $order_id);

            if ($update_query->execute()) {
                echo ucfirst($new_status); // Return formatted status
            } else {
                echo "Error updating status";
            }
        } else {
            echo "Delivered"; // If already delivered, return as is
        }
    } else {
        echo "Order not found";
    }
} else {
    echo "Invalid order ID";
}
?>