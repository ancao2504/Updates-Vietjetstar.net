<?php
    /**
     * Created by Notepad.
     * User: Lak
     * Date: 10/28/13
     */
    ?>
<?php
    date_default_timezone_set("Asia/Ho_Chi_Minh");
    $now = time();
    $ran = rand(99,999999);
    $expired_time = 0; // in seconds
    $enCode = md5(time().$ran."NpVietjetstarNet");
    $refer = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
    $domain = parse_url(get_bloginfo('url'), PHP_URL_HOST);
    
    //if ( (isset($_POST['btnsearch'.$_SESSION['fl_btn_search']]) || isset($_POST['btnchdate'.$_SESSION['fl_btn_chdate']]) || isset($_POST['wgbtnsearch'.$_SESSION['fl_wgbtn_search']])) && isset($_POST['dep']) && !isset($_GET["SessionID"]) && $refer == $domain ) {
    if ( isset($_POST[$_SESSION['fl_btn_search']])
        && !empty($_POST['dep']) 
        && !empty($_POST['des']) 
        && !empty($_POST['depdate']) 
        && empty($_GET['SessionID']) 
        && $refer == $domain ) {
    	
        $_SESSION["SSID"]=null;
        $_SESSION["search"]=null;
        $_SESSION["card"]=null;
        $_SESSION["result"]=null;
        $_SESSION['booking']=null;
        $_SESSION['dep']=null;
        $_SESSION['ret']=null;
        $_SESSION['contact']=null;
        $_SESSION['int']=null;
        $_SESSION['pax']=null;
        $_SESSION['dep_flight']=null;
        $_SESSION['ret_flight']=null;
    	$_SESSION['fl_captcha_ok']=null;
    	//$_SESSION['fl_token'] = null;
    	$_SESSION['fl_req_count'] = null;
       	$_SESSION['fl_req_count_allow'] = null;
    	//$_SESSION['fl_btn_search'] = null;
    	//$_SESSION['fl_wgbtn_search'] = null;
    	//$_SESSION['fl_btn_chdate'] = null;
    
        unset($_SESSION["SSID"]);
        unset($_SESSION["search"]);
        unset($_SESSION["card"]);
        unset($_SESSION["result"]);
        unset($_SESSION['booking']);
        unset($_SESSION['dep']);
        unset($_SESSION['ret']);
        unset($_SESSION['contact']);
        unset($_SESSION['int']);
        unset($_SESSION['pax']);
        unset($_SESSION['dep_flight']);
        unset($_SESSION['ret_flight']);
    	unset($_SESSION['fl_captcha_ok']);
    	//unset($_SESSION['fl_token']);
    	unset($_SESSION['fl_req_count']);
       	unset($_SESSION['fl_req_count_allow']);
    	//unset($_SESSION['fl_btn_search']);
    	//unset($_SESSION['fl_wgbtn_search']);
    	//unset($_SESSION['fl_btn_chdate']);
    
        $_SESSION["SSID"]["ID"]=$enCode;
        $_SESSION["SSID"][$enCode]['s']=array();
        $condition = array(
            'way_flight' => $_POST['direction'],
            'source' => $_POST['dep'],
            'destination' => $_POST['des'],
            'depart' => $_POST['depdate'],
            'return' => $_POST['retdate'],
            'adult' => $_POST['adult'],
            'children' => $_POST['child'],
            'infant' => $_POST['infant']
        );
    
        $isactive=checkactive($_POST['dep'],$_POST['des']);
        if($isactive['vj'] || $isactive['vna'] || $isactive['js'] || $isactive['qh']){
            if($isactive['vj']) $condition['active']['vj']=true; else $condition['active']['vj']=false;
            if($isactive['js']) $condition['active']['js']=true; else $condition['active']['js']=false;
            if($isactive['vna']) $condition['active']['vna']=true; else $condition['active']['vna']=false;
            if($isactive['qh']) $condition['active']['qh']=true; else $condition['active']['qh']=false;
        }
    
        if(!$GLOBALS['CODECITY'][$condition['source']] || !$GLOBALS['CODECITY'][$condition['destination']])
            $condition["isinter"]=true;
        else
            $condition["isinter"]=false;
    		
    	$expsearch = getexpsearch($condition["depart"]) + $expired_time;
        $_SESSION["SSID"][$enCode]['s']=$condition;
    	$_SESSION["SSID"][$enCode]['s']['exp']=$expsearch; // DDOS
        $_SESSION["search"]=$condition;
    
        //cached it
        /*S query file*/
        $file = dirname(__FILE__)."/flight_config/squery.json";
        $json = json_decode(file_get_contents($file),true);
        $exp=$expsearch;
        if($json==NULL || empty($json)){
            $squery=array();
            $squery[$enCode]=array();
            $squery[$enCode]=$condition;
            $squery[$enCode]["exp"]=$exp;
            file_put_contents($file, json_encode($squery));
        }else{
            $json[$enCode]=$condition;
            $json[$enCode]["exp"]=$exp;
            file_put_contents($file, json_encode($json));
        }
    
        header("Location: "._page("flightresult")."?SessionID=".$enCode);
    	exit;
    
       } elseif (isset($_GET["SessionID"]) && !isset($_POST['dep']) && !isset($_POST['sm_request'])) {
    
           $crssid=clearvar(trim($_GET["SessionID"]));
           $condition=array();
    
           if($_SESSION["SSID"][$crssid]){
               $condition = array(
                   'way_flight' => $_SESSION['search']['way_flight'],
                   'source' => $_SESSION['search']['source'],
                   'destination' => $_SESSION['search']['destination'],
                   'depart' => $_SESSION['search']['depart'],
                   'return' => $_SESSION['search']['return'],
                   'adult' => $_SESSION['search']['adult'],
                   'children' => $_SESSION['search']['children'],
                   'infant' => $_SESSION['search']['infant'],
                   'isinter' => $_SESSION['search']['isinter'],
                   'active'    => $_SESSION['search']['active']
               );
    		
    		// DDOS
    		$exp=$_SESSION["SSID"][$crssid]["s"]["exp"];
    		$diff=$now-$exp;
    		if($expired_time > 0 && $diff > $expired_time){
    			header("Location: ".get_bloginfo("url"));
    			exit;
    		}
    		
           }else{
               $file = dirname(__FILE__)."/flight_config/squery.json";
               $squery = json_decode(file_get_contents($file),true);
               if(empty($squery[$crssid])){
                   header("Location: ".get_bloginfo("url"));
    			exit;
               }else{
                   $condition=$squery[$crssid];
                   $_SESSION["SSID"]["ID"]=$crssid;
                   $_SESSION["SSID"][$crssid]['s']=$condition;
                   $_SESSION["search"]=$condition;
    			
    			// DDOS
    			$exp=$condition["exp"];
    			$diff=$now-$exp;
    			if($expired_time > 0 && $diff > $expired_time){
    				header("Location: ".get_bloginfo("url"));
    				exit;
    			}
               }
           }
    
           $direction=$condition['way_flight'];
           $source=$condition['source'];
           $destination=$condition['destination'];
           $direction_fulltext=($condition['way_flight']==1)?"Một chiều":"Khứ hồi";
           $adults=$condition['adult'];
           $depart_fulltext=$condition['depart'];
           $returndate_fulltext=$condition['return'];
           $child=$condition['children'];
           $infant=$condition['infant'];
           $passfulltext=$adults." người lớn";
           $passfulltext.=($child!=0)?", ".$child." Trẻ em":"";
           $passfulltext.=($infant!=0)?", ".$infant." Trẻ sơ sinh":"";
           $countactive=(($condition['active']['vna'])?1:0)+(($condition['active']['vj'])?1:0)+(($condition['active']['js'])?1:0)+(($condition['active']['qh'])?1:0);
           $arrlinkrs=array();
           if($condition['active']['vna']) $arrlinkrs[]=_page('vnalink');
           if($condition['active']['vj']) $arrlinkrs[]=_page('vjlink');
           if($condition['active']['js']) $arrlinkrs[]=_page('jslink');
           if($condition['active']['qh']) $arrlinkrs[]=_page('qhlink');
    	
           // Gen token
           if(empty($_SESSION['fl_token'])){
               $_SESSION['fl_token'] = gen_random_string(rand(9,18));
           }
    
    	// Reset request count
    	$_SESSION['fl_req_count'] = null;
    	$_SESSION['fl_req_count_allow'] = null;
    	unset($_SESSION['fl_req_count']);
    	unset($_SESSION['fl_req_count_allow']);
    
       } else {
           header("Location: ".get_bloginfo("url"));
    	exit;
       }
    
    // BEGIN LOG CLIENT REQUEST
    $ip_address = get_ip_address_from_client();
    $domain = parse_url(get_bloginfo('url'), PHP_URL_HOST);
    $req_content = $condition['way_flight'].$condition['source'].$condition['destination'].$condition['depart'].$condition['return'].$condition['adult'].$condition['children'].$condition['infant'];
    $req_content = preg_replace('/[^a-zA-Z0-9]/', '', $req_content);
    log_client_request($domain, $ip_address, $req_content);
    // END LOG CLIENT REQUEST
    
    get_header();
