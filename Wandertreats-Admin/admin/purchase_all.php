 <?php 


    ini_set('display_errors',0);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    date_default_timezone_set("Asia/Manila");

    session_start();

    $sessionId = session_id();

    //WANDERTREATS WEBSERVICE
     
    // use PHPMailer\PHPMailer\PHPMailer;
    // use PHPMailer\PHPMailer\SMTP;
    // require 'vendor/autoload.php';
    // require_once 'vendor/twilio/Services/Twilio.php';

    include_once('webservice/config.php');
    include_once('webservice/db_info.php');
    include_once('webservice/general_functions.php');

    $purchase = array();
    $messageArray = array();
    $purchaseArray = array();
    $updatePurchase =array();

    $sql = "SELECT * FROM purchase ORDER BY tPurchaseRequestDate DESC";
    $statement = $obj->query($sql); 
    $purchase = $statement ->fetchAll(); 

    function getStatus($code){
        $status = "";
        if($code == "1"){
            $status = "For Payment";
        }elseif ($code == "2") {
            $status = "Paid";
        }elseif ($code == "3") {
            # code...
        }elseif ($code == "4") {
            # code...
        }elseif ($code == "5") {
            $status = "For Claim";
        }elseif ($code == "6") {
            $status = "Claimed";
        }elseif ($code == "6") {
            $status = "Expired";
        }

        return $status;

    }

  // for($x= 0; $x < count($purchase); $x++){


  //     $dbtimestamp = strtotime($purchase[$x]['dRequestClaimDate']);
  //     //CHECK STATUS IF TIME IS VALID TO CLAIMED
  //     if ((time() - $dbtimestamp > 15 * 60) && $purchase[$x]['iStatusCode'] == "5")  {
  //       //SET STATUS CLAIMED
  //       unset($where);
  //       $where['vPurchaseNo'] = $purchase[$x]['vPurchaseNo'];
  //       $updatePurchase['iStatusCode'] = 6;
  //       $updatePurchase['dReceivedDate'] = @date("Y-m-d H:i:s");
  //       $result = myQuery("purchase", $updatePurchase, "update", $where);
  //     }

  //     $expirytimestamp = strtotime($purchase[$x]['tPurchaseExpiryDate']);
  //     if ((time() < $dbtimestamp) && $purchase[$x]['iStatusCode'] != "7")  {
  //       //SET STATUS EXPIRED
  //       unset($where);
  //       $where['vPurchaseNo'] = $purchase[$x]['vPurchaseNo'];
  //       $updatePurchase['iStatusCode'] = 7;
  //       $result = myQuery("purchase", $updatePurchase, "update", $where);
  //     }

  //     $tPurchaseExpiryDate = "Valid until " .date('d F Y, h:i:s A', strtotime($purchase[$x]['tPurchaseExpiryDate']));
  //     $purchaseArray[$x]['dValidity'] = $tPurchaseExpiryDate ;
  //     $purchaseArray[$x] = $purchase[$x];

  //     // $sql = "SELECT iMerchantId, vUserName, vStoreName, vStoreAddress, vLatitude, vLongitude, vRatings,  vLogo, vImages FROM `merchants` WHERE iMerchantId = '".$purchase [$x]['iMerchantId']."' AND eStatus = 'Active'"; 
  //     // $statement = $obj->query($sql); 
  //     // $merchantData = $statement ->fetchAll();

  //     // $sql = "SELECT pr.*, pd.* FROM products as pr JOIN purchase_details as pd ON pr.iProductId = pd.iProductId WHERE iPurchaseId = '".$purchase[$x]['iPurchaseId']."'  AND pr.eStatus = 'Active'";   
  //     // $statement = $obj->query($sql); 
  //     // $productData = $statement ->fetchAll(); 

  //     // // $purchaseArray[$x]['purchaseData'] = $purchaseDetails;
  //     // $purchaseArray[$x]['productData'] = $productData;
  //     // $purchaseArray[$x]['merchantData'] = $merchantData;

  // }



?>




<?php include("include/top-include.php"); ?>
                
