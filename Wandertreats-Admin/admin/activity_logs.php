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

    $success = isset($_REQUEST['success']) ? trim($_REQUEST['success']) : '1';

    $sql = "SELECT * FROM administrator"; 
    $statement = $obj->query($sql); 
    $administrators = $statement ->fetchAll();

   

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
                            <h5>Activity Logs</h5>
                            <span>List of system activities</span>
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
                            <li class="breadcrumb-item active" aria-current="page">Activity Logs</li>
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
                        <a href="admin_add.php"><i class="ik ik-plus"></i></a>
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
                            <th class="nosort">Avatar</th>
                            <th>Name</th>
                            <th>Admin Level</th>
                            <th>Contact</th>
                            <th>Age</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php for($x = 0 ; $x < count($administrators); $x++){ ?>

                        <tr>
                            <td>
                                <label class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input select_all_child" id="" name="" value="option2">
                                    <span class="custom-control-label">&nbsp;</span>
                                </label>
                            </td>
                            <td><img src="img/users/1.jpg" class="table-user-thumb" alt=""></td>
                            <td><?= $administrators[$x]['vFirstName']." ".$administrators[$x]['vLastName']; ?></td>
                            <td><?= $administrators[$x]['vFirstName']." ".$administrators[$x]['vLastName']; ?></td>
                            <td><?= $administrators[$x]['vEmail']; ?></td>
                            <td><?= $administrators[$x]['vMobile']; ?></td>
                            <td><?= $administrators[$x]['eStatus']; ?></td>
                            <td>
                                <div class="list-actions">
                                    <a href="admin_view.php?id=<?= $administrators[$x]['iAdminId']; ?>">  &nbsp;<i class="ik ik-eye"></i></a>&nbsp;&nbsp;
                                    <a href="admin_edit.php?id=<?= $administrators[$x]['iAdminId']; ?>">  &nbsp;<i class="ik ik-edit-2"></i></a>&nbsp;&nbsp;
                                    <a href="#" class="list-delete">  <i class="ik ik-trash-2"></i></a>
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


<script>
  $( document ).ready(function() {

    var success = '<?= $success; ?>';
    if(success == 'true'){
        $.toast({
            heading: 'Success',
            text: 'New administrator successfully registered!',
            showHideTransition: 'slide',
            icon: 'success',
            loaderBg: '#f96868',
            position: 'top-right'
        });
    }

  });
</script>

       
		
        
     