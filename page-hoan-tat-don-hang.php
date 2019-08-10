<?
    /*echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";*/
    $siteurl = get_bloginfo('siteurl');
    if(empty($_SESSION['booking'])){
        header('Location:'.$siteurl);
        exit();
    }
    
    require(TEMPLATEPATH . '/flight_config/sugarrest/sugar_rest.php');
    $sugar = new Sugar_REST();
    $error = $sugar->get_error();
    
    $booking_id = $_SESSION['booking']['id'];
    $options['limit'] = 1;
    // lấy thông tin booking
    $options['where'] = 'ec_flight_bookings.id="'.$booking_id .'"';
    $select = array('name','email','phone','booking_status','flight_type','total_amount', 'is_paid','payment_type','ticket_type');
    $result = $sugar->get("EC_Flight_Bookings", $select, $options);
    $result = $result[0];
    
    $way_flight = $result['flight_type']; 				// Một chiều or Khứ hồi
    $way_flight_text = ($way_flight==0)?"Khứ hồi":"Một chiều"; 				// Một chiều or Khứ hồi
    
    $total_amount = $result['total_amount'];			// Tổng giá tiền của booking
    $booking_status = $result['booking_status'];		// Trạng thái booking
    ($result['is_paid'] == 0) ? $is_paid='Chưa thanh toán' : $is_paid='Đã thanh toán';
    $payment_type = $GLOBALS['payment_type'][$result['payment_type']];
    // lấy thông tin chuyến bay
    
    $options_itinerary['where'] = "ec_booking_itineraries.booking_id = '".$booking_id ."'";
    $select_itinerary = array('direction','departure', 'arrival', 'departure_date', 'arrival_date','flight_number','ticket_class','airline_code','description','duration','airline_name');
    $res_itinerary = $sugar->get("EC_Booking_Itineraries", $select_itinerary, $options_itinerary);
    
    foreach($res_itinerary as $key => $val){
        if($val['direction'] == 0){
            $depart_date = date('d/m/Y', strtotime($val['departure_date']));  		// Ngày đi
            $dep_deptime = date('H:i', strtotime($val['departure_date']));			// Giờ đi
            $dep_arvtime = date('H:i', strtotime($val['arrival_date']));
            $dep_source = $val['departure'];
            $dep_destination = $val['arrival'];
            $dep_flightno = $val['flight_number'];
            $dep_class = $val['ticket_class'];
            if($val['airline_code'] == 'VNA'){
                // $dep_logo = 'bg_vnal';
                $dep_logo = '<img src="'.get_template_directory_uri().'/images/airline-icons/smVN.png"';
            }
            elseif($val['airline_code'] == 'JET'){
                // $dep_logo = 'bg_js';
                $dep_logo = '<img src="'.get_template_directory_uri().'/images/airline-icons/smBL.png"';
            }
            elseif($val['airline_code'] == 'AMK'){
                $dep_logo = 'bg_amk';
            }
            elseif($val['airline_code'] == 'VJA'){
                // $dep_logo = 'bg_vj';
                $dep_logo = '<img src="'.get_template_directory_uri().'/images/airline-icons/smVJ.png"';
            }
            elseif($val['airline_code'] == 'BBA'){
                // $dep_logo = 'bg_qh';
                $dep_logo = '<img src="'.get_template_directory_uri().'/images/airline-icons/smQH.png"';
            }
        }
        if($val['direction'] == 1){
            $return_date = date('d/m/Y', strtotime($val['departure_date']));			// Ngày về
            $ret_deptime = date('H:i', strtotime($val['departure_date']));
            $ret_arvtime = date('H:i', strtotime($val['arrival_date']));			// Giờ về
            $ret_source = $val['departure'];
            $ret_destination = $val['arrival'];
            $ret_flightno = $val['flight_number'];
            $ret_class = $val['ticket_class'];
            if($val['airline_code'] == 'VNA'){
                // $ret_logo = 'bg_vnal';
                $ret_logo = '<img src="'.get_template_directory_uri().'/images/airline-icons/smVN.png"';
            }
            elseif($val['airline_code'] == 'JET'){
                // $ret_logo = 'bg_js';
                $ret_logo = '<img src="'.get_template_directory_uri().'/images/airline-icons/smBL.png"';
            }
            elseif($val['airline_code'] == 'AMK'){
                $ret_logo = 'bg_amk';
            }
            elseif($val['airline_code'] == 'VJA'){
                // $ret_logo = 'bg_vj';
                $ret_logo = '<img src="'.get_template_directory_uri().'/images/airline-icons/smVJ.png"';
            }
            elseif($val['airline_code'] == 'BBA'){
                // $ret_logo = 'bg_qh';
                $ret_logo = '<img src="'.get_template_directory_uri().'/images/airline-icons/smQH.png"';
            }
        }
    }
    
    // lấy thông tin hành khách
    $options_passenger['where'] = "ec_booking_passengers.booking_id = '".$booking_id ."'";
    $select_passenger = array('type');
    $res_passenger = $sugar->get("EC_Booking_Passengers", $select_passenger, $options_passenger);
    $adults = 0;
    $children = 0;
    $infants = 0;
    foreach($res_passenger as $key => $val){
        if($val['type'] == 0)
            $adults++;
        if($val['type'] == 1)
            $children++;
        if($val['type'] == 2)
            $infants ++;
    }
    if($children == 0 || $children != 0)
        $qty_children = ', '.$children.' trẻ em';
    if($infants == 0 || $infants != 0)
        $qty_infants = ', '.$infants.' trẻ sơ sinh.';
    
    if(!isset($_SESSION[$booking_id]['sendmail'])){
        if($result['ticket_type'] == 2){
            //include(get_stylesheet_directory()."/flight_config/mailconfirm_inter.php");
        }else{
            #include(get_stylesheet_directory()."/flight_config/mailconfirm.php");
        }
    
        $_SESSION[$booking_id]['sendmail']=true;
    }
    
