<?php
    if((!isset($_POST["selectflightdep"]) && empty($_SESSION["dep"])) || empty($_SESSION['search'])){
        wp_redirect(get_bloginfo("url"));
    }
    $siteurl=get_bloginfo("url");
    /*If booking successfuled, Redirect to successful page*/
    if(!empty($_SESSION['booking'])){
        $bkid=$_SESSION['booking']['id'];
        if($_SESSION[$bkid]['saved']==true){
            header("Location:".$siteurl."/xac-nhan-don-hang");
            exit();
        }
    }
    
    /*Gan Bien Flight*/
    $noflight=false;
    if(isset($_POST["selectflightdep"])){
        /*Check if selected flight exits, if not $nofight=true*/
        $depflightid=$_POST["selectflightdep"];
        if(!$_SESSION["result"][$depflightid]){
            $noflight=true;
        }
        $_SESSION["dep"]=(!$noflight)?$_SESSION["result"][$depflightid]:null;
    
        if($_SESSION["search"]["way_flight"]=="0"){
            $retflightid=$_POST["selectflightret"];
            if(!$_SESSION["result"][$retflightid]){
                $noflight=true;
            }
            $_SESSION["ret"]=(!$noflight)?$_SESSION["result"][$retflightid]:null;
        }
    }
    
    /*If inter flight*/
    if($_SESSION["search"]["isinter"]):
    
        exit();
    endif;
    
    
    
    $need = '<span style="color:#ED0000;font-weight:bold">*</span>';
    $attentionStart = '<span style="color:#ED0000;font-weight:bold">**</span>';
    $dem = 1;
    
    // ĐIỀU KIỆN TÌM KIÊM
    $way_flight = $_SESSION['search']['way_flight'];
    $source = $_SESSION['search']['source'];
    $destination = $_SESSION['search']['destination'];
    $depart = $_SESSION['search']['depart'];   // dd/mm/yyyy
    $return = $_SESSION['search']['return'] ;
    $adults = $_SESSION['search']['adult'];
    $children = $_SESSION['search']['children'];
    $infants = $_SESSION['search']['infant'];
    
    // CHUYẾN BAY ĐƯỢC CHỌN - LƯỢT ĐI
    $dep_airline = $_SESSION['dep']['airline'];
    $dep_deptime = $_SESSION['dep']['deptime'];
    $dep_arvtime = $_SESSION['dep']['arvtime'];
    $dep_flightno = $_SESSION['dep']['flightno'];
    $dep_price = $_SESSION['dep']['price'];
    $dep_class = $_SESSION['dep']['class'];
    $dep_stop = $_SESSION['dep']['stop'];
    $dep_note = $_SESSION['dep']['note'];
    
    /*LOGO Va Air Code*/
    if($dep_airline == 'vietnamairline'){
        $dep_logo = 'bg_vnal';
        $dep_aircode = 'VNA';
    }
    elseif($dep_airline == 'jetstar'){
        $dep_logo = 'bg_js';
        $dep_aircode = 'JET';
    }
    elseif($dep_airline == 'vietjetair'){
        $dep_logo = 'bg_vj';
        $dep_aircode = 'VJA';
    }
    elseif($dep_airline == 'bambooairways'){
        $dep_logo = 'bg_qh';
        $dep_aircode = 'BBA';
    }
    
    // CHUYẾN BAY ĐƯỢC CHỌN - LƯỢT VỀ
    if($way_flight == 0){
        $ret_airline = $_SESSION['ret']['airline'];
        $ret_deptime = $_SESSION['ret']['deptime'];
        $ret_arvtime = $_SESSION['ret']['arvtime'];
        $ret_flightno = $_SESSION['ret']['flightno'];
        $ret_price = $_SESSION['ret']['price'];
        $ret_class = $_SESSION['ret']['class'];
        $ret_stop = $_SESSION['ret']['stop'];
        $ret_note = $_SESSION['ret']['note'];
    
        if($ret_airline == 'vietnamairline'){
            $ret_logo = 'bg_vnal';
            $ret_aircode = 'VNA';
        }
        elseif($ret_airline == 'jetstar'){
            $ret_logo = 'bg_js';
            $ret_aircode = 'JET';
        }
        elseif($ret_airline == 'vietjetair'){
            $ret_logo = 'bg_vj';
            $ret_aircode = 'VJA';
        }
        elseif($ret_airline == 'bambooairways'){
            $ret_logo = 'bg_qh';
            $ret_aircode = 'BBA';
        }
    }
    
    ($children != 0) ? $qty_children = ', '.$children.' Trẻ em' : $qty_children = '';
    ($infants != 0) ? $qty_infants = ', '.$infants.' Trẻ sơ sinh' : $qty_infants = '';
    
    #DON GIA
    $qty_passenger = $adults + $children + $infants;
    $thue_adult = 0; 		// Thuế người lớn
    $phi_adult = 0;			// Phí người lớn
    $tong_adult = 0;    	// Tổng giá vé người lớn  ( Đã nhân với số lượng)
    
    $gia_child = 0;      	// Giá vé trẻ em
    $thue_child = 0;  		// Thuế trẻ em
    $phi_child = 0;			// Phí trẻ em
    $tong_child = 0;    	// Tổng giá trẻ em  (Đã nhân với số lượng)
    
    $gia_inf = 0;     		// Giá vé em bé
    $thue_inf = 0;			// Thuế em bé
    $phi_inf = 0;			// Phí em bé
    $tong_inf = 0;		 	// Tổng giá em bé  (Đã nhân với số lượng)
    
    $thue_adult_ret = 0; 	// Thuế người lớn	  						-- Lượt về
    $phi_adult_ret = 0; 	// Phí người lớn							-- Lượt vế
    $tong_adult_ret	= 0;	// Tổng giá vé người lớn					-- Lượt về
    
    $gia_child_ret = 0; 	// Giá vé trẻ em							-- Lượt về
    $thue_child_ret = 0;	// Thuế trẻ em								-- Lượt về
    $phi_child_ret = 0; 	// Phí trẻ em								-- Lượt về
    $tong_child_ret = 0; 	// Tổng giá trẻ em  (Đã nhân với số lượng)	-- Lượt về
    
    $gia_inf_ret = 0; 		// Giá vé em bé								-- Lượt về
    $thue_inf_ret = 0;		// Thuế em bé 								-- Lượt về
    $phi_inf_ret = 0; 		// Phí em bé								-- Lượt về
    $tong_inf_ret = 0;		// Tổng giá em bé  (Đã nhân với số lượng)	-- Lượt về
    
    /*POST PASSENGER*/
    if(count($_POST) && isset($_POST['sm_bookingflight']) && !empty($_SESSION['dep']) ){
    
        $_SESSION['contact']=array(
            'flight_type' => $way_flight,
            'airline' => $dep_aircode,
            'airline_inbound' => $ret_aircode,
    		'contact_title' => (int)$_POST['contact_title'],
            'contact_name' => ucwords(utf8convert(strtolower($_POST['contact_name']))),
            'email' => $_POST['contact_email'],
            'country' => $_POST['contact_country'],
            'phone' => $_POST['contact_phone'],
            'address' => utf8convert($_POST['contact_address']),
            'city' => $_POST['contact_city'],
            'description' => $_POST['special_request'],
        );
    
        // Luu thong tin hành trình - lượt đi
        $depart_arr = explode('/',$depart);
        $departure_date = $depart_arr[2].'-'.$depart_arr[1].'-'.$depart_arr[0].' '.$dep_deptime;
        $arrival_date = $depart_arr[2].'-'.$depart_arr[1].'-'.$depart_arr[0].' '.$dep_arvtime;
    
        $_SESSION['dep_flight']=array(
            'name' => $dep_flightno, //code.number
            'airline_code' => $dep_aircode,
            'flight_number' => $dep_flightno,
            'ticket_class' => $dep_class,
            'departure' => $source,
            'arrival' => $destination,
            'departure_date' => $departure_date,
            'arrival_date' => $arrival_date,
            'base_price' => $dep_price,
            'direction' => 0,
            'description' => $dep_note,
            'total_price' => NULL,
        );
    
    
    
        // Lưu thông tin hành trình - lượt về
        if($way_flight == 0){
    
            $return_arr = explode('/',$return);
            $retdeparture_date = $return_arr[2].'-'.$return_arr[1].'-'.$return_arr[0].' '.$ret_deptime;
            $retarrival_date = $return_arr[2].'-'.$return_arr[1].'-'.$return_arr[0].' '.$ret_arvtime;
    
            $_SESSION['ret_flight']=array(
                'name' => $ret_flightno, //code.number
                'airline_code' => $ret_aircode,
                'flight_number' => $ret_flightno,
                'ticket_class' => $ret_class,
                'departure' => $destination,
                'arrival' => $source,
                'departure_date' => $retdeparture_date,
                'arrival_date' => $retarrival_date,
                'base_price' => $ret_price,
                'direction' => 1,
                'description' => $ret_note,
                'total_price' => NULL,
            );
    
        }
        // Lưu thông tin hành khách
        for($i = 0; $i < $qty_passenger; $i++){
    
            $_SESSION['pax'][$i]=array(
                'type' => $_POST['passenger_type'][$i],
                'salutation' => $_POST['passenger_title'][$i],
                'name' => strtoupper(utf8convert(strtolower($_POST['passenger_name'][$i]))),
                'birthday' => $_POST['passenger_birthyear'][$i].'-'.$_POST['passenger_birthmonth'][$i].'-'.$_POST['passenger_birthday'][$i],
                'luggage_price' => $_POST['dep_addbaggage'][$i],
                'luggage_price_inbound' => $_POST['ret_addbaggage'][$i],
                'eticket_outbound' => '',
                'eticket_inbound' => '',
                'pnr_outbound' => '',
                'pnr_inbound' => '',
            );
    
        }
    
        ### Luot di ###
        // Lưu chi tiết đặt vé - Nguoi Lớn
        $detailprice=get_detail_price($dep_price,"adult",$dep_airline);
        $tax = $detailprice['tax'];
        $airportFee = $detailprice['airport_fee'];
        $serviceFee = $detailprice['fee'];
        $adminFee = $detailprice['admin_fee'];
    
        $tong_adult = ($dep_price + $tax + $airportFee + $serviceFee + $adminFee) * $adults;
    
    
        $_SESSION['card']['dep']['adult']=array(
            'direction' => 0,
            'name' => 'Người lớn',
            'passenger_type' => 0 ,
            'quantity' => $adults,
            'unit_price' => $dep_price,
            'tax_and_fee' => $tax,
            'airport_fee' => $airportFee,
            'admin_fee' => $adminFee,
            'service_fee' => $serviceFee,
            'total_price' => $tong_adult
        );
    
        #$hanhkhach .= $adults;
        // Lưu chi tiết đặt vé - Trẻ em
        if($children != 0){
            $detailprice=get_detail_price($dep_price,"child",$dep_airline);
            $gia_child = $detailprice['price'];
            $tax = $detailprice['tax'];
            $airportFee = $detailprice['airport_fee'];
            $serviceFee = $detailprice['fee'];
            $adminFee = $detailprice['admin_fee'];
            $tong_child = ($gia_child + $tax + $airportFee + $serviceFee +  $adminFee ) * $children;
    
            $_SESSION['card']['dep']['child']=array(
                'direction' => 0,
                'name' => 'Trẻ em',
                'passenger_type' => 1,
                'quantity' => $children,
                'unit_price' => $gia_child,
                'tax_and_fee' => $tax,
                'airport_fee' => $airportFee ,
                'admin_fee' => $adminFee,
                'service_fee' => $serviceFee,
                'total_price' => $tong_child
            );
    
        }
    
        if($infants != 0){
            $detailprice=get_detail_price($dep_price,"infant",$dep_airline);
            $gia_infant = $detailprice['price'];
            $tax = $detailprice['tax'];
            $airportFee = $detailprice['airport_fee'];
            $serviceFee = $detailprice['fee'];
            $adminFee = $detailprice['admin_fee'];
    
            $tong_inf = ($gia_infant + $tax + $airportFee + $serviceFee +  $adminFee ) * $infants;
    
            $_SESSION['card']['dep']['infant']=array(
                'direction' => 0,
                'name' => 'Trẻ sơ sinh',
                'passenger_type' => 2,
                'quantity' => $infants,
                'unit_price' => $gia_infant,
                'tax_and_fee' => $tax,
                'airport_fee' => $airportFee,
                'admin_fee' => $adminFee,
                'service_fee' => $serviceFee,
                'total_price' => $tong_inf
            );
    
        }
    
        ### Luot ve ###
        if($way_flight == 0){
    
            $detailprice=get_detail_price($ret_price,"adult",$ret_airline);
            $tax = $detailprice['tax'];
            $airportFee = $detailprice['airport_fee'];
            $serviceFee = $detailprice['fee'];
            $adminFee = $detailprice['admin_fee'];
    
            $tong_adult_ret = ($ret_price + $tax + $airportFee + $serviceFee + $adminFee ) * $adults;
    
            $_SESSION['card']['ret']['adult']=array(
                'direction' => 1,
                'name' => 'Người lớn',
                'passenger_type' => 0 ,
                'quantity' => $adults,
                'unit_price' => $ret_price,
                'tax_and_fee' => $tax,
                'airport_fee' => $airportFee,
                'service_fee' => $serviceFee,
                'admin_fee' => $adminFee,
                'total_price' => $tong_adult_ret
            );
    
            // Lưu chi tiết đặt vé - Trẻ em
            if($children != 0){
    
                $detailprice=get_detail_price($ret_price,"child",$ret_airline);
                $tax = $detailprice['tax'];
                $airportFee = $detailprice['airport_fee'];
                $serviceFee = $detailprice['fee'];
                $adminFee = $detailprice['admin_fee'];
                $gia_child_ret=$detailprice['price'];
    
                $tong_child_ret = ($gia_child_ret + $tax + $airportFee + $serviceFee + $adminFee) * $children;
    
                $_SESSION['card']['ret']['child']=array(
                    'direction' => 1,
                    'name' => 'Trẻ em',
                    'passenger_type' => 1,
                    'quantity' => $children,
                    'unit_price' => $gia_child_ret,
                    'tax_and_fee' => $tax,
                    'airport_fee' => $airportFee,
                    'service_fee' => $serviceFee,
                    'admin_fee' => $adminFee,
                    'total_price' => $tong_child_ret,
                );
    
            }
            if($infants != 0){
                $detailprice=get_detail_price($ret_price,"infant",$ret_airline);
                $tax = $detailprice['tax'];
                $airportFee = $detailprice['airport_fee'];
                $serviceFee = $detailprice['fee'];
                $adminFee = $detailprice['admin_fee'];
                $gia_inf_ret=$detailprice['price'];
    
                $tong_inf_ret = ($gia_inf_ret + $tax + $airportFee + $serviceFee +$adminFee) * $infants;
    
                $_SESSION['card']['ret']['infant']=array(
                    'direction' => 1,
                    'name' => 'Trẻ sơ sinh',
                    'passenger_type' => 2,
                    'quantity' => $infants,
                    'unit_price' => $gia_inf_ret,
                    'tax_and_fee' => $tax,
                    'airport_fee' => $airportFee,
                    'admin_fee' => $adminFee,
                    'service_fee' => $serviceFee,
                    'total_price' => $tong_inf_ret
                );
            }
        }
        # Giá tiền hành lý
        $dep_luggage = 0;
        $ret_luggage = 0;
        $luggage_fee = 0;
        $total_crm = 0;
    
        for($j = 0; $j < count($_POST['dep_addbaggage']); $j++){
            $dep_luggage += (int)($_POST['dep_addbaggage'][$j]);
        }
        for($k = 0; $k < count($_POST['ret_addbaggage']); $k++){
            $ret_luggage += (int)($_POST['ret_addbaggage'][$k]);
        }
        # Lưu giá tiền
        $subtotal = $tong_adult + $tong_child + $tong_inf + $tong_adult_ret + $tong_child_ret + $tong_inf_ret;
        $luggage_fee = $dep_luggage + $ret_luggage;
        $total_crm = $subtotal + $luggage_fee;
    
        $_SESSION['card']['price']=array(
            'subtotal_amount' => $subtotal,
            'luggage_fee' => $luggage_fee,
            'discount_percent' => '',
            'discount_amount' => 0 ,
            'total_amount' => $total_crm
        );
        # echo "<pre>";
        #print_r($_SESSION);
        header("Location: "._page('payment'));
        #echo "</pre>";
    
    }
    get_header();
