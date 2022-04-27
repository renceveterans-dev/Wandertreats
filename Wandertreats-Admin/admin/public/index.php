<?php
?>

<!DOCTYPE html>
<html class="no-js" lang="en">

<?php include("include/header.php"); ?>

<body>
    
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

    <div class="wrapper">


        <!-- HEADER -->

        <?php include("../include/header_nav.php"); ?>


        <div class="page-wrap">


            <!-- SIDEBAR -->


            <?php include("../include/sidebar_nav.php"); ?>


            <div class="main-content">
                <div class="container-fluid">


                    <!-- START OF CONTENTS -->



                    <div class="row clearfix">

                                                    <!-- page statustic chart start -->
                            <div class="col-xl-3 col-md-6">
                                <div class="card card-red text-white">
                                    <div class="card-block">
                                        <div class="row align-items-center">
                                            <div class="col-8">
                                                <h4 class="mb-0" id="totalPassengers" >0</h4>
                                                <p class="mb-0">Passengers</p>
                                            </div>
                                            <div class="col-4 text-right">
                                                <i class="ik ik-user f-30"></i>
                                            </div>
                                        </div>
                                        <div id="Widget-line-chart1" class="chart-line chart-shadow" style="width:100%;height:75px"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card card-blue text-white">
                                    <div class="card-block">
                                        <div class="row align-items-center">
                                            <div class="col-8">
                                                <h4 class="mb-0">0</h4>
                                                <p class="mb-0">Hotels</p>
                                            </div>
                                            <div class="col-4 text-right">
                                                <i class="ik ik-shopping-cart f-30"></i>
                                            </div>
                                        </div>
                                        <div id="Widget-line-chart2" class="chart-line chart-shadow" style="width:100%;height:75px"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card card-green text-white">
                                    <div class="card-block">
                                        <div class="row align-items-center">
                                            <div class="col-8">
                                                <h4 class="mb-0">0</h4>
                                                <p class="mb-0">Visa/Passports</p>
                                            </div>
                                            <div class="col-4 text-right">
                                                <i class="ik ik-thumbs-up f-30"></i>
                                            </div>
                                        </div>
                                        <div id="Widget-line-chart3" class="chart-line chart-shadow" style="width:100%;height:75px"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card card-yellow text-white">
                                    <div class="card-block">
                                        <div class="row align-items-center">
                                            <div class="col-8">
                                                <h4 class="mb-0">0</h4>
                                                <p class="mb-0">Tours</p>
                                            </div>
                                            <div class="col-4 text-right">
                                                <i class="ik ik-volume-2 f-30"></i>
                                            </div>
                                        </div>
                                        <div id="Widget-line-chart4" class="chart-line chart-shadow" style="width:100%;height:75px"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- page statustic chart end -->
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            <div class="widget">
                                <div class="widget-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="state">
                                            <h6>Total Bookings</h6>
                                            <h2 id="Totalflights">0</h2>
                                        </div>
                                        <div class="icon">
                                            <i class="ik ik-send"></i>
                                        </div>
                                    </div>
                                    <!-- <small class="text-small mt-10 d-block">6% higher than last month</small> -->
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-danger" role="progressbar" aria-valuenow="62" aria-valuemin="0" aria-valuemax="100" style="width: 62%;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            <div class="widget">
                                <div class="widget-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="state">
                                            <h6>Total Active Flights</h6>
                                            <h2 id="ActiveFlights"></h2>
                                        </div>
                                        <div class="icon">
                                            <i class="ik ik-send"></i>
                                        </div>
                                    </div>
                                    <!-- <small class="text-small mt-10 d-block">61% higher than last month</small> -->
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-success" role="progressbar" aria-valuenow="78" aria-valuemin="0" aria-valuemax="100" style="width: 78%;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            <div class="widget">
                                <div class="widget-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="state">
                                            <h6>Total Cancelled Flights</h6>
                                            <h2 id="CancelledFlights"></h2>
                                        </div>
                                        <div class="icon">
                                            <i class="ik ik-send"></i>
                                        </div>
                                    </div>
                                   <!--  <small class="text-small mt-10 d-block">Total Events</small> -->
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="31" aria-valuemin="0" aria-valuemax="100" style="width: 31%;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            <div class="widget">
                                <div class="widget-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="state">
                                            <h6>Total Successful Flights</h6>
                                            <h2 id="SuccessfulFlights"></h2>
                                        </div>
                                        <div class="icon">
                                            <i class="ik ik-send"></i>
                                        </div>
                                    </div>
                                    <!-- <small class="text-small mt-10 d-block">Total Comments</small> -->
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-info" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <h4 class="card-title">Weather Report</h4>
                                            <select class="form-control w-25 ml-auto">
                                                <option selected="">Today</option>
                                                <option value="1">Weekly</option>
                                            </select>
                                        </div>
                                        <div class="d-flex align-items-center flex-row mt-30">
                                            <div class="p-2 f-50 text-info"><i class="wi wi-day-showers"></i> <span>23<sup>°</sup></span></div>
                                            <div class="p-2">
                                            <h3 class="mb-0"><?php echo date("l"); ?></h3><small>Philippines</small></div>
                                        </div>
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td>Wind</td>
                                                    <td class="font-medium">ESE 17 mph</td>
                                                </tr>
                                                <tr>
                                                    <td>Humidity</td>
                                                    <td class="font-medium">83%</td>
                                                </tr>
                                                <tr>
                                                    <td>Pressure</td>
                                                    <td class="font-medium">28.56 in</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <hr>
                                        <ul class="list-unstyled row text-center city-weather-days mb-0 mt-20">
                                            <li class="col"><i class="wi wi-day-sunny mr-5"></i><span>09:30</span><h3>20<sup>°</sup></h3></li>
                                            <li class="col"><i class="wi wi-day-cloudy mr-5"></i><span>11:30</span><h3>22<sup>°</sup></h3></li>
                                            <li class="col"><i class="wi wi-day-hail mr-5"></i><span>13:30</span><h3>25<sup>°</sup></h3></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card" style="min-height: 422px;">
                                    <div class="card-header">
                                        <h3>Flights Today</h3>
                                        <div class="card-header-right">
                                            <ul class="list-unstyled card-option">
                                                <li><i class="ik ik-chevron-left action-toggle"></i></li>
                                                <li><i class="ik ik-minus minimize-card"></i></li>
                                                <li><i class="ik ik-x close-card"></i></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-body timeline">
                                        <div class="header bg-theme" style="background-image: url('../img/placeholder/placeimg_400_200_nature.jpg')">
                                            <div class="color-overlay d-flex align-items-center">
                                                <div class="day-number" id="DayDate" >8</div>
                                                <div class="date-right">
                                                    <div class="day-name" id="time_span"></div>
                                                    <div class="month" id="WeekDay">Monday, February 2018</div>
                                                </div>
                                            
                                            </div>                                
                                        </div>
                                        <ul id="flightTodayList">
                                            <li>
                                                <div class="bullet bg-pink"></div>
                                                <div class="time">11am</div>
                                                <div class="desc">
                                                    <h3>Attendance</h3>
                                                    <h4>Computer Class</h4>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="bullet bg-green"></div>
                                                <div class="time">12pm</div>
                                                <div class="desc">
                                                    <h3>Design Team</h3>
                                                    <h4>Hangouts</h4>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="bullet bg-orange"></div>
                                                <div class="time">2pm</div>
                                                <div class="desc">
                                                    <h3>Finish</h3>
                                                    <h4>Go to Home</h4>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                           <!--  <div class="col-md-4">
                                <div class="card" style="min-height: 422px;">
                                    <div class="card-header"><h3>Donut chart</h3></div>
                                    <div class="card-body">
                                        <div id="c3-donut-chart"></div>
                                    </div>
                                </div>
                            </div> -->
                        </div>

                        <div class="card">
                            <div class="card-header row">
                                <div class="col col-sm-3">
                                    <div class="card-options d-inline-block">
                                        <a href="#"><i class="ik ik-inbox"></i></a>
                                        <a href="#"><i class="ik ik-plus"></i></a>
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
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>Phone</th>
                                            <th>Gender</th>
                                            <th>Booking Ref</th>
                                            <th>Date Booked</th>
                                        </tr>
                                    </thead>
                                    <tbody id="passengersTable">
                                        
                                            <?php


                                               

                                                
                                                

                                            ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>








                    <!-- END OF CONTENTS -->


                </div>
            </div>

