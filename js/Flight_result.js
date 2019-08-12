var ArrayResult=new Array();
ArrayResult["count"]=0;

function getflight_info(vna,vj,js,notice,fromdate,todate){
    $('#loadresultfirst').html(notice);
    if(vna=='1') getresults(myvar.vna,fromdate,todate);
    if(vj=='1') getresults(myvar.vj,fromdate,todate);
    if(js=='1') getresults(myvar.js,fromdate,todate);
}

function getresults(airline,link,fromdate,todate){
    $.ajax({
        url: link,
        cache:false,
        traditional: true,
        type: "POST",
        data:"enCode="+SessionID,
        timeout:25000,
        dataType: "html",
        beforeSend: function () {
        },
        success: function(output){
            processResult(airline,output);
        },
        complete: function(){
            $(".waitflight").hide();
        },
        error: function(){
            CountActive--;
            if(CountActive==0 && ArrayResult.length==0){
                var emptyhtml=emptyflight();
                $("#result").html(emptyhtml);
            }
        }
    });
}

function processResult(data){
    //Xử Lý Add Row và hiện bản kết quả
	try{
		var org=JSON.parse(data);
		for(var data in org[0]){
			var airline="";
			if(org[0][data].airline=="Vietnam Airlines") airline="vna";
			if(org[0][data].airline=="Vietjet") airline="vj";
            if(org[0][data].airline=="Jetstar") airline="js";
            if(org[0][data].airline=="Bamboo Airways") airline="qh";
			
			var newrow=addrow(airline,org[0][data],0);
			$("#result #OutBound>tbody").append(newrow);
			ArrayResult[data]=org[0][data];
			ArrayResult["count"]++;
	
			if (!$("#frmfilterflight li." + airline).is(":visible")) {
                $("#frmfilterflight li." + airline).show();
            }
		}
	
		if(Direction==0){
			for(var data in org[1]){
				var airline="";
				if(org[1][data].airline=="Vietnam Airlines") airline="vna";
				if(org[1][data].airline=="Vietjet") airline="vj";
                if(org[1][data].airline=="Jetstar") airline="js";
                if(org[1][data].airline=="Bamboo Airways") airline="qh";
	
				var newrow=addrow(airline,org[1][data],1);
				$("#result #InBound>tbody").append(newrow);
				ArrayResult[data]=org[1][data];
				ArrayResult["count"]++;
				
				if (!$("#frmfilterflight li." + airline).is(":visible")) {
                    $("#frmfilterflight li." + airline).show();
                }
			}
		}
		
		if (ArrayResult["count"] > 0 && $('#loadresultfirst').is(":visible")) {
            $('#loadresultfirst').hide();
            $("#result").show();
            $("#flightsort").show();
            if (CountActive > 1) {
                if (!$("#filterflight").is(":visible"))
                    $("#filterflight").show();
            }
        }
	
		$("table.flightlist").trigger("update");
	
	} catch(err) {
		
        console.log("Error Log : " + err);
		
    } finally {

		CountActive--;
		if(CountActive==0 && ArrayResult["count"]==0){
			var emptyhtml=emptyflight();
			$("#mainDisplay").html(emptyhtml);
		}else if(CountActive==0 && ArrayResult["count"]>0){
			$("ul.date-picker").show();
			$("#sinfo .location").removeClass("contload");
		}
	
	}
    return false;
}

function getflight_inter(isdebug){

}

function parseDate(str) {
    var mdy = str.split('/');
    return new Date(mdy[2], mdy[1], mdy[0]);
}

function comparewithCurrentDate(str){
    var mdy = str.split('/');
    var x=new Date(mdy[2],mdy[1]-1,mdy[0],23,59,59);
    var today = new Date();
    if (x < today)
        return false;
    else
        return true;
}

function compareFromDatewithToDate(date1,date2){
    var mdydate1 = date1.split('/');
    var fromdate = new Date(mdydate1[2],mdydate1[1]-1,mdydate1[0]);

    var mdydate2 = date2.split('/');
    var todate = new Date(mdydate2[2],mdydate2[1]-1,mdydate2[0]);
    if(fromdate > todate)
        return false;
    else
        return true;
}

