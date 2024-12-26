<?php 
   session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>NaturelleShop</title>
    <link rel="shortcut icon" href="../images/icons/icons.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/stylesSinup.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
  </head>
  <body>
    <div class="container">
      <div class="form-wrapper">
        <div class="banner">
          <h1>Hello, Friend!</h1>
          <p>Enter your personal details and start journey with us</p>
        </div>
        <div class="green-bg">
          <button type="button">Sign Up</button>
        </div>
        <form class="signup-form" action="./php/singup.php" method="post" enctype="multipart/form-data" >
          <h1>Create Account</h1>
          
          <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" name="name_filde" placeholder="Name" />
          </div>

          <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email_filde" placeholder="Email" />
          </div>

          <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password_hash_filde" placeholder="Password" />
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
            </div>
          </div>
          <button type="submit">Sign Up</button>
        </form>
      </div>
    </div>

    <script src="assets/js/scriptsinup.js"></script>
  </body>
</html>