<?php 
session_start();
$existe = '';
$message = '';

if (isset($_SESSION['user_id']) && isset($_SESSION['admin_username'])) {
  $message = 'success';
  $admin_id = $_SESSION['user_id'];
  $admin_name=$_SESSION['admin_username'];
  include "../../php/db_connect.php";

  if (!isset($conn)) {
    echo "Database connection is not set.";
    exit;
  }
  if($admin_name==='root'){
    $existe = 'existe';

  }else{

  $sql = "SELECT role.id, role.name
          FROM role
          INNER JOIN admin_role ON role.id = admin_role.role_id
          WHERE admin_role.admin_id = :admin_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute(['admin_id' => $admin_id]);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
 

  if (count($rows) > 0) {
    foreach ($rows as $row) {
      if ($row["name"] === 'gestion des orders' ) {
        $existe = 'existe';
        break;
      }
    }
  }
}
  
} else {
  $message = 'erreur';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
<style>
        .status {
          padding: .5rem 0;
          border-radius: 2rem;
          text-align: center;
          width: 90px;
          color :#141414;
        }

        .status.delivered {
            background-color: #86e49d;
            
        }

        .status.cancelled {
            background-color: #d893a3;
            
        }

        .status.pending {
            background-color: #11ff11;
            
        }
        .status.processing {
            background-color: #ebc474;
        }

        .status.shipped {
            background-color: #6fcaea;
        }
        .status.shipped {
            background-color: #6fcaea;
        }

  </style>
  <meta charset="utf-8" />
  <link rel="shortcut icon" href="../../images/icons/icons.png" type="image/x-icon">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>NaturelleShop ADMINS</title>

  

  <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />

  <!-- Canonical SEO -->
  <link rel="canonical" href="https://www.creative-tim.com/product/fresh-bootstrap-table"/>

  <!--  Social tags    -->
  <meta name="keywords" content="creative tim, html table, html css table, web table, freebie, free bootstrap table, bootstrap, css3 table, bootstrap table, fresh bootstrap table, frontend, modern table, responsive bootstrap table, responsive bootstrap table">

  <meta name="description" content="Probably the most beautiful and complex bootstrap table you've ever seen on the internet, this bootstrap table is one of the essential plugins you will need.">

  <!-- Schema.org markup for Google+ -->
  <meta itemprop="name" content="Fresh Bootstrap Table by Creative Tim">
  <meta itemprop="description" content="Probably the most beautiful and complex bootstrap table you've ever seen on the internet, this bootstrap table is one of the essential plugins you will need.">

  <meta itemprop="image" content="http://s3.amazonaws.com/creativetim_bucket/products/31/original/opt_fbt_thumbnail.jpg">
  <!-- Twitter Card data -->

  <meta name="twitter:card" content="product">
  <meta name="twitter:site" content="@creativetim">
  <meta name="twitter:title" content="Fresh Bootstrap Table by Creative Tim">

  <meta name="twitter:description" content="Probably the most beautiful and complex bootstrap table you've ever seen on the internet, this bootstrap table is one of the essential plugins you will need.">
  <meta name="twitter:creator" content="@creativetim">
  <meta name="twitter:image" content="http://s3.amazonaws.com/creativetim_bucket/products/31/original/opt_fbt_thumbnail.jpg">
  <meta name="twitter:data1" content="Fresh Bootstrap Table by Creative Tim">
  <meta name="twitter:label1" content="Product Type">
  <meta name="twitter:data2" content="Free">
  <meta name="twitter:label2" content="Price">

  <!-- Open Graph data -->
  <meta property="og:title" content="Fresh Bootstrap Table by Creative Tim" />
  <meta property="og:type" content="article" />
  <meta property="og:url" content="https://wenzhixin.github.io/fresh-bootstrap-table/compact-table.html" />
  <meta property="og:image" content="http://s3.amazonaws.com/creativetim_bucket/products/31/original/opt_fbt_thumbnail.jpg"/>
  <meta property="og:description" content="Probably the most beautiful and complex bootstrap table you've ever seen on the internet, this bootstrap table is one of the essential plugins you will need." />
  <meta property="og:site_name" content="Creative Tim" />


  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css">
  <link href="assets/css/fresh-bootstrap-table.css" rel="stylesheet" />
  <link href="assets/css/demo.css" rel="stylesheet" />

  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
  <link href="http://fonts.googleapis.com/css?family=Roboto:400,700,300" rel="stylesheet" type="text/css">

  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="https://unpkg.com/bootstrap-table/dist/bootstrap-table.min.js"></script>

  <!--  Just for demo purpose, do not include in your project   -->
  <script src="assets/js/demo/gsdk-switch.js"></script>
  <script src="assets/js/demo/jquery.sharrre.js"></script>
  <script src="assets/js/demo/demo.js"></script>

</head>
<body>
<?php if ($existe != ''): ?>
    <div class="wrapper">
      <!--   Creative Tim Branding   -->
      <a href="../../../index.php">
        <div class="logo-container">
          <div class="logo">
            <svg width="145"  height="60" xmlns="http://www.w3.org/2000/svg">
                            
                <circle cx="25" cy="30" r="20" stroke="black" stroke-width="3" fill="lightgreen" />
                
                <text x="25" y="37" font-size="20" font-family="Arial" text-anchor="middle" fill="white">N</text>
                
              
            </svg> 
          </div>
          <div class="brand">
          
                <text x="50" y="37" font-size="15" font-family="Arial" style="color:green;" fill="green">Naturelle</text>
                <text x="110" y="37" font-size="15" font-family="Arial" style="color:darkgreen;" fill="darkgreen">Shop</text>
        

          </div>
        </div>
      </a>

      <div class="container">
        <div class="row">
          <div class="col-md-8 col-md-offset-2">
            <div class="description">
              <h2>Order Management</h2>
            </div>

            <div class="fresh-table full-color-orange">
            <!--
              Available colors for the full background: full-color-blue, full-color-azure, full-color-green, full-color-red, full-color-orange
              Available colors only for the toolbar: toolbar-color-blue, toolbar-color-azure, toolbar-color-green, toolbar-color-red, toolbar-color-orange
            -->
              <div class="toolbar">
                <button id="alertBtn" class="btn btn-default">Alert</button>
              </div>

              <table id="fresh-table" class="table">
                <thead>
                  <th data-field="id">User ID</th>
                  <th data-field="name" data-sortable="true">Name</th>
                  <th data-field="id_orders" data-sortable="true">Order ID</th>
                  <th data-field="salary" data-sortable="true">Salary</th>
                  <th data-field="address" data-sortable="true">Address</th>
                  <th data-field="country" data-sortable="true">Country</th>
                  <th data-field="city">City</th>
                  <th data-field="actions" >Actions</th>
                </thead>
                <tbody>
                  <?php 
                       $sql_ordes="SELECT * FROM orders  
                                   ORDER BY created_at DESC";
                       $stm_orders=$conn->prepare($sql_ordes);
                       $stm_orders->execute();
                       $result_orders=$stm_orders->fetchAll(PDO::FETCH_ASSOC);
                       if(count($result_orders) > 0){
                          foreach($result_orders as $row){
                            echo "<tr>";
                            echo "<td>".$row['user_id']."</td>";
                            $sql_users="SELECT * FROM users WHERE id=?";
                            $stm_users=$conn->prepare($sql_users);
                            $stm_users->execute([intval($row['user_id'])]);
                            $resulte_users=$stm_users->fetch();
                            if($resulte_users){
                              echo "<td>".htmlspecialchars($resulte_users['name'])."</td>";                           
                            }else{
                              echo "<td></td>";
                            }
                            echo "<td>".$row['id']."</td>";
                            echo "<td>".$row['price']."</td>";
                            $sql_address="SELECT * FROM user_address WHERE id=?";
                            $stm_assress=$conn->prepare($sql_address);
                            $stm_assress->execute([intval($row['address_id'])]);
                            $resulte_address=$stm_assress->fetch();
                            if($resulte_address){
                              echo "<td>".$resulte_address['address_line1']."</td>";
                              echo "<td>".$resulte_address['country']."</td>";
                              echo "<td>".$resulte_address['city']."</td>";                              
                            }else{
                              echo "<td></td>";
                              echo "<td></td>";
                              echo "<td></td>";
                            } 
                           
                            $sql_status="SELECT * FROM order_statuses WHERE orders_id=?";
                            $stm_status=$conn->prepare($sql_status);
                            $stm_status->execute([intval($row['id'])]);
                            $result_status=$stm_status->fetch();
                            if($result_status){
                              $sql_ordes_status="SELECT * FROM statuses WHERE id=?";
                              $stm_orders_status=$conn->prepare($sql_ordes_status);
                              $stm_orders_status->execute([intval($result_status['status_id'])]);
                              $res=$stm_orders_status->fetch();
                              if($res){
                                if($res['status_name']=='Pending'){
                                  echo "<td><p class='status pending'><a href='detaille_orders.php?id_orders=" . $row['id'] . "'>Pending</a></p></td>";
                                }elseif($res['status_name']=='Processing'){
                                  echo "<td><p class='status processing'><a href='detaille_orders.php?id_orders=" . $row['id'] . "'>Processing</a></p></td>";
                                }elseif($res['status_name']=='Shipped'){                        
                                  echo "<td><p class='status shipped'><a href='detaille_orders.php?id_orders=" . $row['id'] . "'>Shipped</a></p></td>";
                                }elseif($res['status_name']=='Delivered'){
                                  echo "<td><p class='status delivered'><a href='detaille_orders.php?id_orders=" . $row['id'] . "'>Delivered</a></p></td>";
                                }else{
                                  echo "<td><p class='status cancelled'><a href='detaille_orders.php?id_orders=" . $row['id'] . "'>Canceled</a></p></td>";
                                }
                              }else{
                                 echo "<td></td>";
                              }
                            }else{
                              echo "<td></td>";
                            }


                            echo "</tr>";

                          }
                       }                      
                  ?>
                </tbody>
              </table>
            </div>

            
          </div>
        </div>
      </div>
    </div>


    <div class="fixed-plugin" style="top: 300px">
      <div class="dropdown open">
        <a href="#" data-toggle="dropdown">
        <i class="fa fa-cog fa-2x"> </i>
        </a>
        <ul class="dropdown-menu">
          <li class="header-title">Adjustments</li>
          <li class="adjustments-line">
            <a href="javascript:void(0)" class="switch-trigger" >
              <p>Full Background</p>
              <div class="switch" 
                data-on-label="ON"
                data-off-label="OFF" >
                <input type="checkbox" checked data-target="section-header" data-type="parallax"/>
              </div>
              <div class="clearfix"></div>
            </a>
          </li>
          <li class="adjustments-line">
            <a href="javascript:void(0)" class="switch-trigger">
              <p>Colors</p>
              <div class="pull-right">
                <span class="badge filter badge-blue" data-color="blue"></span>
                <span class="badge filter badge-azure" data-color="azure"></span>
                <span class="badge filter badge-green" data-color="green"></span>
                <span class="badge filter badge-orange active" data-color="orange"></span>
                <span class="badge filter badge-red" data-color="red"></span>
              </div>
              <div class="clearfix"></div>
            </a>
          </li>
          <li>
        <div class="">
          <a href="manage_Shippers/dashboard.php" target="_self" class="btn btn-default btn-block btn-fill">Shippers Management</a>
        </div>
      </li>
      <li>
        <div class="">
          <a href="delivery/levresion.php" target="_self" class="btn btn-default btn-block btn-fill">delivery</a>
        </div>
      </li>
      <li>
        <div class="">
          <a href="../dashboard.php" target="_self" class="btn btn-info btn-block btn-fill">back</a>
        </div>
      </li>
        
          
        
        </ul>

      </div>
    </div>
<?php else: ?>
  <p>inaccessible</p>
  <p><?php echo $message; ?></p>
<?php endif; ?>

</body>
<script type="text/javascript">
    var $table = $('#fresh-table')
    var $alertBtn = $('#alertBtn')

    window.operateEvents = {
      'click .like': function (e, value, row, index) {
        alert('You click like icon, row: ' + JSON.stringify(row))
        console.log(value, row, index)
      },
      'click .edit': function (e, value, row, index) {
        alert('You click edit icon, row: ' + JSON.stringify(row))
        console.log(value, row, index)
      },
      'click .remove': function (e, value, row, index) {
        $table.bootstrapTable('remove', {
          field: 'id',
          values: [row.id]
        })
      }
    }

    function operateFormatter(value, row, index) {
      return [
        '<a rel="tooltip" title="Like" class="table-action like" href="javascript:void(0)" title="Like">',
          '<i class="fa fa-heart"></i>',
        '</a>',
        '<a rel="tooltip" title="Edit" class="table-action edit" href="javascript:void(0)" title="Edit">',
          '<i class="fa fa-edit"></i>',
        '</a>',
        '<a rel="tooltip" title="Remove" class="table-action remove" href="javascript:void(0)" title="Remove">',
          '<i class="fa fa-remove"></i>',
        '</a>'
      ].join('')
    }

    $(function () {
      $table.bootstrapTable({
        classes: 'table table-hover table-striped',
        toolbar: '.toolbar',

        search: true,
        showRefresh: true,
        showToggle: true,
        showColumns: true,
        pagination: true,
        striped: true,
        sortable: true,
        pageSize: 8,
        pageList: [8, 10, 25, 50, 100],

        formatShowingRows: function (pageFrom, pageTo, totalRows) {
          return ''
        },
        formatRecordsPerPage: function (pageNumber) {
          return pageNumber + ' rows visible'
        }
      })

      $alertBtn.click(function () {
        alert('You pressed on Alert')
      })
    })

    $('#sharrreTitle').sharrre({
      share: {
        twitter: true,
        facebook: true
      },
      template: '',
      enableHover: false,
      enableTracking: true,
      render: function (api, options) {
        $("#sharrreTitle").html('Thank you for ' + options.total + ' shares!')
      },
      enableTracking: true,
      url: location.href
    })

    $('#twitter').sharrre({
      share: {
        twitter: true
      },
      enableHover: false,
      enableTracking: true,
      buttons: { twitter: {via: 'CreativeTim'}},
      click: function (api, options) {
        api.simulateClick()
        api.openPopup('twitter')
      },
      template: '<i class="fa fa-twitter"></i> {total}',
      url: location.href
    })

    $('#facebook').sharrre({
      share: {
        facebook: true
      },
      enableHover: false,
      enableTracking: true,
      click: function (api, options) {
        api.simulateClick()
        api.openPopup('facebook')
      },
      template: '<i class="fa fa-facebook-square"></i> {total}',
      url: location.href
    })
  </script>

  <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga')

    ga('create', 'UA-46172202-1', 'auto')
    ga('send', 'pageview')

  </script>
</html>