<!--             <aside class="right-sidebar">
                <div class="sidebar-chat" data-plugin="chat-sidebar">
                    <div class="sidebar-chat-info">
                        <h6>Chat List</h6>
                        <form class="mr-t-10">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Search for friends ...">
                                <i class="ik ik-search"></i>
                            </div>
                        </form>
                    </div>
                    <div class="chat-list">
                        <div class="list-group row">
                            <a href="javascript:void(0)" class="list-group-item" data-chat-user="Gene Newman">
                                <figure class="user--online">
                                    <img src="img/users/1.jpg" class="rounded-circle" alt="">
                                </figure><span><span class="name">Gene Newman</span> <span class="username">@gene_newman</span> </span>
                            </a>
                            <a href="javascript:void(0)" class="list-group-item" data-chat-user="Billy Black">
                                <figure class="user--online">
                                    <img src="img/users/2.jpg" class="rounded-circle" alt="">
                                </figure><span><span class="name">Billy Black</span> <span class="username">@billyblack</span> </span>
                            </a>
                            <a href="javascript:void(0)" class="list-group-item" data-chat-user="Herbert Diaz">
                                <figure class="user--online">
                                    <img src="img/users/3.jpg" class="rounded-circle" alt="">
                                </figure><span><span class="name">Herbert Diaz</span> <span class="username">@herbert</span> </span>
                            </a>
                            <a href="javascript:void(0)" class="list-group-item" data-chat-user="Sylvia Harvey">
                                <figure class="user--busy">
                                    <img src="img/users/4.jpg" class="rounded-circle" alt="">
                                </figure><span><span class="name">Sylvia Harvey</span> <span class="username">@sylvia</span> </span>
                            </a>
                            <a href="javascript:void(0)" class="list-group-item active" data-chat-user="Marsha Hoffman">
                                <figure class="user--busy">
                                    <img src="img/users/5.jpg" class="rounded-circle" alt="">
                                </figure><span><span class="name">Marsha Hoffman</span> <span class="username">@m_hoffman</span> </span>
                            </a>
                            <a href="javascript:void(0)" class="list-group-item" data-chat-user="Mason Grant">
                                <figure class="user--offline">
                                    <img src="img/users/1.jpg" class="rounded-circle" alt="">
                                </figure><span><span class="name">Mason Grant</span> <span class="username">@masongrant</span> </span>
                            </a>
                            <a href="javascript:void(0)" class="list-group-item" data-chat-user="Shelly Sullivan">
                                <figure class="user--offline">
                                    <img src="img/users/2.jpg" class="rounded-circle" alt="">
                                </figure><span><span class="name">Shelly Sullivan</span> <span class="username">@shelly</span></span>
                            </a>
                        </div>
                    </div>
                </div>
            </aside> -->