function CurrentDate(){
    var currentTime = new Date();
    var month = currentTime.getMonth() + 1;
    var day = currentTime.getDate();
    var year = currentTime.getFullYear();
    var strDate = day + "/" + month + "/" + year ;

    return strDate;
}

$(document).ajaxComplete(function() {
    if($("table.flightlist tbody>tr").length > 0){
        $.tablesorter.addParser({
            id: 'thousands',
            is: function(s) {
                return false;
            },
            format: function(s) {
                //return s.replace(' VND','').replace(/,/g,'');
                return s.replace(/[^0-9.]/g, "");
            },
            type: 'numeric'
        });
        $("table.flightlist").tablesorter({ 
            sortInitialOrder: 'desc',
            sortList: [[2,0]],
            headers:{
                2:{sorter:'thousands'},
                3:{sorter:false},
                4:{sorter:false}
            }
        });
    }
});

$(document).ready(function(){

    $("table.flightlist").live("sortStart",function(){
        $(".flight-detail").remove();
    })

    $(".rdsort").live("click",function(){
        $("#frmsoftflight li label").removeAttr("style");
        $(this).parent().find("label").attr("style","font-weight:bold;")
        sort_val=$(this).val();
        if(sort_val=="price")
            sorting = [[2,0]];
        if(sort_val=="airline")
            sorting = [[0,0]];
        if(sort_val=="time")
            sorting = [[1,0]];
        //alert(sort_val);
        $("table.flightlist").trigger("sorton",[sorting]);
    })

    $(".flightfilter").live("click",function(){
        $(".flight-detail").remove();
        cr_val=$(this).val();
        if(cr_val=="all"){
            if($(this).attr("checked")){
                $(".flightfilter").each(function(index){
                    $(this).attr("checked","checked");
                    $("table.flightlist .lineresult-main").show();

                })
            }else{
                $(".flightfilter").each(function(index){
                    $(this).removeAttr("checked");
                    $("table.flightlist .lineresult-main").hide();
                })
            }
        }else{
            if($(".flightfilter:checked").length==4){
                $("#filterall").attr("checked","checked");
                $("table.flightlist .lineresult-main").show();
            }else{
                $("#filterall").removeAttr("checked");

                if($(this).attr("checked")){
                    $(".lineresult-main."+cr_val).show();
                }else{
                    $(".lineresult-main."+cr_val).hide();
                }
            }
        }
    })

    $('#frm_requestflight').submit(function(){
        $(':submit', this).click(function() {
            return false;
        });
    });

    $('#sm_request').live("click",function(){
        if($('#fullname').val() == ''){
            $('#fullname').focus();
            return false;
        }else if($('#phone').val() == ''){
            $('#phone').focus();
            return false;
        }else{
            return true;
        }
    })


})


