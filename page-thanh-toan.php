<?
    $siteurl = get_bloginfo('siteurl');
    if((empty($_SESSION['dep']) && empty($_SESSION['interfinishflight'])) || empty($_SESSION['search'])){
        header('Location:'.$siteurl);
        exit();
    }
    if(!empty($_SESSION['booking'])){
        $bkid=$_SESSION['booking']['id'];
        if($_SESSION[$bkid]['saved']==true){
            header("Location:"._page("complete"));
            exit();
        }
    }
    
    if(isset($_POST['sm_transfer_method']) ||
        isset($_POST['sm_office_method']) ||
        isset($_POST['sm_home_method']) ||
        isset($_POST['sm_home_method']) ||
        isset($_POST['sm_nganluong_method'])) {
        $payment_type='';
        if(isset($_POST['sm_transfer_method'])) $payment_type=3;
        elseif(isset($_POST['sm_office_method'])) $payment_type=2;
        elseif(isset($_POST['sm_home_method'])) $payment_type=1;
        elseif(isset($_POST['sm_nganluong_method'])) $payment_type=4;
    
        $way_flight =$_SESSION['search']['way_flight'];
        $source = $_SESSION['search']['source'];
        $destination = $_SESSION['search']['destination'];
        $depart = $_SESSION['search']['depart'];   // dd/mm/yyyy
        $return = $_SESSION['search']['return'] ;
        $adults = $_SESSION['search']['adult'];
        $children = $_SESSION['search']['children'];
        $infants = $_SESSION['search']['infant'];
    
        require(TEMPLATEPATH.'/flight_config/sugarrest/sugar_rest.php');
            $sugar = new Sugar_REST();
            $error = $sugar->get_error();
            $booking_id = array();
    
        #NEU LA CHUYEN QUOC TE
        if($_SESSION['search']['international_flight'] && !empty($_SESSION['contact']) && !empty($_SESSION['int']['dep'])):
    
            $args_booking=array();
    
            /*Ghi Thong Tin Booking va Thong TIn Lien He*/
            $args_booking = $_SESSION['contact'];
            $args_booking['payment_type']=$payment_type;
    		$args_booking['ip_address']=get_ip_address_from_client();
    	  	$args_booking['user_agent']=$_SERVER['HTTP_USER_AGENT'];
            $booking_id = $sugar->set("EC_Flight_Bookings",$args_booking);
    
            /*Ghi Thong Tin Hanh khach*/
            foreach($_SESSION['pax'] as $pass){
                $passenger_arr=array();
                $passenger_arr=$pass;
                $passenger_arr['booking_id']= $booking_id['id'];
                $passenger_info[] = $sugar->set("EC_Booking_Passengers",$passenger_arr);
            }
    
            #GHI HANH TRINH CHUYEN DI
            foreach($_SESSION['int']['dep'] as $ht){
                $args_itinerary=array();
                $args_itinerary=$ht;
                $args_itinerary['booking_id']=$booking_id['id'];
                $itinerary_id_dep = $sugar->set("EC_Booking_Itineraries",$args_itinerary);
            }
    
            #GHI HANH TRINH CHUYEN VE
            if($way_flight==0):
                foreach($_SESSION['int']['ret'] as $ht){
                    $args_itinerary=array();
                    $args_itinerary=$ht;
                    $args_itinerary['booking_id']=$booking_id['id'];
                    $itinerary_id_ret = $sugar->set("EC_Booking_Itineraries",$args_itinerary);
                }
            endif;

            # LƯU CHI TIẾT ĐẶT VÉ - NGƯỜI LỚN
            if(!empty($_SESSION['detail']['adult'])){
                $arrticket_adult=array();
                $arrticket_adult=$_SESSION['detail']['adult'];
                $arrticket_adult['booking_id']=$booking_id['id'];
                $articket_adult_id =  $sugar->set("EC_Booking_Details",$arrticket_adult);
            }
    
            if(!empty($_SESSION['detail']['child'])){
                $arrticket_child=array();
                $arrticket_child=$_SESSION['detail']['child'];
                $arrticket_child['booking_id']=$booking_id['id'];
                $articket_adult_id =  $sugar->set("EC_Booking_Details",$arrticket_child);
            }
    
            if(!empty( $_SESSION['detail']['infant'])){
                $arrticket_infant=array();
                $arrticket_infant=$_SESSION['detail']['infant'];
                $arrticket_infant['booking_id']=$booking_id['id'];
                $articket_infant_id =  $sugar->set("EC_Booking_Details",$arrticket_infant);
            }

            $_SESSION['booking'] = $booking_id;
            $_SESSION[$booking_id['id']]['saved']=true;
            header("Location:"._page("complete"));
            exit();
    
        /*Neu la chuyen noi dia*/
        elseif( !empty($_SESSION['contact']) && !empty($_SESSION['dep_flight']) && !empty($_SESSION['pax']) ):
            $args_booking=array();
    
            /*Ghi Thong Tin Booking va Thong TIn Lien He*/
            $args_booking = $_SESSION['contact'];
            $args_booking['payment_type']=$payment_type;
    		$args_booking['ip_address']=get_ip_address_from_client();
    	  	$args_booking['user_agent']=$_SERVER['HTTP_USER_AGENT'];
            $booking_id = $sugar->set("EC_Flight_Bookings",$args_booking);
    
            /*Ghi Thong Tin Hanh Trinh Chuyen Di*/
            $args_itinerary=array();
            $args_itinerary = $_SESSION['dep_flight'];
            $args_itinerary['booking_id']=$booking_id['id'];
    
            $itinerary_id_dep = $sugar->set("EC_Booking_Itineraries",$args_itinerary);
            /*Ghi Hanh Trinh chuyen ve*/
            if($way_flight == 0){
                $args_itinerary_ret=array();
                $args_itinerary_ret=$_SESSION['ret_flight'];
                $args_itinerary_ret['booking_id']=$booking_id['id'];
                $itinerary_id_ret = $sugar->set("EC_Booking_Itineraries",$args_itinerary_ret);
            }
            // Lưu thông tin hành khách
            for($i=0;$i<count($_SESSION['pax']);$i++){
                $passenger_arr=array();
                $passenger_arr=$_SESSION['pax'][$i];
                $passenger_arr['booking_id']=$booking_id['id'];
                $passenger_info[]=$sugar->set("EC_Booking_Passengers",$passenger_arr);
            }
            #########LUOT DI############
            // Lưu chi tiết đặt vé - Nguoi Lớn
            $arrticket_adult=array();
            $arrticket_adult=$_SESSION['card']['dep']['adult'];
            $arrticket_adult['booking_id']=$booking_id['id'];
            $articket_adult_id =  $sugar->set("EC_Booking_Details",$arrticket_adult);
    
    
            if($children!=0){
                $arrticket_child=array();
                $arrticket_child=$_SESSION['card']['dep']['child'];
                $arrticket_child['booking_id']=$booking_id['id'];
                $articket_child_id =  $sugar->set("EC_Booking_Details",$arrticket_child);
            }
    
            if($infants != 0){
                $arrticket_inf=array();
                $arrticket_inf=$_SESSION['card']['dep']['infant'];
                $arrticket_inf['booking_id']=$booking_id['id'];
                $articket_inf_id=$sugar->set("EC_Booking_Details",$arrticket_inf);
            }
    
            if($way_flight == 0){
                $arrticket_adult_ret=array();
                $arrticket_adult_ret=$_SESSION['card']['ret']['adult'];
                $arrticket_adult_ret['booking_id']=$booking_id['id'];
                $articket_adult_id =  $sugar->set("EC_Booking_Details",$arrticket_adult_ret);
    
                if($children!=0){
                    $arrticket_child_ret=array();
                    $arrticket_child_ret=$_SESSION['card']['ret']['child'];
                    $arrticket_child_ret['booking_id']=$booking_id['id'];
                    $articket_child_id =  $sugar->set("EC_Booking_Details",$arrticket_child_ret);
                }
    
                if($infants != 0){
                    $arrticket_inf_ret=array();
                    $arrticket_inf_ret=$_SESSION['card']['ret']['infant'];
                    $arrticket_inf_ret['booking_id']=$booking_id['id'];
                    $articket_inf_id=$sugar->set("EC_Booking_Details",$arrticket_inf_ret);
                }
            }
    
            $args_booking_update=array();
            $args_booking_update=$_SESSION['card']['price'];
            $args_booking_update['id']=$booking_id['id'];
            $booking_update = $sugar->set("EC_Flight_Bookings",$args_booking_update);

            $_SESSION['booking'] = $booking_id;
            $_SESSION[$booking_id['id']]['saved']=true;
            header("Location:"._page("complete"));
            exit();
        endif;
    }
