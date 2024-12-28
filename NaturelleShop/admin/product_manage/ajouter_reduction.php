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
        <header class="title">ADD Reduction</header>
        <?php if(isset($_GET['error'])){ ?>
                    <div class="alert alert-danger" role="alert" >
                        <?php echo $_GET['error']; ?>
                    </div>
        <?php } ?>
        <?php if(isset($_GET['success'])){ ?>
                    <div class="alert alert-success" role="alert" >
                        <?php echo $_GET['success']; ?>
                    </div>
        <?php } ?>
        <form action="add_coupon.php" method="post" class="form">
            <div class="input-box">
                <label for="code_filde">Coupon Code</label>
                <input type="text" name="code_filde" id="code_filde" placeholder="Enter the coupon code" required />
            </div>

            <div class="input-box">
                <label for="discount_amount_filde">Discount Amount</label>
                <input type="number" name="discount_amount_filde" id="discount_amount_filde" min="0" step="0.01" placeholder="Enter the discount amount" required />
            </div>

            <div class="input-box">
                <label for="valid_from_filde">Valid From</label>
                <input type="datetime-local" name="valid_from_filde" id="valid_from_filde" required />
            </div>

            <div class="input-box">
                <label for="valid_until_filde">Valid Until</label>
                <input type="datetime-local" name="valid_until_filde" id="valid_until_filde" required />
            </div>

            <button type="submit" id="submit_btn_coupon" class="submit">Submit</button>
        </form>


    </section>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

       
</body>
</html>