$( function() {
    $('tr.lineresult-main').live("click",function(){
        $(this).parents('table').find('tr').each( function( index, element ) {
            $(element).removeClass('marked');
        } );
        $(this).addClass('marked');
    });


    /***
     CHANGE DEPART DAY
     ***/
    $(".changedepartflight").click(function(){

        if($(this).parent('li').hasClass('disable') || $(this).parent('li').hasClass('active')){
            return false;
        }

        var departchange = $(this).attr('rel');
        var todate = Returndate;

        if(todate == '' || (todate != '' & compareFromDatewithToDate(departchange, todate)) ){
            generateform(departchange,Returndate);
            $("#frmchangedate").submit();
            return;
        }else{
            alert('Ngày khởi hành không được lớn hơn ngày về');
            return false;
        }

    });


    /***
     CHANGE RETURN DAY
     ***/
    $(".changereturnflight").click(function(){

        if($(this).parent('li').hasClass('disable') || $(this).parent('li').hasClass('active')){
            return false;
        }

        var fromdate = Departdate;
        var returnchange = $(this).attr('rel');
        if(compareFromDatewithToDate(fromdate,returnchange)){
            generateform(Departdate,returnchange);
            $("#frmchangedate").submit();
        }else{
            alert('Ngày về không được nhỏ hơn ngày khởi hành');
            return false;
        }
    });

    /***
     CHON CHUYEN BAY
     ***/
    $(".selectflight").live("click",function(){
        var direction=$(this).closest(".flightlist").attr("id");
        $("#"+direction+" .dep-active").removeClass("dep-active");
        var key = $(this).val();
        //$('a.viewdetail>i').removeClass('fa-minus-circle').addClass('fa-plus-circle');
        if($("#flightdetail"+key).length){

        }else{
            $("#"+direction+" .flight-detail").remove();
            $(this).closest("tr").after('<tr class="flight-detail" id="flightdetail'+key+'"></tr>');
            //$('a.viewdetail[rel="'+key+'"]>i').removeClass('fa-plus-circle').addClass('fa-minus-circle');
            showdetail(false,key,direction);
        }
    })

    /***
     XEM CHI TIET
     ***/
    $(".viewdetail").live("click",function(){
        var direction=$(this).closest(".flightlist").attr("id");
        $("#"+direction+" .dep-active").removeClass("dep-active");

        //$('a.viewdetail>i').removeClass('fa-minus-circle').addClass('fa-plus-circle');
        var keyactive = $(this).attr('rel');
        if($(this).hasClass("on")){
            /*Xoa cai khac di*/
            $("#"+direction+" .flight-detail").remove();
            $(this).removeClass("on");
        }else{
            $("#"+direction+" .flight-detail").remove();
            $("#"+direction+" .live").removeClass("on");
            $(this).addClass("on");
            $(this).closest("tr").after('<tr class="flight-detail" id="flightdetail'+keyactive+'"></tr>');
            //$('a.viewdetail[rel="'+keyactive+'"]>i').removeClass('fa-plus-circle').addClass('fa-minus-circle');
            showdetail(false,keyactive,direction);
        }
        return false;
    })

    /***
     CHECK SUBMIT
     <div class="noneselect">Bạn chưa chọn chuyến bay lượt đi hoặc lượt về</div>
     ***/
    $("#frmSelectFlight").submit(function(){
        var way_flight = Direction;
        if(way_flight == 1){
            if(!$('input[name="selectflightdep"]:checked').val())
            {
                $(".noneselect").text('Bạn chưa chọn chuyến bay');
                $(".noneselect").css('display', 'inline-block');
                $(".noneselect").fadeOut(2000);

                if($(window).width() >= 992 || $(window).width() <= 780) {
                    $('html, body').animate({
                        scrollTop: $("div.label-departure").offset().top
                    }, 2000);
                }
                
                if($(window).width() < 768) {
                    $('html, body').animate({
                        scrollTop: $(".mobile-label-departure").offset().top
                    }, 2000);
                }

                return false;
            }
        }
        else{
            if(!$('input[name="selectflightdep"]:checked').val())
            {
                $(".noneselect").text('Bạn chưa chọn chuyến bay lượt đi');
                $(".noneselect").css('display', 'inline-block');
                $(".noneselect").fadeOut(2000);

                if($(window).width() >= 992 || $(window).width() <= 780) {
                    $('html, body').animate({
                        scrollTop: $("div.label-departure").offset().top
                    }, 2000);
                }

                if($(window).width() < 768) {
                    $('html, body').animate({
                        scrollTop: $(".mobile-label-departure").offset().top
                    }, 2000);
                }

                return false;
            }
            if(!$('input[name="selectflightret"]:checked').val())
            {
                $(".noneselect").text('Bạn chưa chọn chuyến bay lượt về');
                $(".noneselect").css('display', 'inline-block');
                $(".noneselect").fadeOut(2000);

                if($(window).width() >= 992 || $(window).width() <= 780) {
                    $('html, body').animate({
                        scrollTop: $("div.label-return").offset().top
                    }, 2000);
                }
                
                if($(window).width() < 768) {
                    $('html, body').animate({
                        scrollTop: $(".mobile-label-arrvial").offset().top
                    }, 2000);
                }

                return false;
            }
        }
        for(i=0;i<XhrRequest.length;i++){
            if(XhrRequest[i] && XhrRequest[i].readystate != 4)
                XhrRequest[i].abort();
        }
        $("#result").hide();
        $("#mainDisplay").append('<p style="text-align: center;padding: 5px;">Quý khách vui lòng chờ trong giây lát..</p>')
        return true;
    });
});

function showdetail(isselect,flightid,direction){

    $("#flightdetail"+flightid).show();
    var rowdetail=addrowdetail("",ArrayResult[flightid]);

    $("#flightdetail"+flightid).html(rowdetail);

}

