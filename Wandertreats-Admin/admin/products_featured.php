
<?php 


    ini_set('display_errors',0);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    date_default_timezone_set("Asia/Manila");

    session_start();

    $sessionId = session_id();

    // WANDERTREATS WEBSERVICE
     
    // use PHPMailer\PHPMailer\PHPMailer;
    // use PHPMailer\PHPMailer\SMTP;
    // require 'vendor/autoload.php';
    // require_once 'vendor/twilio/Services/Twilio.php';

    include_once('webservice/config.php');
    include_once('webservice/db_info.php');
    include_once('webservice/general_functions.php');


    $productsArr = array();


    $sql = "SELECT * FROM merchant_types"; 
    $statement = $obj->query($sql); 
    $merchantTypeData = $statement ->fetchAll(); 

    $sql = "SELECT me.iMerchantId, me.vUserName, me.vStoreName, me.vStoreAddress, me.vLatitude, me.vLongitude, me.vLogo, pc.vCategoryName, pi.* FROM merchants as me LEFT JOIN product_category as pc ON me.iMerchantId = pc.iMerchantId LEFT JOIN products as pi ON pi.iCategoryId = pc.iCategoryId WHERE pi.vProductName != '' ";

    $statement = $obj->query($sql); 
    $products = $statement ->fetchAll();

    for ($i = 0; $i < count($products); $i++) {
        $productsArr[$i] = $products[$i];
        $imageData = array();
        $imgArr = explode(",", $productsArr[$i]['vImages']);
        for ($k = 0; $k < count($imgArr); $k++) {
            $imageData[$k]['vImage'] = $imgArr[$k];
        }

        $productsArr[$i]['vImages'] = $imageData;

    }

    // echo print_r(json_encode($merchantsArray));

?>

<?php include("include/top-include.php"); ?>
				
<div class="main-content">
    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-file-text bg-blue"></i>
                        <div class="d-inline">
                            <h5>Featured Products</h5>
                            <span>List of Featured Products</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="../index.html"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">Pages</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Featured Products</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="mb-2 clearfix">
                    <a class="btn pt-0 pl-0 d-md-none d-lg-none" data-toggle="collapse" href="#displayOptions" role="button" aria-expanded="true" aria-controls="displayOptions">
                        Display Options
                        <i class="ik ik-chevron-down align-middle"></i>
                    </a>
                    <div class="collapse d-md-block display-options" id="displayOptions">
                        <span class="mr-3 d-inline-block float-md-left dispaly-option-buttons">
                            <a href="products_add.php" class="btn btn-light mr-1 ">
                                <i class="ik ik-plus view-icon"></i> Add Product
                            </a>
                            <a href="#" class="mr-1 view-list active">
                                <i class="ik ik-menu view-icon"></i>
                            </a>
                            <a href="#" class="mr-1 view-thumb">
                                <i class="ik ik-list view-icon"></i>
                            </a>
                            <a href="#" class="mr-1 view-grid">
                                <i class="ik ik-grid view-icon"></i>
                            </a>
                        </span>
                        <div class="d-block d-md-inline-block">
                            <div class="btn-group float-md-left mr-1 mb-1">
                                <button class="btn btn-outline-dark btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Order By
                                    <i class="ik ik-chevron-down mr-0 align-middle"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#">Action</a>
                                    <a class="dropdown-item" href="#">Another action</a>
                                </div>
                            </div>
                            <div class="search-sm d-inline-block float-md-left mr-1 mb-1 align-top">
                                <form action="">
                                    <input type="text" class="form-control" placeholder="Search.." required>
                                    <button type="submit" class="btn btn-icon"><i class="ik ik-search"></i></button>
                                    <button type="button" id="adv_wrap_toggler" class="adv-btn ik ik-chevron-down dropdown-toggle" data-toggle="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                    <div class="adv-search-wrap dropdown-menu dropdown-menu-right" aria-labelledby="adv_wrap_toggler">
                                        <div class="form-group">
                                            <input type="text" class="form-control" placeholder="Full Name">
                                        </div>
                                        <div class="form-group">
                                            <input type="email" class="form-control" placeholder="Email">
                                        </div>
                                        <button class="btn btn-theme">Search</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="float-md-right">
                            <span class="text-muted text-small mr-2">Displaying 1-10 of 210 items </span>
                            <button class="btn btn-outline-dark btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                20
                                <i class="ik ik-chevron-down mr-0 align-middle"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="#">10</a>
                                <a class="dropdown-item" href="#">20</a>
                                <a class="dropdown-item" href="#">30</a>
                                <a class="dropdown-item" href="#">50</a>
                                <a class="dropdown-item" href="#">100</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="separator mb-20"></div>
                <div class="row layout-wrap" id="layout-wrap">

                    <?php for($x = 0 ; $x < count($productsArr); $x++){ ?>

                    <div class="col-12 list-item">
                        <div class="card d-flex flex-row mb-3">
                            <a class="d-flex card-img" href="#editLayoutItem" data-toggle="modal" data-target="#editLayoutItem">
                                <img src="<?= constants::BASE_URL."uploads/products/".$productsArr[$x]['vLogo'];?>" alt="<?= $merchantsArray[$x]['vLogo']; ?>" class="list-thumbnail responsive border-0">
                                <span class="badge badge-pill badge-primary position-absolute badge-top-left">New</span>
                                <span class="badge badge-pill badge-secondary position-absolute badge-top-left-2">Trending</span>
                            </a>
                            <div class="d-flex flex-grow-1 min-width-zero card-content">
                                <div class="card-body align-self-center d-flex flex-column flex-md-row justify-content-between min-width-zero align-items-md-center">
                                    <p class="mb-1 text-muted text-small category w-15 w-xs-100"><?= $productsArr[$x]['vProductName']; ?></p>
                                    <a class="list-item-heading mb-1 truncate w-40 w-xs-100" href="#editLayoutItem" data-toggle="modal" data-target="#editLayoutItem">
                                       <?= $productsArr[$x]['vStoreName']; ?>
                                    </a>
                                    <p class="mb-1 text-muted text-small category w-15 w-xs-100"><span>&#8369;</span><?= $productsArr[$x]['fPrice']; ?></p>
                                    <p class="mb-1 text-muted text-small date w-15 w-xs-100">Stocks : <?= $productsArr[$x]['iStock']; ?></p>
                                    <div class="w-15 w-xs-100">
                                        <span class="badge badge-pill badge-success"><?= $productsArr[$x]['eStatus']; ?></span>
                                    </div>
                                </div>
                                <div class="list-actions">
                                    <a href="#editLayoutItem" data-toggle="modal" data-target="#editLayoutItem"><i class="ik ik-eye"></i></a>
                                    <a href="layout-edit-item.html"><i class="ik ik-edit-2"></i></a>
                                    <a href="#" class="list-delete"><i class="ik ik-trash-2"></i></a>
                                </div>
                                <div class="custom-control custom-checkbox pl-1 align-self-center">
                                    <label class="custom-control custom-checkbox mb-0">
                                        <input type="checkbox" class="custom-control-input">
                                        <span class="custom-control-label"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php } ?> 
                   
                
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("include/bottom-include.php"); ?>