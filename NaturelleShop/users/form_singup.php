
<?php  
session_start();
 ?>
<!DOCTYPE html>
<html lang="en">
<head>    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NaturelleShop</title>
  
    <!-- favicon -->
    <link rel="shortcut icon" href="../images/icons/icons.png" type="image/x-icon">

    <!-- Montserrat Font -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    <!-- Lien vers le fichier CSS externe -->
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <section class="container">
        <header class="title">Register</header>
        <?php if(isset($_GET['error'])){ ?>
            <div class="alert" role="alert">
                <?php echo $_GET['error']; ?>
            </div>
        <?php } ?>
        <form id="signup" action="../php/sing_up.php" method="post" enctype="multipart/form-data" class="form">
            
            <div class="column">
                <div class="input-box">
                    <label>First Name</label>
                    <input type="text" name="first_name_filde" id="first_name" placeholder="Enter first name" value="" />
                </div>

                <div class="input-box">
                    <label>Last Name</label>
                    <input type="text" name="last_name_filde" id="last_name" placeholder="Enter last name" value="" />
                </div>
            </div>

            <div class="input-box">
                <label>Phone Number</label>
                <input type="tel" name="phone_number_filde" id="phone_number" placeholder="Enter your phone number" value="" />
            </div>

            <div class="input-box">
                <label>Email</label>
                <input type="email" id="email_filde" name="email_filde" placeholder="Email Address" value="exemple@gmail.com" />
            </div>

            <div class="gender-box">
                <h3>Gender</h3>
                <div class="gender-option">
                    <div class="gender">
                        <input type="radio" id="check-male" name="sex_filde" value="Male" checked />
                        <label for="check-male">Male</label>
                    </div>
                    <div class="gender">
                        <input type="radio" id="check-female" name="sex_filde" value="Female" />
                        <label for="check-female">Female</label>
                    </div>
                    <div class="gender">
                        <input type="radio" id="check-other" name="sex_filde" value="Other" />
                        <label for="check-other">Other</label>
                    </div>
                </div>
            </div>

            <div class="input-box">
                <label>Password</label>
                <input type="password" id="password_hash_filde" name="password_hash_filde" placeholder="Enter your password" value="" />
            </div>

            <div class="field" style="border: none;">
                <input type="file" id="image" name="image_filde">
                <label for="image">Choose a photo
                    <ion-icon name="camera-outline"></ion-icon>
                </label>
            </div>

            <button type="submit" id="submit_btn" class="submit">Submit</button>
            <p class="signin">Already have an account? <a href="../../index.php">Signin</a></p>
        </form>
    </section>

    <!-- Ionicon link -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