function addrow(airline,obj,direction){

    var sltname=(direction==0)?"selectflightdep":"selectflightret";
    logo_class="";
    if(airline == 'vna'){
        logo_class = 'bg_vnal';
    }
    else if(airline == 'js'){
        logo_class = 'bg_js';
    }
    else if(airline == 'vj'){
        logo_class = 'bg_vj';
    }
    else if(airline == 'qh'){
        logo_class = 'bg_qh';
    }

    var newrow='<tr class="lineresult-main '+airline+'"> \
					<td class="f_code '+logo_class+'"><span class="hidden-xs">'+obj.flightno+'</span></td> \
                    <td class="f_time">'+obj.deptime+' - '+obj.arvtime+'</td> \
                    <td class="f_price">'+formatNumber(obj.baseprice)+'<span class="hidden-xs">&nbsp;VND</span></td> \
                    <td class="f_detail"><a href="#" class="viewdetail" rel="'+obj.flightid+'"><span class="hidden-xs">Chi tiết</span></a> </td> \
                    <td class="f_select"> \
                        <div style="position:relative"> \
                             <label class="checkbox-custom-label control-label" for="selectflightret'+obj.flightid+'"><input type="radio" name="'+sltname+'" class="selectflight checkbox-custom" value="'+obj.flightid+'" id="selectflightret'+obj.flightid+'" /> \
                           <span>Chọn</span></label> \
                        </div>\
                    </td>\
                </tr>';
    return newrow;
}

function addrowdetail(airline,obj){

    var rowdetail=' <td colspan="5" class="flight-detail-content"> \
        <table class="table"> \
		<tr> \
        <td>Từ: <strong>'+obj.depcity+'</strong></td> \
        <td>Đến: <strong>'+obj.descity+'</strong></td> \
        </tr>\
        <tr>\
            <td>Sân bay: <strong>'+obj.depairport+'</strong></td>\
            <td>Sân bay: <strong>'+obj.desairport+'</strong></td>\
        </tr>\
        <tr>\
            <td>Thời gian: <strong>'+obj.deptime+'</strong>, '+obj.depdate+'</td>\
            <td>Thời gian: <strong>'+obj.arvtime+'</strong>, '+obj.arvdate+'</td>\
        </tr>\
		<tr> \
			<td>Số hiệu: <strong>'+obj.flightno+'</strong></td> \
			<td>Loại vé: '+obj.faretype+'</td>\
		</tr> \
    </table>';

    rowdetail+='<table class="table">\
            <thead>\
                <tr>\
                    <th style="text-align: left;">&nbsp;</th>\
                    <th style="text-align: center;"><span class="hidden-xs">Số lượng</span><span class="visible-xs">SL<span></th>\
                    <th style="text-align: right;">Giá vé</th>\
                    <th style="text-align: right;">Thuế phí</th>\
                    <th style="text-align: right;">Tổng</th>\
                </tr>\
            </thead>\
            <tbody>\
            <tr>\
                <td style="text-align: left;"><span class="hidden-xs">Người lớn</span><span class="visible-xs">Ng lớn<span></td>\
                <td style="text-align: center;">'+Adult+'</td>\
                <td style="text-align: right;">'+formatNumber(obj.baseprice)+' <span class="hidden-xs">VND</span></td>\
                <td style="text-align: right;">'+formatNumber(obj.adult.taxfee)+' <span class="hidden-xs">VND</span></td>\
                <td style="text-align: right;"><strong>'+formatNumber(obj.adult.total)+' <span class="hidden-xs">VND</span></strong></td>\
            </tr>';

        if(Child != 0){
            rowdetail+='<tr>\
                <td style="text-align: left;">Trẻ em</td>\
                <td style="text-align: center;">'+Child+'</td>\
                <td style="text-align: right;">'+formatNumber(obj.child.baseprice)+' <span class="hidden-xs">VND</span></td>\
                <td style="text-align: right;">'+formatNumber(obj.child.taxfee)+' <span class="hidden-xs">VND</span></td>\
                <td style="text-align: right;"><strong>'+formatNumber(obj.child.total)+' <span class="hidden-xs">VND</span></strong></td>\
            </tr>';
        }

        if(Infant != 0){
            rowdetail+='<tr>\
                <td style="text-align: left;">Em bé</td>\
                <td style="text-align: center;">'+Infant+'</td>\
                <td style="text-align: right;">'+formatNumber(obj.infant.baseprice)+' <span class="hidden-xs">VND</span></td>\
                <td style="text-align: right;">'+formatNumber(obj.infant.taxfee)+' <span class="hidden-xs">VND</span></td>\
                <td style="text-align: right;"><strong>'+formatNumber(obj.infant.total)+' <span class="hidden-xs">VND</span></strong></td>\
            </tr>';
        }
            rowdetail+='<tr>\
                <td colspan="5" style="text-align: right; font-weight: bold; font-size:1.2em;">Tổng cộng: <span style="color:#27AE60;">'+formatNumber(obj.subtotal)+' <span class="hidden-xs">VND</span></span></td>\
                </tr>\
               <tbody>\
              </table>\
        </td>';

    return rowdetail;
}