?>
<div class="main">
    <div class="col-md-8">
        <div class="row">
            <ul id="progressbar">
                <li class="current"><span class="hidden-xs">Chọn hành trình</span></li>
                <li><span class="hidden-xs">Thông tin hành khách<span></li>
                <li><span class="hidden-xs">Thanh toán</span></li>
                <li><span class="hidden-xs">Hoàn tất</span></li>
            </ul>
        </div>
        <?php
            if($condition)
            {
            // BEGIN CHECK CLIENT REQUEST
            $req_count_allow = 19; // requests
            $req_time_allow = 1800; // in seconds
            $req_count = (int)check_client_request($domain, $ip_address, $req_time_allow);
            $_SESSION['fl_req_count'] = $req_count;
                $_SESSION['fl_req_count_allow'] = $req_count_allow;
            //$geoip_country_code = getenv(GEOIP_COUNTRY_CODE);
            
            //if(!$ip_address || strtoupper($ip_address) == 'UNKNOWN' || ($geoip_country_code !== 'VN' && !$_SESSION['fl_captcha_ok']) || (checkCIDRBlacklist($ip_address) && !$_SESSION['fl_captcha_ok']) || ($req_count > $req_count_allow && !$_SESSION['fl_captcha_ok'])) {
            if(!$ip_address || strtoupper($ip_address) == 'UNKNOWN' || (checkCIDRBlacklist($ip_address) && !$_SESSION['fl_captcha_ok']) || ($req_count > $req_count_allow && !$_SESSION['fl_captcha_ok'])) {
            
            $_SESSION['fl_captcha_ok'] = false;
            $_SESSION['fl_captcha'] = simple_php_captcha(array('characters' => '0123456789'));
            include_once(TEMPLATEPATH."/tplpart-captchaform.php");
            
            }
            else {
            
            /*Neu tu search form*/
            ($condition['children'] != 0) ? $qty_children = ', '.$condition['children'].' Trẻ em' : $qty_children = '';
            ($condition['infant'] != 0) ? $qty_infants = ', '.$condition['infant'].' Trẻ sơ sinh' : $qty_infants = '';
            
            if($condition['way_flight'] == 0 && $condition['return'] != '')
            $str_return = 'Ngày về:</td><td><strong>'.$condition['return'].'</strong>';
            else
            $str_return = '</td><td>';
            
            # KIỂM TRA NẾU LÀ CHUYẾN NỘI ĐỊA
            if(!$condition["isinter"]){
            $waiting_notices = '<div class="waiting_block"><h2>Từ <span class="fontplace">'.$GLOBALS['CODECITY'][$condition['source']].'</span> đi <span class="fontplace">'.$GLOBALS['CODECITY'][$condition['destination']].'</span></h2><table><tr><td>Loại vé:</td><td><strong>'.$GLOBALS['way_flight_list'][$condition['way_flight']].'</strong></td><td>Số hành khách:</td><td><strong>'.$condition['adult'].' người lớn'.$qty_children.$qty_infants.'</strong></td></tr><tr><td>Ngày đi:</td><td><strong>'.$condition['depart'].'</strong></td><td>'.$str_return.'</td></tr></table><p class="notice-waiting">Quý khách vui lòng chờ trong giây lát ...</p></div>';
            if(!$condition['active']['vna'] && !$condition['active']['js'] && !$condition['active']['vj'] && !$condition['active']['qh']){ /*Neu duong bay ko co tuyen nay*/
            $isempty_flight=true;
            }else{
            ?>
        <script type="text/javascript">
            var SessionID='<?php echo $crssid?>';
            var Direction=<?php echo $direction?>;
            var DirectionText='<?php echo $direction_fulltext?>';
            var Source='<?php echo $source?>';
            var Destination='<?php echo $destination?>';
            var SourceCity='<?php echo $GLOBALS['CODECITY'][$source]?>';
            var DesCity='<?php echo $GLOBALS['CODECITY'][$destination]?>';
            var Departdate='<?php echo $condition['depart']?>';
            var Returndate='<?php echo $condition['return']?>';
            var Adult=<?php echo $adults?>;
            var Child=<?php echo $child?>;
            var Infant=<?php echo $infant?>;
            var PassengerText='<?php echo $passfulltext?>';
            var CountActive=<?php echo $countactive?>;
            var Hotline='<?php echo get_option("fl_phone") ?>';
            var Getrs=new Array(<?php echo "'".implode("','",$arrlinkrs)."'"; ?>);
            var XhrRequest=new Array();
            
            for(var i=0;i<Getrs.length;i++){
            
            	XhrRequest[i]=$.ajax({
            		url:Getrs[i],
            		cache:false,
            		traditional: true,
            		type: "POST",
            		data:"enCode="+SessionID+"&cache=<?php echo ($_GET["clearcache"])?0:1; ?>&<?php echo $_SESSION['fl_token']; ?>=",
            		timeout:45000,
            		dataType: "html"
            	}).done(function(data){
            			$(function(){
            				processResult(data);
            			})
            	}).error(function(){
            				CountActive--;
            				$(document).ready(function(){
            					if(CountActive==0 && ArrayResult['count']==0){
            						var emptyhtml=emptyflight();
            						$(document).ready(function(){$("#result").html(emptyhtml)});
            					}
            				})
            		})
            }
            // HIDDEN LOAD RESULFT FIRST
            //$(document).ready(function(){$("#loadresultfirst").html('<?=$waiting_notices?>')});
            
        </script>
        <?php
            }
            
            # ELSE LÀ VÉ QUỐC TẾ
            }else{
            $source_ia = getCityName($condition['source']);
            $destination_ia = getCityName($condition['destination']);
            
            $waiting_notices = '<div class="waiting_block"><h2>Khởi hành từ <span class="fontplace">'.$source_ia.'</span> đi <span class="fontplace">'.$destination_ia.'</span></h2><table><tr><td>Loại vé:</td><td><strong>'.$GLOBALS['way_flight_list'][$condition['way_flight']].'</strong></td><td>Số hành khách:</td><td><strong>'.$condition['adults'].' người lớn'.$qty_children.$qty_infants.'</strong></td></tr><tr><td>Ngày khởi hành:</td><td><strong>'.$condition['depart'].'</strong></td><td>'.$str_return.'</td></tr></table><p class="notice-waiting">Mời bạn vui lòng chờ trong giây lát ...</p></div>';
            ?>
        <script type="text/javascript">
            $(document).ready(function(){
            	getflight_inter(<?php echo $enCode ?>,'<?php echo $waiting_notices?>','','');
            })
        </script>
        <?php
            }
            
            } // END CHECK CLIENT REQUEST
            
                      } // END IF
                       elseif(isset($_POST['sm_request'])){ /*######If submit from request form#########*/ ?> 
        <?php
            if(isset($_POST['g-recaptcha-response'])){
              $captcha=$_POST['g-recaptcha-response'];
            
            }
            
            $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LcRggYTAAAAAH1j_07H0QBXeWbLQFK3Z9rvLfqQ&response=" .$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
            
            // print_r($captcha); echo "<br>";
              //print_r( $_SERVER['REMOTE_ADDR']);
            if ($response . success == false) {
            	echo 'Spam';
               // http_response_code(401); // It's SPAM! RETURN SOME KIND OF ERROR
            } else {
               // Everything is ok and you can proceed by executing your login, signup, update etc scripts
            	require(TEMPLATEPATH . '/flight_config/sugarrest/sugar_rest.php');
            	$sugar = new Sugar_REST();
            	$error = $sugar->get_error();
            	$arr_req = array(
            		'contact_name'   => trim($_POST['fullname']),
            		'phone'		  => trim($_POST['phone']),
            		'request_detail' => $_POST['content_request'],
            		'request_type'   => 3,
            		'request_status' => 0,
            	);
            	$req_id = $sugar->set("EC_Request_Flight",$arr_req);
            
            	if($req_id){
            		?>
        <div class="emptyflight_block">
            <h3>Yêu cầu của bạn đã được gửi thành công</h3>
            <p>Hệ thống đã nhận được yêu cầu của bạn! Nhân viên chúng tôi sẽ liên hệ lại với bạn trong vòng 5 phút.</p>
            <p>Cần trợ giúp bạn hãy gọi theo số <strong style="font-size:16px;color:#FE5815;"><?php echo  get_option('opt_phone');?></strong>.</p>
            <p style="color:#03F" ><a href="<?php bloginfo('siteurl');?>" >&laquo; Trở về trang chủ &raquo;</a></p>
        </div>
        <?php
            }else{
            	?>
        <div class="emptyflight_block">
            <h3>Gửi thất bại!</h3>
            <p>Bạn hãy liên hệ theo số <strong style="font-size:16px;color:#FE5815;"><?php echo  get_option('opt_phone'); ?></strong>, để được trợ giúp</p>
            <p style="color:#03F" ><a href="<?php bloginfo('siteurl');?>" >&laquo; Trở về trang chủ &raquo;</a></p>
        </div>
        <?php
            }
             
            }
            
            
                      }
                      else{
                          ?>
        <div class="emptyflight_block">
            <h3 class="noinfo">Vui lòng chọn Thông tin tìm kiếm chuyến bay</h3>
        </div>
        <?php
            }
            ?>
        <?php if($isempty_flight) include_once(TEMPLATEPATH."/tplpart-emptyflight.php"); ?>
        <div id="loadresultfirst"></div>
        <div id="result">
            <form action="<?php echo _page("passenger")?>" method="post" id="frmSelectFlight">
                <!--Thong Tin Chang Di-->
                <div class="field-table" width="100%">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12 hidden-xs">
                            <label>Chuyến bay : <strong><?= $direction_fulltext ?></strong></label>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12 hidden-xs">
                            <label>Số lượng : <strong><?= $adults ?> người lớn<?= $qty_children.$qty_infants ?></strong></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6 hidden-xs">
                            <label>Ngày đi : <strong><?= $depart_fulltext ?></strong></label>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6 hidden-xs">
                            <?php if($direction!=1){ ?>
                            <label>Ngày về : <strong><?= $returndate_fulltext ?></strong></label>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <!--Thong Tin Chang Di-->
                <div class="label-departure row hidden-xs">
                    <div >
                        <i class="fa fa-plane box-icon-border box-icon-left round box-icon-white box-icon-small"></i>
                        Chiều đi&nbsp;&nbsp;&nbsp;&nbsp;
                        <?= $GLOBALS['CODECITY'][$source] ?> <i class="fa fa-long-arrow-right"></i>&nbsp;&nbsp;<?= $GLOBALS['CODECITY'][$destination] ?> 
                    </div>
                </div>
                <div class="row mobile-label-departure hidden-sm hidden-md hidden-lg">
                    <div class="mobile-left-label-departure">
                        <?= $GLOBALS['CODECITY'][$source] ?> 
                        <i class="fa fa-long-arrow-right"></i>&nbsp;&nbsp;
                        <?= $GLOBALS['CODECITY'][$destination] ?>   
                    </div>
                    <div class="mobile-right-depart-date">
                        <strong><?= $depart_fulltext ?></strong>         
                    </div>        
                </div>
                <div class="row">
                    <ul class="row date-picker hidden-xs">
                        <?php echo date_of_currentdate($depart_fulltext, 0); ?>
                    </ul>
                    <ul class="row date-picker visible-xs">
                        <?php echo date_of_currentdate($depart_fulltext, 0, true); ?>
                    </ul>
                </div>
                <div class="row">
                    <table class="flightlist table" border="0" id="OutBound">
                        <thead class="hidden-xs">
                            <tr>
                                <th class="type-string sortairport">
                                    <span class="hidden-xs">Chuyến bay</span>
                                    <span class="visible-xs"><i class="fa fa-plane"></i></span>
                                </th>
                                <th class="type-string sorttime" >
                                    <span class="hidden-xs">Thời gian</span>
                                    <span class="visible-xs"><i class="fa fa-clock-o"></i></span>
                                </th>
                                <th class="type-string sortprice">
                                    <span class="hidden-xs">Giá vé</span>
                                    <span class="visible-xs"><i class="fa fa-money"></i></span>
                                </th>
                                <th>
                                    <span class="hidden-xs">Xem</span>
                                    <!--<span class="visible-xs"><i class="fa fa-angle-double-down"></i></span>-->
                                </th>
                                <th>
                                    <span class="hidden-xs">Chọn</span>
                                    <span class="visible-xs"><i class="fa fa-check"></i></span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <?php if(isset($_SESSION['search']['way_flight']) && $_SESSION['search']['way_flight'] == "0"){ ?>
                <div  class="label-return row hidden-xs">
                    <div><i class="fa fa-plane box-icon-border box-icon-left round box-icon-white box-icon-small"></i>Chiều về&nbsp;&nbsp;&nbsp;&nbsp; 
                        <?= $GLOBALS['CODECITY'][$destination] ?> <i class="fa fa-long-arrow-right"></i>&nbsp;&nbsp;<?= $GLOBALS['CODECITY'][$source] ?>
                    </div>
                </div>
                <div class="row mobile-label-arrvial hidden-sm hidden-md hidden-lg">
                    <div class="mobile-left-label-arrvial">
                        <?= $GLOBALS['CODECITY'][$destination] ?> 
                        <i class="fa fa-long-arrow-right"></i>&nbsp;&nbsp;
                        <?= $GLOBALS['CODECITY'][$source] ?>   
                    </div>
                    <div class="mobile-right-depart-date">
                        <strong><?= $returndate_fulltext ?></strong>         
                    </div>        
                </div>
                <div class="row">
                    <ul class="row date-picker hidden-xs">
                        <?php echo date_of_currentdate($returndate_fulltext, 1); ?>
                    </ul>
                    <ul class="row date-picker visible-xs">
                        <?php echo date_of_currentdate($returndate_fulltext, 1, true); ?>
                    </ul>
                </div>
                <div class="row">
                    <table class="flightlist table" border="0" id="InBound">
                        <thead class="hidden-xs">
                            <tr>
                                <th class="type-string sortairport">
                                    <span class="hidden-xs">Chuyến bay</span>
                                    <span class="visible-xs"><i class="fa fa-plane"></i></span>
                                </th>
                                <th class="type-string sorttime" >
                                    <span class="hidden-xs">Thời gian</span>
                                    <span class="visible-xs"><i class="fa fa-clock-o"></i></span>
                                </th>
                                <th class="type-string sortprice">
                                    <span class="hidden-xs">Giá vé</span>
                                    <span class="visible-xs"><i class="fa fa-money"></i></span>
                                </th>
                                <th>
                                    <span class="hidden-xs">Xem</span>
                                    <!--<span class="visible-xs"><i class="fa fa-angle-double-down"></i></span>-->
                                </th>
                                <th>
                                    <span class="hidden-xs">Chọn</span>
                                    <span class="visible-xs"><i class="fa fa-check"></i></span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <?php } ?>
                <div id="flightselectbt" class="mt30 continue-flight-result">
                    <span class="moreScroll visible-lg"></span>	
                    <button type="submit" id="sm_fselect" name="sm_fselect" class="button redcus pull-right"><span class="pull-left"> Tiếp tục</span> <i class="fa fa-angle-right box-icon-border box-icon-right round box-icon-white box-icon-small"></i></button>
                    <br/><span class="noneselect"></span>
                </div>
            </form>
            <form class="search-flight-form" name="changedate" method="post" action="<?= _page("flightresult") ?>" style="display: none !important;" id="frmchangedate"></form>
        </div>
        <div class="clearfix"></div>
    </div>
    <!-- #col-md-8 -->
    <div class="col-md-4">
        <div class="wgbox mobile-margin-left-right-15">
            <?php get_sidebar(); ?>
        </div>
    </div>
    <!-- #ctright -->
    <div class="clear"></div>
</div>
<!-- end main -->
<?php get_footer(); ?>