?>
<div class="main">
    <div class="col-md-8">
        <div class="row">
            <ul id="progressbar" class="mobile-margin-bottom-progressbar">
                <li class="pass"><a href="<?php echo _page("flightresult");  echo ($_SESSION["SSID"]["ID"])?("?SessionID=".$_SESSION["SSID"]["ID"]):""; ?>"><span class="hidden-xs">Chọn hành trình</span></a></li>
                <li class="current"><span class="hidden-xs">Thông tin hành khách</span></li>
                <li><span class="hidden-xs">Thanh toán</span></li>
                <li><span class="hidden-xs">Hoàn tất</span></li>
            </ul>
        </div>
        <?php if($noflight===true): ?>
        <!--Redirect ve trang tim chuyen bay neu chuyen bay duoc chon ko ton tai-->
        <script type="text/javascript" language="javascript">
            $(document).ready(function(){
                $("#mainDisplay").html("<p style='color: red;padding: 20px 10px;'>Chuyến bay bạn chọn không tồn tại, trang web sẽ tự động quay lại trang tìm chuyến bay.</p>")
                setTimeout(function () {
                    window.location.href = "<?=_page("flightresult")?>";
                }, 2000);
            })      
        </script>
        <?php endif; /*End no flight*/ ?>
        <form action="" method="post" id="frmBookingFlight">
            <div class="passsenger mobile-margin-left-right-15">
                <h3 class="title hidden-xs">Thông tin chuyến bay</h3>
                <div class="field-table" width="100%">
                    <div class="row hidden-xs">
                        <div class="col-md-6 col-sm-6 col-xs-12"><label>Chuyến bay :  
                            <strong><?= $GLOBALS['way_flight_list'][$_SESSION['search']['way_flight']] ?></strong></label>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12"><label>Số lượng :  
                            <strong><?= $adults ?> người lớn<?= $qty_children.$qty_infants?></strong></label>
                        </div>
                    </div>
                    <div class="row hidden-xs">
                        <div class="col-md-6 col-sm-6 col-xs-12"><label>Ngày đi : <strong><?= $depart ?></strong></label></div>
                        <? if($way_flight == 0)
                            echo '<div class="col-md-6 col-sm-6 col-xs-12"><label>Ngày về : <strong>'.$return.'</strong></label></div>';
                        ?>
                    </div>
                </div>
                <div class="field-table" width="100%">
                    <!--Thong Tin Chang Di-->
                    <div class="label-departure row hidden-xs">
                        <div ><i class="fa fa-plane box-icon-border box-icon-left round box-icon-white box-icon-small"></i>Chiều đi&nbsp;&nbsp;&nbsp;&nbsp; 
                            <?= $GLOBALS['CODECITY'][$source] ?> <i class="fa fa-long-arrow-right"></i>&nbsp;&nbsp;<?= $GLOBALS['CODECITY'][$destination] ?> 
                        </div>
                    </div>
                    <div class="row config-info-padding-top hidden-xs">
                        <div class="col-md-2 col-sm-2 col-xs-12 <?= $dep_logo ?>"></div>
                        <div class="col-md-3 col-sm-3 col-xs-6">
                            <b><?= $GLOBALS['CODECITY'][$source] ?> (<?= $source ?>)</b>
                            <p ><?= $depart ?>, <b><?= $dep_deptime ?></b></p>
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-6">
                            <b><?= $GLOBALS['CODECITY'][$destination] ?> (<?= $destination ?>)</b>
                            <p ><?= $depart ?>, <b><?= $dep_arvtime ?></b></p>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-12">Mã chuyến: <b><?= $dep_flightno ?></b><br />Loại vé: <b><?= $dep_class ?></b></div>
                    </div>
                    <!--MOBILE-->
                    <div class="row mobile-info-custommer-dep hidden-sm hidden-md hidden-lg">
                        <div class="mobile-go-air mb-float-left">
                            <b><?= $GLOBALS['CODECITY'][$source] ?></b>
                        </div>
                        <div class="mobile-images-arrows-right mb-float-left">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/arrow-right-black.png"/>
                            <b><?= $GLOBALS['CODECITY'][$destination] ?></b>
                        </div>
                        <div class="mobile-go-date-air"><?= $depart ?></div>
                        <div class="mobile-logo-air">
                            <?php 
                                $xhtml = '';
                                switch($dep_airline){
                                    case "vietnamairline":
                                        $xhtml.= '<img src="'.get_template_directory_uri().'/images/airline-icons/smVN.png"';
                                        break;
                                    case "jetstar":
                                        $xhtml.= '<img src="'.get_template_directory_uri().'/images/airline-icons/smBL.png"';
                                        break;
                                    case "vietjetair":
                                        $xhtml.= '<img src="'.get_template_directory_uri().'/images/airline-icons/smVJ.png"';
                                        break;
                                    case "bambooairways":
                                        $xhtml.= '<img src="'.get_template_directory_uri().'/images/airline-icons/smQH.png"';
                                        break;
                                    default:
                                        break;
                                }
                                echo $xhtml;
                            ?>
                        </div>
                        <p class="mobile-name-aircode"><b><?= $dep_flightno ?></b></p>
                        <div class="mobile-dep-arv-time">
                            <b><?= $dep_deptime ?></b> - <b><?= $dep_arvtime ?></b>
                        </div>
                    </div></div>
                    <!--END MOBILE-->           
                    <? if($dep_note != ''){ ?>
                    <div class="row">
                        <div style="background:#F4F9FD;border:1px dotted #CCC;text-align:center"><?= $dep_note; ?></div>
                    </div>
                    <? } ?>
                    <? if($way_flight == 0){ ?>
                    <div  class="label-return row hidden-xs">
                        <div><i class="fa fa-plane box-icon-border box-icon-left round box-icon-white box-icon-small"></i>Chiều về&nbsp;&nbsp;&nbsp;&nbsp; 
                            <?= $GLOBALS['CODECITY'][$destination] ?> <i class="fa fa-long-arrow-right"></i>&nbsp;&nbsp;<?= $GLOBALS['CODECITY'][$source] ?>
                        </div>
                    </div>
                    <div class="row config-info-padding-top hidden-xs">
                        <div class="col-md-2 col-sm-2 col-xs-12 <?= $ret_logo ?>"></div>
                        <div class="col-md-3 col-sm-3 col-xs-6">
                            <b><?= $GLOBALS['CODECITY'][$destination] ?> (<?= $destination ?>)</b>
                            <p><?= $return ?>, <b><?= $ret_deptime ?></b></p>
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-6">
                            <b><?= $GLOBALS['CODECITY'][$source] ?> (<?= $source ?>)</b>
                            <p><?= $return ?>, <b><?= $ret_arvtime ?></b></p>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-12">Mã chuyến: <b><?= $ret_flightno ?></b><br />Loại vé: <b><?= $ret_class ?></b></div>
                    </div>
                    <!--MOBILE-->
                    <div class="row mobile-info-custommer-dep hidden-sm hidden-md hidden-lg">
                        <div class="mobile-go-air mb-float-left">
                            <b><?= $GLOBALS['CODECITY'][$destination] ?></b>
                        </div>
                        <div class="mobile-images-arrows-right mb-float-left">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/arrow-right-black.png"/>
                            <b><?= $GLOBALS['CODECITY'][$source] ?></b>
                        </div>
                        <div class="mobile-go-date-air"><?= $return ?></div>
                        <div class="mobile-logo-air">
                            <?php 
                                $xhtml = '';
                                switch($ret_airline){
                                    case "vietnamairline":
                                        $xhtml.= '<img src="'.get_template_directory_uri().'/images/airline-icons/smVN.png"';
                                        break;
                                    case "jetstar":
                                        $xhtml.= '<img src="'.get_template_directory_uri().'/images/airline-icons/smBL.png"';
                                        break;
                                    case "vietjetair":
                                        $xhtml.= '<img src="'.get_template_directory_uri().'/images/airline-icons/smVJ.png"';
                                        break;
                                    case "bambooairways":
                                        $xhtml.= '<img src="'.get_template_directory_uri().'/images/airline-icons/smQH.png"';
                                        break;
                                    default:
                                        break;
                                }
                                echo $xhtml;
                            ?>
                        </div>
                        <p class="mobile-name-aircode"><b><?= $ret_flightno ?></b></p>
                        <div class="mobile-dep-arv-time">
                            <b><?= $ret_deptime ?></b> - <b><?= $ret_arvtime ?></b>
                        </div>
                    </div></div>
                    <!--END MOBILE-->
                    <? if($ret_note != ''){ ?>
                    <div style="background:#F4F9FD;border:1px dotted #CCC;text-align:center"><?= $ret_note?></div>
                    <? } ?>
                    <? } ?>
                </div>
                <!--<fieldset>-->
                <!--    <legend><strong>Điều kiện vé</strong></legend>-->
                <!--    <table class="field-table">-->
                <!--        <tr><td colspan="2" class="go-icon"><b style="font-size:14px;">Lượt đi</b></td></tr>-->
                <!--        --><?php //getInfoTicket($dep_airline,$dep_class)?>
                <!--        --><?php //if($way_flight == 0) { ?>
                <!--        <tr><td colspan="2" class="back-icon"><b style="font-size:14px;">Lượt về</b></td></tr>-->
                <!--        --><?php //getInfoTicket($ret_airline, $ret_class)?>
                <!--        --><?php //} ?>
                <!--    </table>-->
                <!--</fieldset>-->
                <!-- THÔNG TIN HÀNH KHÁCH -->
                <div class="gap gap-mini"></div>
                <h3 class="title hidden-xs">Thông tin Hành khách</h3>
                <? // THÔNG TIN HÀNH KHÁCH : ADULT 
                    for($k=1; $k <= $adults; $k++){
                ?>
                <div class="mobile-info-pax">
                    <p class="float-left-pax-adult"><span class="pax-count"><?= $dem ?></span><strong>Người lớn</strong><b class="color-text-CMND">tên đúng với CMND</b></p>
                    <div class="field-table">
                        <div class="row">
                            <div class="col-md-3 col-sm-4 col-xs-4">
                                <div class="form-group">
                                    <!-- <label>Quý danh</label> <br/> -->
                                    <select name="passenger_title[]" class="form-control">
                                        <option value="0">Ông</option>
                                        <option value="1">Bà</option>
                                        <option value="0">Anh</option>
                                        <option value="1">Chị</option>
                                    </select>
                                    <input type="hidden" name="passenger_type[]" value="0" />
                                </div>
                            </div>
                            <div class="col-md-9 col-sm-8 col-xs-8">
                                <div class="form-group"> 
                                    <!-- <label>Họ và tên <?= $need ?><br />
                                    <span style="font-size:11px;color:#777;font-style:italic;">Ví dụ: NGUYEN VAN AN</span></label>  -->
                                    <input type="text" name="passenger_name[]" class="passenger_name form-control" placeholder="NGUYEN TUAN ANH">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12 date-of-birth-adult hidden-xs hidden-sm hidden-md hidden-lg">
                                <label>Ngày sinh<br /><span style="font-size:11px;color:#777;font-style:italic;">Ví dụ: 10/07/1989</span></label>
                                <div class="form-group row">
                                    <div class="col-md-4 col-sm-4 col-xs-4">
                                        <select name="passenger_birthday[]" class=" birthday form-control  ">
                                            <option value="0">Ngày</option>
                                            <?
                                                for($i = 1; $i <= 31; $i++)
                                                {
                                                    ?>
                                            <option value="<?= $i ?>" <? if($birthday[2] == $i) echo "selected"; ?> ><?= $i ?></option>
                                            <?
                                                }
                                                ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-4">
                                        <select name="passenger_birthmonth[]" class=" birthmonth form-control  ">
                                            <option value="0">Tháng</option>
                                            <?
                                                for($i = 1; $i <= 12; $i++)
                                                {
                                                    ?>
                                            <option value="<?= $i ?>" <? if($birthday[1] == $i) echo "selected"; ?> ><?= $i ?></option>
                                            <?
                                                }
                                                ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-4">
                                        <select name="passenger_birthyear[]" class=" birthyear form-control  ">
                                            <option value="0">Năm</option>
                                            <?
                                                (int)$youngest = (int)date('Y') - 15;
                                                (int)$oldest = (int)date('Y') - 85;
                                                for($i = $youngest; $i >= $oldest; $i--)
                                                {
                                                    ?>
                                            <option value="<?= $i ?>" <? if($birthday[0] == $i) echo "selected"; ?> ><?= $i ?></option>
                                            <?
                                                if($i % 10 == 0)
                                                    echo '<option value="0">------</option>';
                                                }
                                                ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="field-table" width="100%">
                        <tr>
                            <td colspan="2" style="height:10px;">
                                <p><strong>Hành lý nên mua thêm</strong></p>
                            </td>
                        </tr>
                        <?php Dep_addBaggage($dep_airline, $dep_class);?>
                        <tr>
                            <td width="20%">&nbsp;</td>
                            <td width="80%">&nbsp;</td>
                        </tr>
                        <?php if($way_flight == 0) { ?>
                        <tr>
                            <td colspan="2" style="height:10px;">
                                <p><strong>Hành lý lượt về</strong></p>
                            </td>
                        </tr>
                        <?php Ret_addBaggage($ret_airline, $ret_class);?>
                        <?php } ?>
                    </table>
                </div>
                <?
                    $dem++;
                    } // END FOR ADULTS
                    //ADULT
                    
                    // THÔNG TIN HÀNH KHÁCH: CHILDREN
                    for($k = 1; $k <= $children; $k++){
                    ?>
                <div>
                    <p><span class="pax-count"><?= $dem ?></span><strong>Trẻ em</strong></p>
                    <div class="field-table">
                        <!-- <div  style="font-size:12px;color:#777;font-style:italic;"><span style="color: red"> Lưu ý </span>: Tên hành khách vui lòng viết hoa, không dấu</div> -->
                        <div class="row">
                            <div class="col-md-2 col-sm-4 col-xs-4">
                                <div class="form-group">
                                    <!-- <label>Quý danh</label><br/> -->
                                    <select name="passenger_title[]" class="form-control">
                                        <option value="0">Trẻ em trai</option>
                                        <option value="1">Trẻ em gái</option>
                                    </select>
                                    <input type="hidden" name="passenger_type[]" value="1" />
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-8 col-xs-8">
                                <div class="form-group">
                                    <!-- <label>Họ và tên <?= $need ?><br /><span style="font-size:11px;color:#777;font-style:italic;">Ví dụ: NGUYEN VAN AN</span></label> -->
                                    <input type="text" name="passenger_name[]" class="passenger_name form-control" placeholder="NGUYEN PHUOC VINH"/>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <label>Ngày sinh <?= $need ?><br /><span style="font-size:11px;color:#777;font-style:italic;">Ví dụ: 10/07/1998</span></label>
                                <div class="form-group row">
                                    <div class="col-md-4 col-sm-4 col-xs-4">
                                        <select name="passenger_birthday[]" class="birthday form-control">
                                            <option value="0">Ngày</option>
                                            <?
                                                for($i = 1; $i <= 31; $i++)
                                                {
                                                    ?>
                                            <option value="<?= $i ?>" <? if($birthday[2] == $i) echo "selected"; ?> ><?= $i ?></option>
                                            <?
                                                }
                                                ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-4">
                                        <select name="passenger_birthmonth[]" class="birthmonth form-control">
                                            <option value="0">Tháng</option>
                                            <?
                                                for($i = 1; $i <= 12; $i++)
                                                {
                                                    ?>
                                            <option value="<?= $i ?>" <? if($birthday[1] == $i) echo "selected"; ?> ><?= $i ?></option>
                                            <?
                                                }
                                                ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-4">
                                        <select name="passenger_birthyear[]" class="birthyear form-control">
                                            <option value="0">Năm</option>
                                            <?
                                                (int)$youngest = (int)date('Y') - 3;
                                                (int)$oldest = (int)date('Y') - 18;
                                                for($i = $youngest; $i >= $oldest; $i--)
                                                {
                                                    ?>
                                            <option value="<?= $i ?>" <? if($birthday[0] == $i) echo "selected"; ?> ><?= $i ?></option>
                                            <?
                                                if($i % 10 == 0)
                                                    echo '<option value="0">------</option>';
                                                }
                                                ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="field-table" width="100%">
                        <tr>
                            <td colspan="2" style="height:10px;">
                                <p><strong>Hành lý <?php echo ($way_flight == 0 ? 'lượt đi' : '') ?></strong></p>
                            </td>
                        </tr>
                        <?php Dep_addBaggage($dep_airline, $dep_class);?>
                        <tr>
                            <td width="20%">&nbsp;</td>
                            <td width="80%">&nbsp;</td>
                        </tr>
                        <?php if($way_flight == 0) { ?>
                        <tr>
                            <td colspan="2" style="height:10px;">
                                <p><strong>Hành lý lượt về</strong></p>
                            </td>
                        </tr>
                        <?php Ret_addBaggage($ret_airline, $ret_class);?>
                        <?php } ?>
                    </table>
                </div>
                <?
                    $dem++;
                    }// END FOR CHILDREN
                    //CHILDREN
                    
                    //	THÔNG TIN HÀNH KHÁCH: INFANT
                    for($k = 1; $k <= $infants; $k++){
                    ?>
                <div>
                    <p><span class="pax-count"><?= $dem ?></span><strong>Em bé</strong></p>
                    <div class="field-table">
                        <!-- <div  style="font-size:12px;color:#777;font-style:italic;"><span style="color: red"> Lưu ý </span>: Tên hành khách vui lòng viết hoa, không dấu</div> -->
                        <div class="row">
                            <div class="col-md-2 col-sm-4 col-xs-4">
                                <div class="form-group">
                                    <!-- <label>Quý danh</label><br/> -->
                                    <select name="passenger_title[]" class="form-control">
                                        <option value="0">Em bé trai</option>
                                        <option value="1">Em bé gái</option>
                                    </select>
                                    <input type="hidden" name="passenger_type[]" value="2" />
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-8 col-xs-8">
                                <div class="form-group">
                                    <!-- <label>Họ và tên <?= $need ?><br /><span style="font-size:11px;color:#777;font-style:italic;">Ví dụ: NGUYEN VAN AN</span></label> -->
                                    <input type="text" name="passenger_name[]" class="passenger_name form-control" placeholder="NGUYEN MANH BAO"/>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <label>Ngày sinh <?= $need ?><br /><span style="font-size:11px;color:#777;font-style:italic;">Ví dụ: 10/07/2012</span></label>
                                <div class="form-group row">
                                    <div class="col-md-4 col-sm-4 col-xs-4">
                                        <select name="passenger_birthday[]" class="birthday form-control">
                                            <option value="0">Ngày</option>
                                            <?
                                                for($i = 1; $i <= 31; $i++)
                                                {
                                                    ?>
                                            <option value="<?= $i ?>" <? if($birthday[2] == $i) echo "selected"; ?> ><?= $i ?></option>
                                            <?
                                                }
                                                ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-4">
                                        <select name="passenger_birthmonth[]" class="birthmonth form-control">
                                            <option value="0">Tháng</option>
                                            <?
                                                for($i = 1; $i <= 12; $i++)
                                                {
                                                    ?>
                                            <option value="<?= $i ?>" <? if($birthday[1] == $i) echo "selected"; ?> ><?= $i ?></option>
                                            <?
                                                }
                                                ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-4">
                                        <select name="passenger_birthyear[]" class="birthyear form-control">
                                            <option value="0">Năm</option>
                                            <?
                                                (int)$youngest = (int)date('Y');
                                                (int)$oldest = (int)date('Y')-2;
                                                for($i = $youngest; $i >= $oldest; $i--)
                                                {
                                                    ?>
                                            <option value="<?= $i ?>" <? if($birthday[0] == $i) echo "selected"; ?> ><?= $i ?></option>
                                            <?
                                                if($i % 10 == 0)
                                                    echo '<option value="0">------</option>';
                                                }
                                                ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <? $dem++; } // END FOR INFANT ?>
                <!--THÔNG TIN LIÊN HỆ-->	
                <div class="gap gap-small hidden-xs"></div>
                <h3 class="title hidden-xs">Thông tin liên hệ</h3>
                <div class="mobile-info-pax">
                    <p style="margin:0 0 5px 0px;">(<?= $attentionStart ?>) Vui lòng cung cấp đầy đủ thông tin chi tiết liên hệ chính xác: họ tên, số ĐT chính, gmail</p>
                    <div class="err-info" id="err_info"></div>
                    <div class="field-table">
                        <div class="row">
                            <div class="col-md-2 col-sm-2 col-xs-4">
                                <div class="form-group">
                                    <label for="contact_title" style="font-weight:bold">Quý danh</label>   
                                    <select name="contact_title" id="contact_title" class="form-control">
                                        <option value="0">Ông</option>
                                        <option value="1">Bà</option>
                                        <option value="0">Anh</option>
                                        <option value="1">Chị</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5 col-sm-5 col-xs-8">
                                <div class="form-group">
                                    <label for="contact_name" style="font-weight:bold">Họ và tên <?= $need ?></label>
                                    <input type="text" name="contact_name" id="contact_name" class="form-control" placeholder="NGUYEN TUAN ANH"/> 
                                </div>
                            </div>
                            <div class="col-md-5 col-sm-5 col-xs-12">
                                <div class="form-group">
                                    <label for="contact_phone" style="font-weight:bold">Điện thoại di động <?= $need ?></label> 
                                    <input type="text" name="contact_phone" id="contact_phone" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div  class="col-md-7 col-sm-7 col-xs-12" >
                                <div class="form-group">
                                    <label for="contact_email" style="font-weight:bold">Gmail </label> 
                                    <input type="text" name="contact_email" id="contact_email" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-5 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="contact_address" style="font-weight:bold">Địa chỉ </label> 
                                    <input type="text" name="contact_address" id="contact_address" class="form-control" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <p id="err_info" class="line_error"></p>
                <div class="gap gap-small hidden-xs"></div>
                <h3 class="title hidden-xs">Yêu cầu đặc biệt</strong></h3>
                <p style="margin:0 0 10px 0px;">Khi bạn có thêm yêu cầu, hãy viết vào ô bên dưới.</p>
                <div class="field-table">
                    <div><textarea name="special_request"  class="form-control"></textarea></div>
                    <div class="row gap smal-gap mobile-small-gap">
                        <button type="submit" id="sm_bookingflight" name="sm_bookingflight" class="button redcus pull-right"  ><span class="pull-left"> Tiếp tục</span> <i class="fa fa-angle-right box-icon-border box-icon-right round box-icon-white box-icon-small"></i></button>
                    </div>
                    <div class="gap gap-small hidden-xs"></div>
                </div>
            </div>
            <!--end coleft-->
        </form>
    </div>
    <!--#col-md-8-->
    <div  class="col-md-4">
        <div class="wgbox mobile-margin-left-right-15">
            <div class="booking-item-payment mobile-info-pax" id="reviewprice">
                <div class="widgetblock-title"><span>Tóm tắt bảng giá</span></div>
                <div class="widgetblock-content">
                    <fieldset>
                        <legend>Lượt đi</legend>
                        <table class="field-table">
                            <tr>
                                <td colspan="5">
                                    <input type="hidden" id="wayflight" value="<?= $way_flight; ?>"  />
                                    <strong>Giá vé</strong>
                                </td>
                            </tr>
                            <tr class="calcuprice">
                                <td>Người lớn </td>
                                <td align="right"><b><?= $adults; ?></b> x </td>
                                <td style="text-align: right;"><?= format_price($dep_price);?></td>
                                <td style="padding-right:2px;padding-left:2px;text-align:center">=</td>
                                <td style="text-align: right;"><? $total_adults = $adults * $dep_price; echo format_price($total_adults);?></td>
                            </tr>
                            <?php if($children != 0) { ?>
                            <tr class="calcuprice">
                                <td>Trẻ em </td>
                                <td align="right"><b><?= $children; ?></b> x</td>
                                <td style="text-align: right;"><? $price_child = get_price_child($dep_price,$dep_airline); echo format_price($price_child); ?></td>
                                <td style="padding-right:2px;padding-left:2px;text-align:center">=</td>
                                <td style="text-align: right;"><? $total_child = $children * $price_child; echo format_price($total_child);?></td>
                            </tr>
                            <?php } ?>
                            <?php if($infants != 0) {?>
                            <tr class="calcuprice">
                                <td>Trẻ sơ sinh </td>
                                <td align="right"><b><?= $infants ?></b> x</td>
                                <td style="text-align: right;"><? $price_inf = get_price_infant($dep_price,$dep_airline); echo format_price($price_inf); ?></td>
                                <td style="padding-right:2px;padding-left:2px;text-align:center">=</td>
                                <td style="text-align: right;"><? $total_inf = $infants * $price_inf; echo format_price($total_inf); ?></td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <td colspan="5"><strong>Thuế và phí</strong></td>
                            </tr>
                            <tr class="calcuprice">
                                <td>Người lớn </td>
                                <td align="right"><b><?= $adults; ?></b> x </td>
                                <td style="text-align: right;"><? $tax_adults = getTaxFee_adult($dep_price,$dep_airline); echo format_price($tax_adults); ?></td>
                                <td style="padding-right:2px;padding-left:2px;text-align:center">=</td>
                                <td style="text-align: right;"><? $total_tax_adults = $adults * $tax_adults; echo format_price($total_tax_adults); ?></td>
                            </tr>
                            <?php if($children != 0) { ?>
                            <tr class="calcuprice">
                                <td>Trẻ em </td>
                                <td align="right"><b><?= $children; ?></b> x</td>
                                <td style="text-align: right;"><? $tax_child = getTaxFee_child($dep_price,$dep_airline); echo format_price($tax_child); ?></td>
                                <td style="padding-right:2px;padding-left:2px;text-align:center">=</td>
                                <td style="text-align: right;"><? $total_tax_child = $children * $tax_child; echo format_price($total_tax_child); ?></td>
                            </tr>
                            <?php } ?>
                            <?php if($infants != 0) {?>
                            <tr class="calcuprice">
                                <td>Trẻ sơ sinh </td>
                                <td align="right"><b><?= $infants; ?></b> x</td>
                                <td style="text-align: right;"><? $tax_inf = getTaxFee_infant($dep_price,$dep_airline); echo format_price($tax_inf); ?></td>
                                <td style="padding-right:2px;padding-left:2px;text-align:center">=</td>
                                <td style="text-align: right;"><? $total_tax_inf = $infants * $tax_inf; echo format_price($total_tax_inf);?></td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <td colspan="5" style="border-top:1px solid #DDD"></td>
                            </tr>
                            <tr>
                                <td colspan="2">Tổng giá</td>
                                <td colspan="3" style="text-align: right;"><b><? $total_price = $total_adults + $total_child + $total_inf + $total_tax_adults + $total_tax_child + $total_tax_inf; echo format_price($total_price);?></b></td>
                            </tr>
                            <tr>
                                <td colspan="2">Hành lý thêm</td>
                                <td colspan="3" style="text-align: right;"><span id="dep_pricebaggage">0</span> VND</td>
                            </tr>
                            <tr>
                                <td colspan="2">Giảm giá</td>
                                <td colspan="3" style="text-align: right;">- 0 VND</td>
                            </tr>
                            <tr>
                                <td colspan="5" style="border-top:1px solid #DDD;text-align: right">
                                    <p style="color:#555;font-size:14px;padding: 5px 0;">Lượt đi tổng cộng</p>
                                    <strong style="font-size:15px;"><span id="dep_total"><?= format_price_nocrc($total_price)?></span> VND</strong>
                                    <input type="hidden" id="hddeptotalprice" value="<?= (int)($total_price) ?>" />
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                    <?php if($way_flight == 0){?>
                    <fieldset>
                        <legend>Lượt về</legend>
                        <table class="field-table">
                            <tr>
                                <td colspan="5"><strong>Giá vé</strong></td>
                            </tr>
                            <tr class="calcuprice">
                                <td>Người lớn </td>
                                <td align="right"><b><?= $adults; ?></b> x </td>
                                <td style="text-align: right;"><?= format_price($ret_price);?></td>
                                <td style="padding-right:2px;padding-left:2px;text-align:center">=</td>
                                <td style="text-align: right;"><? $total_adults_ret = $adults * $ret_price; echo format_price($total_adults_ret);?></td>
                            </tr>
                            <?php if($children != 0) { ?>
                            <tr class="calcuprice">
                                <td>Trẻ em </td>
                                <td align="right"><b><?= $children; ?></b> x</td>
                                <td style="text-align: right;"><? $price_child_ret = get_price_child($ret_price,$ret_airline); echo format_price($price_child_ret); ?></td>
                                <td style="padding-right:2px;padding-left:2px;text-align:center">=</td>
                                <td style="text-align: right;"><? $total_child_ret = $children * $price_child_ret; echo format_price($total_child_ret);?></td>
                            </tr>
                            <?php } ?>
                            <?php if($infants != 0) {?>
                            <tr class="calcuprice">
                                <td>Trẻ sơ sinh </td>
                                <td align="right"><b><?= $infants ?></b> x</td>
                                <td style="text-align: right;"><? $price_inf_ret = get_price_infant($ret_price,$ret_airline); echo format_price($price_inf_ret); ?></td>
                                <td style="padding-right:2px;padding-left:2px;text-align:center">=</td>
                                <td style="text-align: right;"><? $total_inf_ret = $infants * $price_inf_ret; echo format_price($total_inf_ret); ?></td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <td colspan="5"><strong>Thuế và phí</strong></td>
                            </tr>
                            <tr class="calcuprice">
                                <td>Người lớn </td>
                                <td align="right"><b><?= $adults; ?></b> x </td>
                                <td style="text-align: right;"><? $tax_adults_ret = getTaxFee_adult($ret_price,$ret_airline); echo format_price($tax_adults_ret); ?></td>
                                <td style="padding-right:2px;padding-left:2px;text-align:center">=</td>
                                <td style="text-align: right;"><? $total_tax_adults_ret = $adults * $tax_adults_ret; echo format_price($total_tax_adults_ret); ?></td>
                            </tr>
                            <?php if($children != 0) { ?>
                            <tr class="calcuprice">
                                <td>Trẻ em </td>
                                <td align="right"><b><?= $children; ?></b> x</td>
                                <td style="text-align: right;"><? $tax_child_ret = getTaxFee_child($ret_price,$ret_airline); echo format_price($tax_child_ret); ?></td>
                                <td style="padding-right:2px;padding-left:2px;text-align:center">=</td>
                                <td style="text-align: right;"><? $total_tax_child_ret = $children * $tax_child_ret; echo format_price($total_tax_child_ret); ?></td>
                            </tr>
                            <?php } ?>
                            <?php if($infants != 0) {?>
                            <tr class="calcuprice">
                                <td>Trẻ sơ sinh </td>
                                <td align="right"><b><?= $infants; ?></b> x</td>
                                <td style="text-align: right;"><? $tax_inf_ret = getTaxFee_infant($ret_price,$ret_airline); echo format_price($tax_inf_ret); ?></td>
                                <td style="padding-right:2px;padding-left:2px;text-align:center">=</td>
                                <td style="text-align: right;"><b><? $total_tax_inf_ret = $infants * $tax_inf_ret; echo format_price($total_tax_inf_ret);?></b></td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <td colspan="5" style="border-top:1px solid #DDD"></td>
                            </tr>
                            <tr>
                                <td colspan="2">Tổng giá</td>
                                <td colspan="3" style="text-align: right;"><? $total_price_ret = $total_adults_ret + $total_child_ret + $total_inf_ret + $total_tax_adults_ret + $total_tax_child_ret + $total_tax_inf_ret; echo format_price($total_price_ret);?></td>
                            </tr>
                            <tr>
                                <td colspan="2">Hành lý thêm</td>
                                <td colspan="3" style="text-align: right;"><span id="ret_pricebaggage">0</span> VND</td>
                            </tr>
                            <tr>
                                <td colspan="2">Giảm giá</td>
                                <td colspan="3" style="text-align: right;">- 0 VND</td>
                            </tr>
                            <tr>
                                <td colspan="5" style="border-top:1px solid #DDD;text-align: right;">
                                    <p style="color:#555;font-size:14px;padding: 5px 0;">Lượt về tổng cộng</p>
                                    <strong style="font-size:15px;"><span id="ret_total"><?= format_price_nocrc($total_price_ret) ?></span> VND</strong>
                                    <input type="hidden" id="hdrettotalprice" value="<?= (int)$total_price_ret ?>" />
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                    <? } ?>
                    <div class="total">
                        <div class="cont">Tổng cộng</div>
                        <p><span id="amounttotal"><? $total = $total_price + $total_price_ret; echo format_price_nocrc($total);?></span> VND</p>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <!--#reviewprice-->
            <div class="gap gap-small hidden-xs"></div>
            <?php get_sidebar(); ?>
        </div>
        <!--.wgbox-->
    </div>
    <!--#ctright-->
    <div class="clear"></div>
</div>
<!--end .main -->
<?php get_footer(); ?>