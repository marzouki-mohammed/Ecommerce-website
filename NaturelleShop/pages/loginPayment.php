<?php 
   session_start();
   



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NaturelleShop</title>
    <link rel="shortcut icon" href="../images/icons/icons.png" type="image/x-icon">
    
   
    
    
    
    <style>
        /* From Uiverse.io by alexruix */ 
            body{
                padding: 0;
                margin: 0;
                display: flex;
                text-align: center;
                justify-content: center;
                align-items: center;
                height: 100vh;
                
            }
            .form-box {
            max-width: 400px;
            background: #f1f7fe;
            overflow: hidden;
            border-radius: 16px;
            color: #010101;
            }

            .form {
            position: relative;
            display: flex;
            flex-direction: column;
            padding: 32px 24px 24px;
            gap: 16px;
            text-align: center;
            }

            /*Form text*/
            .title {
            font-weight: bold;
            font-size: 1.6rem;
            }

            .subtitle {
            font-size: 1rem;
            color: #666;
            }

            /*Inputs box*/
            .form-container {
            overflow: hidden;
            border-radius: 8px;
            background-color: #fff;
            margin: 1rem 0 .5rem;
            width: 100%;
            }

            .input {
            background: none;
            border: 0;
            outline: 0;
            height: 40px;
            width: 90%;
            border-bottom: 1px solid #eee;
            font-size: .9rem;
            padding: 8px 15px;
          
            }

            .form-section {
            padding: 16px;
            font-size: .85rem;
            background-color: #e0ecfb;
            box-shadow: rgb(0 0 0 / 8%) 0 -1px;
            }

            .form-section a {
            font-weight: bold;
            color: #0066ff;
            transition: color .3s ease;
            }

            .form-section a:hover {
            color: #005ce6;
            text-decoration: underline;
            }

            /*Button*/
            .form button {
            background-color: #0066ff;
            color: #fff;
            border: 0;
            border-radius: 24px;
            padding: 10px 16px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color .3s ease;
            }

            .form button:hover {
            background-color: #005ce6;
            }
            .alert{
                padding: 15px;
                margin-bottom: 20px;
                border: 1px solid transparent;
                border-radius: 4px;
                color: #a94442; /* Text color */
                background-color: #f2dede; /* Background color */
                border-color: #ebccd1; /* Border color */
                font-size: 16px;
                font-family: Arial, sans-serif; /* Font styling */
            }
    </style>
</head>
<body>

        <div class="form-box">
                <?php if(isset($_GET['error'])){ ?>
                    <div class="alert" role="alert">
                        <?php echo $_GET['error']; ?>
                    </div>
                <?php } ?>
                <form class="form"  action="./php/lonigprossPermission.php" method="post" enctype="multipart/form-data" >
                    <span class="title">Log in</span>
                    <span class="subtitle">Log in your email.</span>
                    <div class="form-container">                    
                            <input type="email" name="emailfiels" class="input" placeholder="Email" value="exemple@gmail.com">
                            <input type="password" name="passfields" class="input" placeholder="Password" value="">
                    </div>
                    <button type="submit" >Log in</button>
                </form>
        <div class="form-section">   
               <p>Create Account <a href="sinupPayment.php">Sign up</a> </p>     
        </div>
        </div>
</body>
</html>