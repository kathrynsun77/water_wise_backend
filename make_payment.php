<?php
// $servername = "localhost";
//$servername = "sql12.freesqldatabase.com";
//$username = "sql12628993";
//$password = "vYNV8FFHMG";
//$database = "sql12628993";

$servername = "localhost";
$username = "root";
$password = "";
$database = "water_wise";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$idUser = $_POST['cust-id'];
$amount = intval($_POST['amount']);
$usage = intval($_POST['usage']);
$idInt=intval($idUser);
$invoice = $_POST['invoice'];
$payment_type=$_POST['payment-id'];
$mysqltime = date('Y-m-d H:i:s');
$newCreditValue = intval(($amount/2) * 0.1);

// Query to validate the user's credentials
$sql = "INSERT INTO transaction (customer_id,transaction_type,transaction_date,transaction_amount,payment_type,usage_amount,invoice_number) 
            VALUES ($idInt,1,'".date("Y-m-d H:i:s")."',$amount,'$payment_type',$usage,'$invoice')";

$result = $conn->query($sql);

$sql3 = "UPDATE customer_bill SET bill_status=1 WHERE inovice_number='$invoice'";
$conn->query($sql3);

$sql2="SELECT * FROM transaction WHERE customer_id=$idInt";
$res= $conn->query($sql2);

$sql5 = "UPDATE pipe
SET meter_value = REPLACE(meter_value, '|', '0')
WHERE customer_id = $idInt";

$conn->query($sql5);


// Check if the row with customer ID exists
$sqlCheck = "SELECT customer_id FROM points WHERE customer_id = $idInt";
$resultCheck = $conn->query($sqlCheck);

if ($resultCheck->num_rows > 0) {
    // Row exists, so run the update statement
    $sql = "UPDATE points SET total_point = total_point + $newCreditValue WHERE customer_id = $idInt";
    $conn->query($sql);
} else {
    // Row doesn't exist, so run the insert statement
    $sql = "INSERT INTO points (total_point, customer_id) VALUES ($newCreditValue, $idInt)";
    $conn->query($sql);
}


// Check if the query returned any rows
if ($res->num_rows > 0) {
    $data = array();
    while ($getData = $res->fetch_assoc()) {
        $data[] = $getData;
    }
    // Login successful
    echo json_encode(array(
        "message"=>"Success",
        "data"=>$data[0],
    ));
} else {
    // Login failed
    echo json_encode(array(
        "message"=>"Failed",
    ));
}
$conn->close();
?>
