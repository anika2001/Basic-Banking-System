<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/table.css">
    <link rel="stylesheet" type="text/css" href="css/navbar.css">
</head>

<<body style="background-color : #ADD8E6;">

<?php
  include 'navbar.php';
?>

<div class="container">
    <h2 class="text-center pt-4" style="color : black;">Transaction History</h2>
    <br>
    <div class="table-responsive-sm">
        <table class="table table-hover table-striped table-condensed table-bordered">
            <thead style="color : black;">
                <tr>
                    <th class="text-center">S.No.</th>
                    <th class="text-center">Sender</th>
                    <th class="text-center">Receiver</th>
                    <th class="text-center">Amount</th>
                    <th class="text-center">Date & Time</th>
                </tr>
            </thead>
            <tbody>
            <?php
            try {
                include 'db_connections.php';

                // Create a PDO connection
                $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

                // Set the PDO error mode to exception
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Prepare and execute the SELECT query for the "transaction" table
                $stmt = $pdo->prepare("SELECT * FROM transaction");
                $stmt->execute();

                // Fetch the results as an associative array
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($result as $row) {
            ?>
                    <tr style="color : black;">
                        <td class="py-2"><?php echo $row['sno']; ?></td>
                        <td class="py-2"><?php echo $row['sender']; ?></td>
                        <td class="py-2"><?php echo $row['receiver']; ?></td>
                        <td class="py-2"><?php echo $row['balance']; ?> </td>
                        <td class="py-2"><?php echo $row['datetime']; ?> </td>
                    </tr>
            <?php
                }
            } catch (PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<footer class="text-center mt-5 py-2">
    <p><br>CREATED BY ANIKA PARVEEN</p>
</footer>

</body>
</html>