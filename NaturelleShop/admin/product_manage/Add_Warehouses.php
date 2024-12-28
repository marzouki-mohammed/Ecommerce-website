<?php 
 session_start();
 
 
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Form</title>
    

   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/styleform.css">
</head>
<body>


    <section class="container">
        <header class="title">ADD</header>
        <?php if(isset($_GET['error'])){ ?>
                    <div class="alert alert-danger" role="alert" >
                        <?php echo $_GET['error']; ?>
                    </div>
        <?php } ?>
        <form action="add_warehouse.php" method="post" class="form">
            <div class="input-box">
                <label for="name">Warehouse Name</label>
                <input type="text" name="name" id="name" placeholder="Enter the warehouse name" required />
            </div>

            <div class="input-box">
                <label for="location">Location</label>
                <input type="text" name="location" id="location" placeholder="Enter the warehouse location" required />
            </div>

            <button type="submit" id="submit_btn" class="submit">Submit</button>
        </form>


    </section>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

       
</body>
</html>
