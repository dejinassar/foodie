<!DOCTYPE html>
<html lang="en">
<?php
include("connection/connect.php"); 
error_reporting(0);
session_start();
include_once 'product-action.php'; 

// Fetch restaurant details
$res_id = $_GET['res_id'];
$stmt = $db->prepare("SELECT * FROM restaurant WHERE rs_id=?");
$stmt->bind_param("i", $res_id);
$stmt->execute();
$restaurant = $stmt->get_result()->fetch_assoc();
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $restaurant['title']; ?> - Menu</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
    .restaurant-info {
        text-align: center;
        margin-bottom: 20px;
    }

    .restaurant-info img {
        width: 150px;
        height: auto;
        padding-top: 100px;
    }

    .cart-container {
        margin-top: 30px;
    }
    </style>
</head>

<body>

    <header id="header" class="header-scroll top-header headrom">
        <nav class="navbar navbar-dark">
            <div class="container">
                <button class="navbar-toggler hidden-lg-up" type="button" data-toggle="collapse"
                    data-target="#mainNavbarCollapse">&#9776;</button>
                <a class="navbar-brand" href="index.php"> <img class="img-rounded" src="images/icn.png" alt=""> </a>
                <div class="collapse navbar-toggleable-md  float-lg-right" id="mainNavbarCollapse">
                    <ul class="nav navbar-nav">
                        <li class="nav-item"> <a class="nav-link active" href="index.php">Home <span
                                    class="sr-only">(current)</span></a> </li>
                        <li class="nav-item"> <a class="nav-link active" href="restaurants.php">Restaurants <span
                                    class="sr-only"></span></a> </li>


                        <?php
						if(empty($_SESSION["user_id"])) // if user is not login
							{
								echo '<li class="nav-item"><a href="login.php" class="nav-link active">Login</a> </li>
							  <li class="nav-item"><a href="registration.php" class="nav-link active">Register</a> </li>';
							}
						else
							{

									
									echo  '<li class="nav-item"><a href="your_orders.php" class="nav-link active">My Orders</a> </li>';
									echo  '<li class="nav-item"><a href="logout.php" class="nav-link active">Logout</a> </li>';
							}

						?>

                    </ul>

                </div>
            </div>
        </nav>

    </header>

    <div class="container mt-4">
        <!-- Restaurant Details -->
        <div class="restaurant-info">
            <img src="admin/Res_img/<?php echo $restaurant['image']; ?>" alt="Restaurant Image">
            <h2><?php echo $restaurant['title']; ?></h2>
            <p><?php echo $restaurant['address']; ?></p>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="menu-widget">
                    <h3 class="widget-title text-dark">MENU</h3>
                    <div class="menu-list">
                        <?php  
                        $stmt = $db->prepare("SELECT * FROM dishes WHERE rs_id=?");
                        $stmt->bind_param("i", $res_id);
                        $stmt->execute();
                        $products = $stmt->get_result();
                        if (!empty($products)) {
                            foreach ($products as $product) {
                        ?>
                        <div class="food-item row align-items-center">
                            <div class="col-lg-3">
                                <img src="admin/Res_img/dishes/<?php echo $product['img']; ?>"
                                    class="img-fluid menu-img" alt="Food">
                            </div>
                            <div class="col-lg-6">
                                <h6><a href="#"><?php echo $product['title']; ?></a></h6>
                                <p><?php echo $product['slogan']; ?></p>
                                <span class="price">&#8358;<?php echo number_format($product['price'], 2); ?></span>
                            </div>
                            <div class="col-lg-3 text-right">
                                <form method="post"
                                    action='dishes.php?res_id=<?php echo $_GET['res_id'];?>&action=add&id=<?php echo $product['d_id']; ?>'>
                                    <input type="number" name="quantity" value="1" min="1" class="form-control mb-2"
                                        style="width: 60px; display: inline-block;" />
                                    <button type="submit" class="btn btn-success btn-sm">Add</button>
                                </form>
                            </div>
                        </div>
                        <hr>
                        <?php
                            }
                        } else {
                            echo '<p>No dishes available for this restaurant.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4 cart-container">
                <div class="widget widget-cart">
                    <h3 class="widget-title">Your Cart</h3>
                    <?php
                    $item_total = 0;
                    if (!empty($_SESSION["cart_item"])) {
                        foreach ($_SESSION["cart_item"] as $item) {
                    ?>
                    <div class="cart-item d-flex align-items-center">

                        <div class="cart-details flex-grow-1 ml-2">
                            <p><?php echo $item["title"] . " x " . $item["quantity"] ?>
                                <span
                                    class="float-right">&#8358;<?php echo number_format($item["price"] * $item["quantity"], 2); ?></span>
                            </p>
                        </div>
                        <a href="dishes.php?res_id=<?php echo $_GET['res_id']; ?>&action=remove&id=<?php echo $item['d_id']; ?>"
                            class="btn btn-danger btn-sm">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                    <hr>
                    <?php
                            $item_total += ($item["price"] * $item["quantity"]);
                        }
                    ?>
                    <h4>Total: &#8358;<?php echo number_format($item_total, 2); ?></h4>
                    <a href="checkout.php?res_id=<?php echo $_GET['res_id']; ?>"
                        class="btn btn-primary btn-block">Checkout</a>
                    <?php
                    } else {
                        echo "<p>Your cart is empty.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>

</html>