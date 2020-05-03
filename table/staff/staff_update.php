<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update</title>
    <link rel="stylesheet" href="../../css/update.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
    <script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://kit.fontawesome.com/a81368914c.js"></script>
    <script defer type="text/javascript" src="../../js/main.js"></script>
</head>

<body>

    <?php

    require "../../include/config.php";
    require "../../include/common.php";

    $statement = false;

    try {

        //#1 Open Connection
        $connection = new PDO($dsn, $username, $password, $options);

        //address_id dropdown
        $statement = $connection->prepare("SELECT address_id, address FROM address");
        $statement->execute();
        $address_result = $statement->fetchAll();

        //store_id dropdown
        $statement = $connection->prepare("SELECT store_id FROM store");
        $statement->execute();
        $store_result = $statement->fetchAll();
    } catch (PDOException $error) {

        echo "<br>" . $error->getMessage();
    }

    if (isset($_POST['submit'])) {

        if (count($_FILES) > 0) {
            if (is_uploaded_file($_FILES['picture']['tmp_name'])) {
                echo "imgdata created";
                $imgData = file_get_contents($_FILES['picture']['tmp_name']);
            }
        }

        try {
            $connection = new PDO($dsn, $username, $password, $options);
            $staff = [
                "staff_id"            => $_POST['staff_id'],
                "first_name"          => $_POST['first_name'],
                "last_name"           => $_POST['last_name'],
                "email"               => $_POST['email'],
                "address_id"          => $_POST['address_id'],
                "picture"             => $imgData,
                "store_id"            => $_POST['store_id'],
                "active"              => $_POST['active'],
                "username"            => $_POST['username'],
                "password"            => $_POST['password'],
                "last_update"         => $_POST['last_update']
            ];

            $statement = $connection->prepare(
                "UPDATE staff 
      SET staff_id        = :staff_id,
          first_name      = :first_name,
          last_name       = :last_name,
          email       = :email,
          address_id       = :address_id,
          picture       = :picture,
          store_id       = :store_id,
          active       = :active,
          username       = :username,
          password       = :password,
          last_update     = NOW()
      WHERE staff_id   = :staff_id "
            );

            $statement->execute($staff);
        } catch (PDOException $error) {
            echo "<br>" . $error->getMessage();
        }
    }

    //use $_GET to retrieve information from the URL 
    if (isset($_GET['id'])) {
        try {
            $connection = new PDO($dsn, $username, $password, $options);
            $statement = $connection->prepare("SELECT * FROM staff WHERE staff_id= :staff_id");
            $statement->bindValue(':staff_id', $_GET['id']);
            $statement->execute();

            $result = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            echo "<br>" . $error->getMessage();
        }
        //echo $_GET['actor_id']; 
    } else {
        echo "Something went wrong!";
        exit;
    }
    ?>

    <?php if (isset($_POST['submit']) && $statement) : ?>
        <?php
        header("location: staff.php");
        exit();
        ?>
    <?php endif; ?>

    <form method="post">
        <div class="content">
            <h3 class="title">Update Staff Information</h3>

            <?php
            foreach ($result as $key => $value) :
                if ($key == 'address_id') {
            ?>
                    <select name="address_id" id="address_id">
                        <option value="hide">Staff Address</option>
                        <?php foreach ($address_result as $address) : ?>
                            <option value=<?php echo ($address["address_id"]) ?>><?php echo ("( ID : " . $address['address_id'] . " ) " . $address["address"]) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php
                    continue;
                } elseif ($key == 'store_id') {
                ?>
                    <select name="store_id" id="store_id">
                        <option value="hide">Store</option>
                        <?php foreach ($store_result as $store) : ?>
                            <option value=<?php echo ($store["store_id"]) ?>><?php echo ($store["store_id"]) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php
                    continue;
                } elseif ($key == 'picture') {
                ?>
                    <div class="input-file-container">
                        <input class="input-file" id="my-file" type="file" name="picture" onchange="return fileValidation()">
                        <label tabindex="0" for="my-file" class="input-file-trigger">Put your Selfie here :)</label>
                    </div>
                    <p class="file-return"></p>
                <?php
                    continue;
                } elseif ($key == 'active') {
                ?>
                    <h5 class="active-label">Active?</h5>
                    <div class="toggle">
                        <input type="radio" name="active" id="sizeWeight" <?php echo (($value == '1') ? "checked='checked'" : "") ?> value="1" />
                        <label for="sizeWeight">Yes</label>
                        <input type="radio" name="active" id="sizeDimensions" value="0" <?php echo (($value == '0') ? "checked='checked'" : "") ?> />
                        <label for="sizeDimensions">No</label>
                    </div>
                <?php
                    continue;
                }
                ?>

                <div class="input-div">
                    <div class="i">
                    </div>
                    <div class="div">
                        <h5><?php echo str_replace('_', ' ', ucfirst($key)) ?></h5>
                        <input type="<?php echo (($key == 'password') ? 'password' : 'text'); ?>" name="<?php echo $key; ?>" id="<?php echo $key; ?>" class="input" value="<?php echo escape($value) ?>" <?php echo (($key == 'staff_id' || $key == 'username' || $key == 'password' || $key == 'last_update') ? 'readonly' : '') ?>>
                    </div>
                </div>
            <?php endforeach; ?>

            <input class="btn btn-dark ml-1" type="submit" name="submit" value="Submit" style="margin-bottom: 15px" />
            <a href="actor.php" class="btn-back" style="margin-bottom: 15px">BACK</a>
        </div>
    </form>
</body>

</html>