<!--             <div class="chat-panel" hidden>
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <a href="javascript:void(0);"><i class="ik ik-message-square text-success"></i></a>
                        <span class="user-name">John Doe</span>
                        <button type="button" class="close" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="card-body">
                        <div class="widget-chat-activity flex-1">
                            <div class="messages">
                                <div class="message media reply">
                                    <figure class="user--online">
                                        <a href="#">
                                            <img src="img/users/3.jpg" class="rounded-circle" alt="">
                                        </a>
                                    </figure>
                                    <div class="message-body media-body">
                                        <p>Epic Cheeseburgers come in all kind of styles.</p>
                                    </div>
                                </div>
                                <div class="message media">
                                    <figure class="user--online">
                                        <a href="#">
                                            <img src="img/users/1.jpg" class="rounded-circle" alt="">
                                        </a>
                                    </figure>
                                    <div class="message-body media-body">
                                        <p>Cheeseburgers make your knees weak.</p>
                                    </div>
                                </div>
                                <div class="message media reply">
                                    <figure class="user--offline">
                                        <a href="#">
                                            <img src="img/users/5.jpg" class="rounded-circle" alt="">
                                        </a>
                                    </figure>
                                    <div class="message-body media-body">
                                        <p>Cheeseburgers will never let you down.</p>
                                        <p>They'll also never run around or desert you.</p>
                                    </div>
                                </div>
                                <div class="message media">
                                    <figure class="user--online">
                                        <a href="#">
                                            <img src="img/users/1.jpg" class="rounded-circle" alt="">
                                        </a>
                                    </figure>
                                    <div class="message-body media-body">
                                        <p>A great cheeseburger is a gastronomical event.</p>
                                    </div>
                                </div>
                                <div class="message media reply">
                                    <figure class="user--busy">
                                        <a href="#">
                                            <img src="img/users/5.jpg" class="rounded-circle" alt="">
                                        </a>
                                    </figure>
                                    <div class="message-body media-body">
                                        <p>There's a cheesy incarnation waiting for you no matter what you palete preferences are.</p>
                                    </div>
                                </div>
                                <div class="message media">
                                    <figure class="user--online">
                                        <a href="#">
                                            <img src="img/users/1.jpg" class="rounded-circle" alt="">
                                        </a>
                                    </figure>
                                    <div class="message-body media-body">
                                        <p>If you are a vegan, we are sorry for you loss.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form action="javascript:void(0)" class="card-footer" method="post">
                        <div class="d-flex justify-content-end">
                            <textarea class="border-0 flex-1" rows="1" placeholder="Type your message here"></textarea>
                            <button class="btn btn-icon" type="submit"><i class="ik ik-arrow-right text-success"></i></button>
                        </div>
                    </form>
                </div>
            </div> -->


            <!-- FOOTER -->

            <?php include("../include/footer.php"); ?>




        </div>



    </div>