function emptyflight(){
    var html='<div class="empty_flight">\
        <h3>Chuyến bay bạn yêu cầu hiện tại đã hết !</h3>\
        <p><strong>Thông báo:</strong> chuyến bay khởi hành từ <strong>'+SourceCity+'</strong> đi <strong>'+DesCity+'</strong> trong ngày <strong>'+Departdate+'</strong> của các hãng hàng không trên hệ thông đặt vé online đã hết.</p>\
        <p>Bạn có thể thay đổi <strong>ngày đi</strong>, hoặc <strong>ngày về</strong> để tìm chuyến bay khác.</p>\
        <p>Nếu bạn muốn <strong>đặt vé máy bay theo yêu cầu</strong> trên, bạn có thể gửi yêu cầu theo <strong>biểu mẫu bên dưới</strong> hoặc gọi tới số điện thoại <strong style="font-size:16px;color:#E00;">'+Hotline+'</strong>. Nhân viên của chúng tôi sẽ <strong>tìm vé máy bay theo yêu cầu</strong> của bạn </p>\
        <div class="request_block">\
            <form method="post" action="" id="frm_requestflight">\
                <table>\
                    <caption>Đặt vé theo yêu cầu</caption>\
                    <tr>\
                        <td><label for="fullname">Họ tên:</label></td><td><input type="text" name="fullname" id="fullname" /></td>\
                    </tr>\
                    <tr>\
                        <td><label for="phone">Điện thoại:</label></td><td><input type="text" name="phone" id="phone" /></td>\
                    </tr>\
                    <tr>\
                        <td><label for="content_request">Nội dung:</label></td>\
                        <td><textarea name="content_request" id="content_request" style="height:80px;">Tôi muốn tìm vé cho chuyến bay từ '+SourceCity+' đi '+DesCity+' vào ngày '+Departdate+' cho '+PassengerText+' </textarea></td>\
                    </tr>\
                    <tr>\
                        <td></td><td><input type="submit" name="sm_request" id="sm_request" value="Gửi" class="btn btn-primary mt10"/></td>\
                    </tr>\
                </table>\
            </form>\
        </div>\
    </div>';
    return html;
}

function generateform(depdate,retdate){
    var htmlform='<input type="hidden" name="direction" value="'+Direction+'">\
                    <input type="hidden" name="dep" value="'+Source+'">\
                    <input type="hidden" name="des" value="'+Destination+'">\
                    <input type="hidden" name="depdate" value="'+depdate+'">\
                    <input type="hidden" name="retdate" value="'+retdate+'">\
                    <input type="hidden" name="adult" value="'+Adult+'">\
                    <input type="hidden" name="child" value="'+Child+'">\
                    <input type="hidden" name="infant" value="'+Infant+'">';
    $("#frmchangedate").append(htmlform);
}
	$(document).ready(function () {
		$(window).scroll(function () {
			var heightScroll = $(document).height() - $(window).height() - 400;
			var scrollTop = jQuery(window).scrollTop();
			if (scrollTop >= heightScroll) {
				$('#flightselectbt').removeClass('scrollDown');
				$('.moreScroll').text('');
			}
			else {
				$('#flightselectbt').addClass('scrollDown');
				$('.moreScroll').text('Kéo xuống để xem thêm kết quả');
			}
		});
	});