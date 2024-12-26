<?php 
    session_start();
	
    include "../php/db_connect.php";

	if (!isset($conn)) {
        echo "La connexion à la base de données n'est pas établie.";
        exit;
    }

	if(!isset($_GET['id']) || empty($_GET['id'])){
		echo "This page is not accessible";
		exit;
	}

	$id_order=intval($_GET['id']);

	$sql_orders="SELECT *FROM orders WHERE id=?";
	$stm_orders=$conn->prepare($sql_orders);
	$stm_orders->execute([$id_order]);
	$result_orders=$stm_orders->fetch();

	if(!$result_orders){
		echo "This page is not accessible";
		exit;
	}

	$user_id=$result_orders['user_id'];
	$sql_user="SELECT *FROM users WHERE id=?";
	$stm_user=$conn->prepare($sql_user);
	$stm_user->execute([$user_id]);
	$result_user=$stm_user->fetch();

	if(!$result_user){
		echo "This page is not accessible";
		exit;
	}

	function convertirDateEnEcriture($date) {
		$mois = array(
			1 => 'janvier', 
			2 => 'février', 
			3 => 'mars', 
			4 => 'avril', 
			5 => 'mai', 
			6 => 'juin', 
			7 => 'juillet', 
			8 => 'août', 
			9 => 'septembre', 
			10 => 'octobre', 
			11 => 'novembre', 
			12 => 'décembre'
		);
		
		$dateObj = new DateTime($date);
		$jour = $dateObj->format('j');
		$moisNum = (int)$dateObj->format('n');
		$annee = $dateObj->format('Y');
	  
		$jourEcrit = ($jour == 1) ? '1er' : $jour;
	  
		$dateEcriture = $jourEcrit . ' ' . $mois[$moisNum] . ' ' . $annee;
	  
		return $dateEcriture;
	}

	$date=convertirDateEnEcriture($result_orders['updated_at']);


	$sql_status="SELECT * FROM order_statuses WHERE orders_id=?";
	$stm_status=$conn->prepare($sql_status);
	$stm_status->execute([$id_order]);
	$result_status=$stm_status->fetch();

	if(!$result_status){
		echo "This page is not accessible";
	    exit;
	}
  
	 $sql_ordes_status="SELECT * FROM statuses WHERE id=?";
	 $stm_orders_status=$conn->prepare($sql_ordes_status);
	 $stm_orders_status->execute([intval($result_status['status_id'])]);
	 $res=$stm_orders_status->fetch();

	 if(!$res){
		echo "This page is not accessible";
	    exit;
	 }

	 if($res['status_name'] == 'Canceled'){
		echo "This page is not accessible";
	    exit;
	 }



?>


<!DOCTYPE html>
<html lang="en">
<head>

 
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>NaturelleShop</title>
    <link rel="shortcut icon" href="../images/icons/icons.png" type="image/x-icon">
	       <!-- Font-awesome CSS -->
		   <link rel="stylesheet" href=
		   "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
					 integrity=
		   "sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
					 crossorigin="anonymous" 
					 referrerpolicy="no-referrer" />
		   
			   <!-- Bootstrap CSS -->
			   <link rel="stylesheet" href=
		   "https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" 
					 integrity=
		   "sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" 
					 crossorigin="anonymous">
					 <link rel="stylesheet" href="assets/css/style_orders.css" />
		
</head>
<body>
	<div class="card">
        <div class="title">
		  <a href="../../index.php"  class="title">
            NaturelleShop
          </a>
		</div>


        <div class="info">
            <div class="row">
                <div class="col-7">
                    <span id="heading">Date</span><br>
                    <span id="details"><?php echo $date ;?></span>
                </div>
                <div class="col-5 pull-right">
                    <span id="heading">Order No.</span><br>
                    <span id="details">
						<?php echo $result_user['name'] . " " . $id_order; ?>
					</span>
                </div>
            </div>      
        </div>   
      
        <div class="tracking">
            <div class="title">Tracking Order</div>
        </div>


        <div class="progress-track">
            <ul id="progressbar">
                <?php 
				  if($res['id']==1){
                      echo '<li class="step0 active" id="step1">Pending</li>
							<li class="step0" id="step2">Processing</li>
							<li class="step0  text-center" id="step3">Shipped</li>
							<li class="step0  text-right" id="step4">Delivered</li>
							<li class="step0 text-right" id="step5">Canceled</li>';


				  }elseif($res['id']==2){
					 echo '<li class="step0 active" id="step1">Pending</li>
							<li class="step0 active" id="step2">Processing</li>
							<li class="step0  text-center" id="step3">Shipped</li>
							<li class="step0  text-right" id="step4">Delivered</li>
							<li class="step0 text-right" id="step5">Canceled</li>';

				  }elseif($res['id']==3){
					 echo '<li class="step0 active" id="step1">Pending</li>
							<li class="step0 active" id="step2">Processing</li>
							<li class="step0  text-center active" id="step3">Shipped</li>
							<li class="step0  text-right" id="step4">Delivered</li>
							<li class="step0 text-right" id="step5">Canceled</li>';


				  }elseif($res['id']==4){
					echo '<li class="step0 active" id="step1">Pending</li>
							<li class="step0 active" id="step2">Processing</li>
							<li class="step0  text-center active" id="step3">Shipped</li>
							<li class="step0  text-right active" id="step4">Delivered</li>
							<li class="step0 text-right" id="step5">Canceled</li>';

				  }else{
					echo '<li class="step0" id="step1 active">Pending</li>
							<li class="step0" id="step2 active">Processing</li>
							<li class="step0  text-center active" id="step3">Shipped</li>
							<li class="step0  text-right active" id="step4">Delivered</li>
							<li class="step0 text-right active" id="step5">Canceled</li>';
				  }
				?>
            </ul>
        </div>


        <div class="footer">
            <div class="row">
                <div class="col-2"><img class="img-fluid" src="../images/icons/icons.png"></div>
				<div class="col-10">Want any help? Please &nbsp;<a href="mailto:naturelleshop.boutique@gmail.com">contact us</a></div>
            </div>        
        </div>

    </div>
</body>
</html>