<!-- 
    <div id="loading">
      <img id="loading-image" src="../img/loading.gif" alt="Loading..." />
    </div> -->

    <div class="modal fade apps-modal" id="appsModal" tabindex="-1" role="dialog" aria-labelledby="appsModalLabel" aria-hidden="true" data-backdrop="false">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="ik ik-x-circle"></i></button>
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="quick-search">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-4 ml-auto mr-auto">
                                <div class="input-wrap">
                                    <input type="text" id="quick-search" class="form-control" placeholder="Search..." />
                                    <i class="ik ik-search"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body d-flex align-items-center">
                    <div class="container">
                        <div class="apps-wrap">
                            <div class="app-item">
                                <a href="#"><i class="ik ik-bar-chart-2"></i><span>Dashboard</span></a>
                            </div>
                            <div class="app-item dropdown">
                                <a href="#" class="dropdown-toggle" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ik ik-command"></i><span>Ui</span></a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <a class="dropdown-item" href="#">Action</a>
                                    <a class="dropdown-item" href="#">Another action</a>
                                    <a class="dropdown-item" href="#">Something else here</a>
                                </div>
                            </div>
                            <div class="app-item">
                                <a href="#"><i class="ik ik-mail"></i><span>Message</span></a>
                            </div>
                            <div class="app-item">
                                <a href="#"><i class="ik ik-users"></i><span>Accounts</span></a>
                            </div>
                            <div class="app-item">
                                <a href="#"><i class="ik ik-shopping-cart"></i><span>Sales</span></a>
                            </div>
                            <div class="app-item">
                                <a href="#"><i class="ik ik-briefcase"></i><span>Purchase</span></a>
                            </div>
                            <div class="app-item">
                                <a href="#"><i class="ik ik-server"></i><span>Menus</span></a>
                            </div>
                            <div class="app-item">
                                <a href="#"><i class="ik ik-clipboard"></i><span>Pages</span></a>
                            </div>
                            <div class="app-item">
                                <a href="#"><i class="ik ik-message-square"></i><span>Chats</span></a>
                            </div>
                            <div class="app-item">
                                <a href="#"><i class="ik ik-map-pin"></i><span>Contacts</span></a>
                            </div>
                            <div class="app-item">
                                <a href="#"><i class="ik ik-box"></i><span>Blocks</span></a>
                            </div>
                            <div class="app-item">
                                <a href="#"><i class="ik ik-calendar"></i><span>Events</span></a>
                            </div>
                            <div class="app-item">
                                <a href="#"><i class="ik ik-bell"></i><span>Notifications</span></a>
                            </div>
                            <div class="app-item">
                                <a href="#"><i class="ik ik-pie-chart"></i><span>Reports</span></a>
                            </div>
                            <div class="app-item">
                                <a href="#"><i class="ik ik-layers"></i><span>Tasks</span></a>
                            </div>
                            <div class="app-item">
                                <a href="#"><i class="ik ik-edit"></i><span>Blogs</span></a>
                            </div>
                            <div class="app-item">
                                <a href="#"><i class="ik ik-settings"></i><span>Settings</span></a>
                            </div>
                            <div class="app-item">
                                <a href="#"><i class="ik ik-more-horizontal"></i><span>More</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script>
        window.jQuery || document.write('<script src="src/js/vendor/jquery-3.3.1.min.js"><\/script>')
    </script>

    <script src="../plugins/popper.js/dist/umd/popper.min.js"></script>
    <script src="../plugins/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="../plugins/perfect-scrollbar/dist/perfect-scrollbar.min.js"></script>
    <script src="../plugins/screenfull/dist/screenfull.js"></script>
    <script src="../plugins/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../plugins/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../plugins/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
    
    <script src="../plugins/jvectormap/jquery-jvectormap.min.js"></script>
    <script src="../plugins/jvectormap/tests/assets/jquery-jvectormap-world-mill-en.js"></script>
    <script src="../plugins/chartist/dist/chartist.min.js"></script>
    <script src="../js/widget-statistic.js"></script>
    <script src="../plugins/moment/moment.js"></script>
    <script src="../plugins/tempusdominus-bootstrap-4/build/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="../plugins/d3/dist/d3.min.js"></script>
    <script src="../plugins/c3/c3.min.js"></script>
    <script src="../js/tables.js"></script>
    <script src="../js/widgets.js"></script>
    <script src="../js/charts.js"></script>
    <script src="../dist/js/theme.min.js"></script>

    <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
    <script>
        (function(b, o, i, l, e, r) {
            b.GoogleAnalyticsObject = l;
            b[l] || (b[l] =
                function() {
                    (b[l].q = b[l].q || []).push(arguments)
                });
            b[l].l = +new Date;
            e = o.createElement(i);
            r = o.getElementsByTagName(i)[0];
            e.src = 'https://www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e, r)
        }(window, document, 'script', 'ga'));
        ga('create', 'UA-XXXXX-X', 'auto');
        ga('send', 'pageview');
    </script>
</body>

</html>

<?php ?>