?>
<?php get_header(); ?>
<div class="main">
    <div class="payment col-md-8">
        <div class="row">
            <ul id="progressbar">
                <li class="pass"><a href="<?php echo _page("flightresult");  echo ($_SESSION["SSID"]["ID"])?("?SessionID=".$_SESSION["SSID"]["ID"]):""; ?>"><span class="hidden-xs">Chọn hành trình</span></a></li>
                <li class="pass"><a href="<?=_page("passenger")?>"><span class="hidden-xs">Thông tin hành khách</span></a></li>
                <li class="current"><span class="hidden-xs">Thanh toán</span></li>
                <li><span class="hidden-xs">Hoàn tất</span></li>
            </ul>
        </div>
        <p style="padding: 5px 0;line-height: 18px;font-size: 13px;">
            Sau khi chọn vui lòng nhấn <span class="ticket-book-text">"Đặt vé"</span>. Booker sẽ gọi đến xác thực thông tin book vé. Điều này là cần thiết nhằm tránh sai sót khi ra sân bay.
        </p>
        <!-- THANH TOAN CHUYỂN KHOẢN -->
        <form action="<?= _page("payment"); ?>" method="post" id="frm_selectpaymentmethod" class="mobile-info-pax mobile-margin-left-right-15">
            <div class="methods clearfix">
                <div class="methods-header transfer">
                    <label for="method_transfer" class="methods-header active">
                        <input checked="checked" type="radio" id="method_transfer" name="radio" />
                        <span class="text-transfer">Chuyển khoản</span>
                        <p class="note-text-transfer">Đây là hình thức thanh toán tối ưu nhất và không mất phí. Quý khách có thể chọn lựa tài khoản ngân hàng chuyển đến cùng hệ thống với cái đang sử dụng nhằm đảm bảo tiền được chuyển sang nhanh.</p>
                    </label>
                </div>
                <div class="methods-content clearfix" id="content_transfer" style="display: block;">
                    <p style="padding-left: 10px;" class="mobile-mothods-content-first">
                        Khi thực hiện chuyển khoản xin vui lòng ghi rõ mã đơn hàng, tên người liên hệ đặt vé hoặc số di động. Hoàn tất việc chuyển tiền đề nghị nhắn tin để xác thực : <span class="accountant-phone-text"><?php echo ot_get_option('accountant_phone'); ?></span> <br/>
                        Cú pháp tin nhắn:<br>
                        <b>"0989 456 789 da thanh toan"</b><br/>
                        <b>Bạn chỉ cần nhắn số ĐT</b>
                    </p>
                    <p>
                        <button type="submit" name="sm_transfer_method" class="button redcus pull-right mb30"> 
                        <span class="pull-left">Đặt vé </span>
                        <i class="fa fa-angle-right box-icon-border box-icon-right round box-icon-white box-icon-small"></i>
                        </button>
                    </p>
                </div>
            </div>
            <!--.methods-->
            <!-- THANH TOAN TẠI VĂN PHÒNG -->
            <div class="methods clearfix">
                <div class="methods-header office">
                    <label for="method_office" class="methods-header">
                        <input type="radio" id="method_office" name="radio" />
                        <span class="text-office"> Tại văn phòng</span> 
                        <p class="note-text-office">Sau khi đặt hàng thành công, quý khách có thể đến văn phòng ở địa chỉ <strong><?php echo ot_get_option('company_address'); ?></strong> để thanh toán và nhận vé.</p>
                    </label>
                </div>
                <div class="methods-content" id="content_office">
                    <div class="clearfix"></div>
                    <p>
                        <button type="submit"name="sm_office_method" class="button redcus pull-right mb30">
                        <span class="pull-left"> Đặt vé </span>
                        <i class="fa fa-angle-right box-icon-border box-icon-right round box-icon-white box-icon-small"></i>
                        </button>
                    </p>
                </div>
            </div>
            <!--.methods-->
            <!-- THANH TOAN TẠI NHÀ -->
            <div class="methods clearfix">
                <div class="methods-header home">
                    <label for="method_athome" class="methods-header">
                        <input type="radio" id="method_athome" name="radio" />
                        <span class="text-home">Tại nhà</span> 
                        <p class="note-text-home">Chúng tôi sẽ tiến hành in mặt vé, đi cùng phiếu thu số tiền phải trả của Quý khách. Đội giao vé nhanh sẽ liên hệ quý khách trong thời gian sớm nhất.</p>
                    </label>
                </div>
                <div class="methods-content" id="content_athome">
                    <p style="padding: 10px 0;" class="mobile-methods-content-athome">Thanh toán tại nhà chỉ áp dụng cho các quận huyện trong TP.HCM.</p>
                    <p><button type="submit"name="sm_home_method" class="button redcus  pull-right mb30"> <span class="pull-left">Đặt vé</span> <i class="fa fa-angle-right box-icon-border box-icon-right round box-icon-white box-icon-small"></i></button>
                    </p>
                </div>
            </div>
            <!--.method-->
        </form>
    </div>
    <!--#col-md-8-->
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