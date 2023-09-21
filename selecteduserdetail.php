<?php
include 'db_connections.php';

if(isset($_POST['submit']))
{
    $from = $_GET['id'];
    $to = $_POST['to'];
    $amount = $_POST['amount'];

    try {
        // Create a PDO connection
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        
        // Set the PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Retrieve sender's account information
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :from");
        $stmt->bindParam(':from', $from, PDO::PARAM_INT);
        $stmt->execute();
        $sender = $stmt->fetch(PDO::FETCH_ASSOC);

        // Retrieve receiver's account information
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :to");
        $stmt->bindParam(':to', $to, PDO::PARAM_INT);
        $stmt->execute();
        $receiver = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check for negative values
        if ($amount < 0) {
            echo '<script type="text/javascript">';
            echo ' alert("Oops! Negative values cannot be transferred")';
            echo '</script>';
        }
        // Check for insufficient balance
        else if ($amount > $sender['balance']) {
            echo '<script type="text/javascript">';
            echo ' alert("Bad Luck! Insufficient Balance")';
            echo '</script>';
        }
        // Check for zero values
        else if ($amount == 0) {
            echo "<script type='text/javascript'>";
            echo "alert('Oops! Zero value cannot be transferred')";
            echo "</script>";
        }
        else {
            // Deduct amount from sender's account
            $newSenderBalance = $sender['balance'] - $amount;
            $stmt = $pdo->prepare("UPDATE users SET balance = :newSenderBalance WHERE id = :from");
            $stmt->bindParam(':newSenderBalance', $newSenderBalance, PDO::PARAM_INT);
            $stmt->bindParam(':from', $from, PDO::PARAM_INT);
            $stmt->execute();

            // Add amount to receiver's account
            $newReceiverBalance = $receiver['balance'] + $amount;
            $stmt = $pdo->prepare("UPDATE users SET balance = :newReceiverBalance WHERE id = :to");
            $stmt->bindParam(':newReceiverBalance', $newReceiverBalance, PDO::PARAM_INT);
            $stmt->bindParam(':to', $to, PDO::PARAM_INT);
            $stmt->execute();

            // Record the transaction
            $senderName = $sender['name'];
            $receiverName = $receiver['name'];
            $stmt = $pdo->prepare("INSERT INTO transaction (sender, receiver, balance) VALUES (:sender, :receiver, :amount)");
            $stmt->bindParam(':sender', $senderName, PDO::PARAM_STR);
            $stmt->bindParam(':receiver', $receiverName, PDO::PARAM_STR);
            $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);
            $stmt->execute();

            echo "<script> alert('Transaction Successful'); window.location='transactionhistory.php'; </script>";
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        // Close the PDO connection
        $pdo = null;
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/table.css">
    <link rel="stylesheet" type="text/css" href="css/navbar.css">

    <style type="text/css">
    	
		button{
			border:none;
			background: #d9d9d9;
		}
	    button:hover{
			background-color:#777E8B;
			transform: scale(1.1);
			color:white;
		}

    </style>
</head>

<body style="background-color : #E59866 ;">
 
<?php
  include 'navbar.php';
?>

<div class="container">
    <h2 class="text-center pt-4" style="color : black;">Transaction</h2>
    <?php
        include 'db_connections.php';
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $sid = $_GET['id'];
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :sid");
        $stmt->bindParam(':sid', $sid, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>
    <form method="post" name="tcredit" class="tabletext"><br>
        <div>
            <table class="table table-striped table-condensed table-bordered">
                <tr style="color : black;">
                    <th class="text-center">Id</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Balance</th>
                </tr>
                <tr style="color : black;">
                    <td class="py-2"><?php echo $row['id'] ?></td>
                    <td class="py-2"><?php echo $row['name'] ?></td>
                    <td class="py-2"><?php echo $row['email'] ?></td>
                    <td class="py-2"><?php echo $row['balance'] ?></td>
                </tr>
            </table>
        </div>
        <br><br><br>
        <label style="color : black;"><b>Transfer To:</b></label>
        <select name="to" class="form-control" required>
            <option value="" disabled selected>Choose</option>
            <?php
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id != :sid");
                $stmt->bindParam(':sid', $sid, PDO::PARAM_INT);
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
            ?>
                <option class="table" value="<?php echo $row['id']; ?>">
                    <?php echo $row['name']; ?> (Balance: <?php echo $row['balance']; ?>)
                </option>
            <?php 
                } 
            ?>
        </select>
        <br>
        <br>
        <label style="color : black;"><b>Amount:</b></label>
        <input type="number" class="form-control" name="amount" required>
        <br><br>
        <div class="text-center">
            <button class="btn mt-3" name="submit" type="submit" id="myBtn">Transfer</button>
        </div>
    </form>
</div>
<footer class="text-center mt-5 py-2">
    <p>&copy 2021. Made by <b>AYUSH PRAJAPATI</b> <br> Ayush Prajapati Foundation</p>
</footer>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
</body>
</html>
<?php
if (isset($_POST['submit'])) {
    $from = $_GET['id'];
    $to = $_POST['to'];
    $amount = $_POST['amount'];

    // Retrieve sender's and receiver's information
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :from");
    $stmt->bindParam(':from', $from, PDO::PARAM_INT);
    $stmt->execute();
    $sender = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :to");
    $stmt->bindParam(':to', $to, PDO::PARAM_INT);
    $stmt->execute();
    $receiver = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the transaction is valid
    if ($amount < 0) {
        echo '<script type="text/javascript">';
        echo ' alert("Oops! Negative values cannot be transferred")';
        echo '</script>';
    } elseif ($amount > $sender['balance']) {
        echo '<script type="text/javascript">';
        echo ' alert("Bad Luck! Insufficient Balance")';
        echo '</script>';
    } elseif ($amount == 0) {
        echo "<script type='text/javascript'>";
        echo "alert('Oops! Zero value cannot be transferred')";
        echo "</script>";
    } else {
        // Start a database transaction
        $pdo->beginTransaction();

        // Deduct amount from sender's account
        $newSenderBalance = $sender['balance'] - $amount;
        $stmt = $pdo->prepare("UPDATE users SET balance = :newSenderBalance WHERE id = :from");
        $stmt->bindParam(':newSenderBalance', $newSenderBalance, PDO::PARAM_INT);
        $stmt->bindParam(':from', $from, PDO::PARAM_INT);
        $stmt->execute();

        // Add amount to receiver's account
        $newReceiverBalance = $receiver['balance'] + $amount;
        $stmt = $pdo->prepare("UPDATE users SET balance = :newReceiverBalance WHERE id = :to");
        $stmt->bindParam(':newReceiverBalance', $newReceiverBalance, PDO::PARAM_INT);
        $stmt->bindParam(':to', $to, PDO::PARAM_INT);
        $stmt->execute();

        // Record the transaction
        $stmt = $pdo->prepare("INSERT INTO transaction (sender, receiver, balance) VALUES (:sender, :receiver, :balance)");
        $stmt->bindParam(':sender', $sender['name'], PDO::PARAM_STR);
        $stmt->bindParam(':receiver', $receiver['name'], PDO::PARAM_STR);
        $stmt->bindParam(':balance', $amount, PDO::PARAM_INT);
        $stmt->execute();

        // Commit the transaction
        $pdo->commit();

        echo "<script> alert('Transaction Successful'); window.location='transactionhistory.php'; </script>";
    }
}
?>
</body>
</html>