<div class="main-content">
    <div class="container-fluid">
        
        
        <!-- START OF CONTENTS -->
        
       <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-file-text bg-blue"></i>
                        
                        <div class="d-inline">
                            <h5>All Purchase Order</h5>
                            <span>List of All Purchase Order</span>
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
                            <li class="breadcrumb-item active" aria-current="page">All Purchase Order</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>


        <div class="card">
            <div class="card-header row">
                <div class="col col-sm-3">
                    <div class="card-options d-inline-block">
                        <a href="#"><i class="ik ik-inbox"></i></a>
                        <a href="products_add.php"><i class="ik ik-plus"></i></a>
                        <a href="#"><i class="ik ik-rotate-cw"></i></a>
                        <div class="dropdown d-inline-block">
                            <a class="nav-link dropdown-toggle" href="#" id="moreDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ik ik-more-horizontal"></i></a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="moreDropdown">
                                <a class="dropdown-item" href="#">Action</a>
                                <a class="dropdown-item" href="#">More Action</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col col-sm-6">
                    <div class="card-search with-adv-search dropdown">
                        <form action="">
                            <input type="text" class="form-control global_filter" id="global_filter" placeholder="Search.." required>
                            <button type="submit" class="btn btn-icon"><i class="ik ik-search"></i></button>
                            <button type="button" id="adv_wrap_toggler" class="adv-btn ik ik-chevron-down dropdown-toggle" data-toggle="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                            <div class="adv-search-wrap dropdown-menu dropdown-menu-right" aria-labelledby="adv_wrap_toggler">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control column_filter" id="col0_filter" placeholder="Name" data-column="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control column_filter" id="col1_filter" placeholder="Position" data-column="1">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control column_filter" id="col2_filter" placeholder="Office" data-column="2">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="text" class="form-control column_filter" id="col3_filter" placeholder="Age" data-column="3">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="text" class="form-control column_filter" id="col4_filter" placeholder="Start date" data-column="4">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="text" class="form-control column_filter" id="col5_filter" placeholder="Salary" data-column="5">
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-theme">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col col-sm-3">
                    <div class="card-options text-right">
                        <span class="mr-5" id="top">1 - 50 of 2,500</span>
                        <a href="#"><i class="ik ik-chevron-left"></i></a>
                        <a href="#"><i class="ik ik-chevron-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="advanced_table" class="table">
                    <thead>
                        <tr>
                            <th class="nosort" width="10">
                                <label class="custom-control custom-checkbox m-0">
                                    <input type="checkbox" class="custom-control-input" id="selectall" name="" value="option2">
                                    <span class="custom-control-label">&nbsp;</span>
                                </label>
                            </th>
                            
                            <th>Reference No</th>
                            <th>Store</th>
              
                            <th>Product</th>
                            <th>Purchase By</th>
                            <th>Stocks</th>
                            <th>Date Purchase</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php for($x = 0 ; $x < count($purchase); $x++){ ?>
                        <?php $purArr = $purchase[$x];  ?>
                        <tr>
                            <td>
                                <label class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input select_all_child" id="" name="" value="option2">
                                    <span class="custom-control-label">&nbsp;</span>
                                </label>
                            </td>
                           
                            <td><?= $purArr['vPurchaseNo']; ?></td>
                            <td><?= $purArr['vStoreName']; ?></td>
                            <td><?= $purArr['vPurchaseName']; ?></td>
                            <td><?= stringToSecret($purArr['vName']."".$purArr['vLastName']); ?></td>
                            <td><?= number_format($purArr['fTotalGenerateFare'], 2); ?></td>
                            <td><?= $purArr['tPurchaseRequestDate']; ?></td>
                            <td><?= getStatus($purArr['iStatusCode']); ?></td>
                    
                            
                            <td>
                                <div class="list-actions">
                                    <a href="products_view.php?"> View &nbsp;<i class="ik ik-eye"></i></a>&nbsp;&nbsp;
                                    <a href="products_edit.php?"> Edit &nbsp;<i class="ik ik-edit-2"></i></a>&nbsp;&nbsp;
                                    <a href="#" class="list-delete" role="button" onclick="deleteItem('iPurchaseId', <?= $purArr['iPurchaseId']; ?>, 'purchase')"> Delete <i class="ik ik-trash-2"></i></a>
                                </div>
                            </td>
                             <td></td>
                        </tr>
                        
                       
                        <?php }  ?>

                    </tbody>
                </table>
            </div>
        </div>

           
        
        
        
        
        <!-- END OF CONTENTS -->
        

    </div>
</div>

<?php include("include/bottom-include.php"); ?>

       
        
        
     