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
        <form id="signup" action="add_Manage/pross.php" method="post" enctype="multipart/form-data" class="form">
            <div class="gender-box">
                <h3>Type</h3>
                <div class="gender-option">
                    <div class="gender">
                        <input type="radio" id="check-simple" name="type_filde" value="simple" checked />
                        <label for="check-simple">Simple</label>
                    </div>
                    <div class="gender">
                        <input type="radio" id="check-composer" name="type_filde" value="composer" />
                        <label for="check-composer">Composer</label>
                    </div>
                </div>
            </div>
            
            <div class="input-box">
                <label for="title_filde">Title</label>
                <input type="text" name="title_filde" id="title_filde" placeholder="Enter the title" required />
            </div>

            <div class="input-box">
                <label for="desc_filde">Description</label>
                <textarea id="desc_filde" name="desc_filde" placeholder="Enter the description" rows="100" required></textarea>
            </div>
            
            <div class="column">
                <div class="input-box">
                    <label for="price_filde">Price</label>
                    <input type="number" name="price_filde" id="price_filde" min="0" step="0.01" placeholder="Enter the price" required />
                </div>

                <div class="input-box">
                    <label for="quentiter_filde">Quantity</label>
                    <input type="number" name="quentiter_filde" id="quentiter_filde" min="0" placeholder="Enter the quantity" required />
                </div>
            </div>

            <div class="input-box">
                <label for="active_pro">Actif</label>
                <select name="active_pro" id="active_pro">
                    <option value="1">Oui</option>
                    <option value="0">Non</option>
                </select>
            </div>

            <button type="submit" id="submit_btn" class="submit">Submit</button>
        </form>

    </section>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

       
</body>
</html>