?>
<?php get_header(); ?>
<div class="main">
    <div class="col-md-8">
        <div class="row">
            <ul id="progressbar">
                <li class="pass"><a href="#"><span class="hidden-xs">Chọn hành trình</span></a></li>
                <li class="pass"><a href="#"><span class="hidden-xs">Thông tin hành khách</span></a></li>
                <li class="pass"><a href="#"><span class="hidden-xs">Thanh toán</span></a></li>
                <li class="current"><span class="hidden-xs">Hoàn tất</span></li>
            </ul>
        </div>
        <div id="mainDisplay">
            <div class="row">
                <!-- <h1 class="complete-title"><?php the_title(); ?></h1> -->
                <h2>Booking: <strong class="order-number"><?= $result['name'] ?></strong></h2>
                <div>
                    <p>Booker sẽ liên hệ quý khách trong thời gian sớm nhất (giờ làm việc) để xác thực thông tin đăng ký và giá vé của thời điểm xử lý.</p>
                    <?php $phoneLast = '0909 58 8080'; ?>
                    <p>Sau quá trình xác nhận này quý khách cần chuyển khoản thanh toán để bảo vệ giá. Sau khi chuyển khoản thành công quý khách vui lòng gọi vào số <strong class="color-phone-order-booking"><?php echo ot_get_option('phone_office'); ?><?php echo (ot_get_option('phone_mobile') != '' ? ' - '.$phoneLast : ''); ?></strong>.</p>
                    <?php if(!empty($result['email'])){ ?>
                    <p>Chi tiết thông tin chuyến bay và mã vé sẽ được gửi tới email: <strong><?= $result['email'] ?></strong></p>
                    <?php } else { ?>
                    <p>Chi tiết thông tin chuyến bay và mã vé sẽ được gửi tới số điện thoại: <strong><?= $result['phone'] ?></strong></p>
                    <?php } ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <!--.confirmbox-->
            <div class="row">
                <div id="printarea">
                    <?php
                        if($result['ticket_type'] == 2)
                        { #CHUYEN QUOC TE
                            ?>
                    <table class="field-table">
                        <tr>
                            <td>Mã đơn hàng:</td>
                            <td><strong><?= $result['name'] ?></strong></td>
                            <td>Trạng thái:</td>
                            <td><strong>Chưa xác nhận</strong></td>
                        </tr>
                        <tr>
                            <td width="15%">Chuyến bay:</td>
                            <td><strong><?= $way_flight_text ?></strong></td>
                            <td width="20%">Số hành khách:</td>
                            <td><strong><?= $adults ?> người lớn<?= $qty_children.$qty_infants?></strong></td>
                        </tr>
                        <!-- <tr>
                            <td>Ngày đi:</td>
                            <td><strong><?= $depart_date ?></strong></td>
                            <? if($way_flight == 0)
                                echo '<td>Ngày về:</td><td><strong>'.$return_date.'</strong></td>';
                                ?>
                        </tr> -->
                    </table>
                    <!--HANH TRINH CHUYEN BAY-->
                    <?php
                        /*echo '<pre>';
                        print_r($res_itinerary);
                        echo '</pre>';*/
                        if($way_flight==1){
                            $dep_int=$res_itinerary;
                        }else{
                            foreach($res_itinerary as $ht){
                                if($ht['direction']==0)
                                    $dep_int[]=$ht;
                                else
                                    $ret_int[]=$ht;
                            }
                        }
                        $dep_source=$dep_int[0]['departure'];
                        ?>
                    <table class="field-table">
                        <tr>
                            <td colspan="4" class="go-icon"><i class="fa  fa-plane"></i> Chiều đi từ <strong><?= $dep_source ?></strong></td>
                        </tr>
                        <?php foreach($dep_int as $ht):
                            $dep_logo=get_stylesheet_directory_uri()."/images/inter_airline_icon/".$ht['airline_code'].".gif";
                            $dep_destination=$ht['arrival'];
                            $dep_source=$ht['departure'];
                            $dep_flightno=$ht['flight_number'];
                            $depart_date = date('d/m/Y', strtotime($ht['departure_date']));  		// Ngày đi
                            $arv_date   = date('d/m/Y', strtotime($ht['arrival_date']));// Ngày tới
                            $dep_deptime = date('H:i', strtotime($ht['departure_date']));  		// giờ đi đi
                            $dep_arvtime = date('H:i', strtotime($ht['arrival_date']));
                        ?>
                        <tr>
                            <td class="logo"><img src="<?= $dep_logo ?>"/> </td>
                            <td>
                                <b><?= $dep_source ?></b>
                                <p style="font-size:11px;"><?= $depart_date ?>, <b><?= $dep_deptime ?></b></p>
                            </td>
                            <td>
                                <b><?= $dep_destination ?></b>
                                <p style="font-size:11px;"><?= $arv_date ?>, <b><?= $dep_arvtime ?></b></p>
                            </td>
                            <td><?= $ht['airline_name'] ?> <br/>
                                Mã chuyến bay: <b><?= $dep_flightno ?></b>
                            </td>
                        </tr>
                        <?php
                            if($ht['description']!="") echo '<tr><td colspan="4"><p style="background: #FFECCA;text-align:center;padding:5px;">'.$ht['description'].'</p></td> </tr>';
                            endforeach;
                        ?>
                        <? if($way_flight == 0){ # 2 chieu ?>
                        <?php $ret_from=$ret_int[0]['departure']; ?>
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="back-icon">Chiều về từ <strong><?= $ret_from ?></strong></td>
                        </tr>
                        <?php foreach($ret_int as $ht):
                            $ret_logo=get_stylesheet_directory_uri()."/images/inter_airline_icon/".$ht['airline_code'].".gif";
                            $ret_destination=$ht['arrival'];
                            $ret_source=$ht['departure'];
                            $ret_flightno=$ht['flight_number'];
                            $return_date = date('d/m/Y', strtotime($ht['departure_date']));  		// Ngày đi
                            $arv_date   = date('d/m/Y', strtotime($ht['arrival_date']));// Ngày tới
                            $ret_deptime = date('H:i', strtotime($ht['departure_date']));  		// giờ đi đi
                            $ret_arvtime = date('H:i', strtotime($ht['arrival_date']));
                        ?>
                        <tr>
                            <td class="logo"><img src="<?= $dep_logo ?>"/> </td>
                            <td>
                                <b><?= $ret_source ?></b>
                                <p style="font-size:11px;"><?= $return_date ?>, <b><?= $ret_deptime ?></b></p>
                            </td>
                            <td>
                                <b><?= $ret_destination ?></b>
                                <p style="font-size:11px;"><?= $arv_date ?>, <b><?= $ret_arvtime ?></b></p>
                            </td>
                            <td><?= $ht['airline_name'] ?> <br/>
                                Mã chuyến: <b><?= $ret_flightno ?></b>
                            </td>
                        </tr>
                        <?php
                            if($ht['description']!="") echo '<tr><td colspan="4"><p style="background: #FFECCA;text-align:center;padding:5px;">'.$ht['description'].'</p></td> </tr>';
                            endforeach;
                            ?>
                        <? } ?>
                    </table>
                    <?php
                        }else{ #CHUYEN NOI DIA
                            ?>
                    <table class="field-table">
                        <tr>
                            <td class="csm-booking-order"><strong><?= $result['name'] ?></strong></td>
                            <td class="csm-info-flight-order hidden-xs"><strong><?= $way_flight_text ?></strong></td>
                            <td class="csm-quality-order"><strong><?= $adults ?> người lớn<?= $qty_children.$qty_infants?></strong></td>
                        </tr>
                    </table>
                    <table class="field-table hidden-xs">
                        <tr>
                            <td colspan="4" class="go-icon"><i class="fa fa-plane box-icon-border box-icon-left round box-icon-white box-icon-small"></i></i>Chiều đi từ <strong><?= $GLOBALS['CODECITY'][$dep_source] ?></strong></td>
                        </tr>
                        <tr>
                            <td class="logo <?= $dep_logo ?>"></td>
                            <td>
                                <b><?= $GLOBALS['CODECITY'][$dep_source] ?> (<?= $dep_source ?>)</b>
                                <p style="font-size:11px;"><?= $depart_date ?>, <b><?= $dep_deptime ?></b></p>
                            </td>
                            <td>
                                <b><?= $GLOBALS['CODECITY'][$dep_destination] ?> (<?= $dep_destination ?>)</b>
                                <p style="font-size:11px;"><?= $depart_date ?>, <b><?= $dep_arvtime ?></b></p>
                            </td>
                            <td>Mã chuyến: <b><?= $dep_flightno ?></b><br>Loại vé: <b><?= $dep_class ?></b></td>
                        </tr>
                        <? if($way_flight == 0){ ?>
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="back-icon"><i class="fa fa-plane box-icon-border box-icon-left round box-icon-white box-icon-small  fa-flip-horizontal"></i> Chiều về từ <strong><?= $GLOBALS['CODECITY'][$ret_source] ?></strong></td>
                        </tr>
                        <tr>
                            <td class="logo <?= $ret_logo ?>"></td>
                            <td>
                                <b><?= $GLOBALS['CODECITY'][$ret_source] ?> (<?= $ret_source ?>)</b>
                                <p style="font-size:11px;"><?= $return_date ?>, <b><?= $ret_deptime ?></b></p>
                            </td>
                            <td>
                                <b><?= $GLOBALS['CODECITY'][$ret_destination] ?> (<?= $ret_destination ?>)</b>
                                <p style="font-size:11px;"><?= $return_date ?>, <b><?= $ret_arvtime ?></b></p>
                            </td>
                            <td>Mã chuyến: <b><?= $ret_flightno ?></b><br>Loại vé: <b><?= $ret_class ?></b></td>
                        </tr>
                        <? } ?>
                    </table>
                    <div class="row mobile-info-custommer-dep hidden-sm hidden-md hidden-lg">
                        <div class="mobile-go-air mb-float-left">
                            <b><?= $GLOBALS['CODECITY'][$dep_source] ?></b>
                        </div>
                        <div class="mobile-images-arrows-right mb-float-left">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/arrow-right-black.png"/>
                            <b><?= $GLOBALS['CODECITY'][$dep_destination] ?></b>
                        </div>
                        <div class="mobile-go-date-air"><?= $depart_date ?></div>
                        <div class="mobile-logo-air">
                            <?php echo $dep_logo; ?>
                        </div>
                        <p class="mobile-name-aircode"><b><?= $dep_flightno ?></b></p>
                        <div class="mobile-dep-arv-time">
                            <b><?= $dep_deptime ?></b> - <b><?= $dep_arvtime ?></b>
                        </div>
                    </div></div>
                    <!--END MOBILE-->
                    <? if($way_flight == 0){ ?>
                        <!--MOBILE CHIỀU VỀ-->
                        <div class="row mobile-info-custommer-dep hidden-sm hidden-md hidden-lg">
                            <div class="mobile-go-air mb-float-left">
                                <b><?= $GLOBALS['CODECITY'][$ret_source] ?></b>
                            </div>
                            <div class="mobile-images-arrows-right mb-float-left">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/arrow-right-black.png"/>
                                <b><?= $GLOBALS['CODECITY'][$ret_destination] ?></b>
                            </div>
                            <div class="mobile-go-date-air"><?= $return_date ?></div>
                            <div class="mobile-logo-air">
                                <?php echo $ret_logo; ?>
                            </div>
                            <p class="mobile-name-aircode"><b><?= $ret_flightno ?></b></p>
                            <div class="mobile-dep-arv-time">
                                <b><?= $ret_deptime ?></b> - <b><?= $ret_arvtime ?></b>
                            </div>
                        </div></div>
                        <!--END MOBILE-->
                    <? } ?>    
                    <? } # END ELSE?>
                    <br>
                    <p><label style="display: inline-block;width: 145px;margin-right: 10px;">Tổng cộng: </label><span class="total-amount-text">
                        <? if($result['ticket_type']==2) echo format_price($total_amount,'USD');
                            else echo format_price($total_amount);?>
                        </span>
                    </p>
                    <label style="display: inline-block;width: 145px;margin-right: 10px;" class="mobile-width-order">Hình thức thanh toán:</label> <span style="font-weight:bold;font-size:16px;color:#59A800"><?= $payment_type ?></span>.</p>
                    <label style="display: inline-block;width: 145px;margin-right: 10px;" class="mobile-width-order">Trạng thái thanh toán:</label> <span style="font-weight:bold;font-size:16px;color:#59A800"><?= $is_paid; ?></span>.</p>
                </div>
            </div>
        </div>
        <!--#mainDisplay-->
    </div>
    <!--#mainleft-->
    <div id="ctright" class="col-md-4">
        <div class="wgbox mobile-margin-left-right-15">
            <?php get_sidebar(); ?>
        </div>
        <!--.wgbox-->
        <div class="clearfix"></div>
    </div>
    <!--#ctright-->
    <div class="clear"></div>
</div>
<!-- end .main -->
<?php get_footer(); ?>