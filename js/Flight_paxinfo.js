/**
 * Created with JetBrains PhpStorm.
 * User: Lak
 * Date: 11/2/13
 * Time: 11:46 AM
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function() {
    $(".dep_addbaggage").change(function(){
        var priceBaggage = 0; // gia hanh ly luot di
        $(".dep_addbaggage").each(function(){
            priceBaggage += parseInt($(this).val());
        });

        $("#dep_pricebaggage").text(priceBaggage).formatCurrency({symbol:'',roundToDecimalPlace:0,digitGroupSymbol:','}); // in gia hanh ly luot di

        var deptotalprice = parseInt($("#hddeptotalprice").val()); // tong gia tien luot di
        var deptotalprice_incbag = priceBaggage + deptotalprice; //tong gia tien luot di bao gom tien hanh ly

        // in tong gia tien luot di
        $("#dep_total").text(deptotalprice_incbag).formatCurrency({symbol:'',roundToDecimalPlace:0,digitGroupSymbol:','});

        if($("#wayflight").val() == 0){

            var priceBaggage_ret = 0; // gia hanh ly luot ve
            $(".ret_addbaggage").each(function(){
                priceBaggage_ret += parseInt($(this).val());
            });

            var rettotalprice = parseInt($("#hdrettotalprice").val());// tong gia tien luot ve
            var rettotalprice_incbag = priceBaggage_ret + rettotalprice;

            $("#amounttotal").text((deptotalprice_incbag + rettotalprice_incbag)).formatCurrency({symbol:'',roundToDecimalPlace:0,digitGroupSymbol:','});
        }
        else{
            $("#amounttotal").text(deptotalprice_incbag).formatCurrency({symbol:'',roundToDecimalPlace:0,digitGroupSymbol:','});
        }
    });

    $(".ret_addbaggage").change(function(){
        var priceBaggage = 0; // gia hanh ly luot di
        $(".dep_addbaggage").each(function(){
            priceBaggage += parseInt($(this).val());
        });

        var deptotalprice = parseInt($("#hddeptotalprice").val()); // tong gia tien luot di
        var deptotalprice_incbag = priceBaggage + deptotalprice; //tong gia tien luot di bao gom tien hanh ly


        var priceBaggage_ret = 0; // gia hanh ly luot ve
        $(".ret_addbaggage").each(function(){
            priceBaggage_ret += parseInt($(this).val());
        });

        $("#ret_pricebaggage").text(priceBaggage_ret).formatCurrency({symbol:'',roundToDecimalPlace:0,digitGroupSymbol:'.'}); // in gia hanh ly luot ve

        var rettotalprice = parseInt($("#hdrettotalprice").val());// tong gia tien luot ve
        var rettotalprice_incbag = priceBaggage_ret + rettotalprice;

        // in tong gia tien luot ve
        $("#ret_total").text(rettotalprice_incbag).formatCurrency({symbol:'',roundToDecimalPlace:0,digitGroupSymbol:'.'});
        $("#amounttotal").text((deptotalprice_incbag + rettotalprice_incbag)).formatCurrency({symbol:'',roundToDecimalPlace:0,digitGroupSymbol:'.'});
    });

    $("#sm_bookingflight").click(function(){
        var error = 0;
        $(".passenger_name").each(function(index) {
            if($(this).val().length == ''){
                $(this).focus();
				$(this).css({"border":"1px solid #F00"});
                
                error++;
                return false;
            }else{
                $(".mini_err").remove();
            }
        });
        if(error > 0){
            return false;
        }
        var regEmail = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        var regEmailNew = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        var regexGmail = /(\W|^)[\w.+\-]*@gmail\.com(\W|$)/;
        var number = /^[0-9]+$/;
        var regexCheckPhone = /((09|03|07|08|05)+([0-9]{8})\b)/g;

        if($("#contact_name").val() == ''){
            $("#contact_name").css({"border":"1px solid #F00"});
            $("#contact_name").focus();
            $("#err_info").html('Vui lòng nhập Họ và tên.');
            $("#err_info").fadeIn();
            return false;
        }
        if($("#contact_phone").val().length < 9 || !$("#contact_phone").val().match(number) || !regexCheckPhone.test($("#contact_phone").val())){
            $("#contact_phone").css({"border":"1px solid #F00"});
            $("#contact_phone").focus();
            $("#err_info").html('Vui lòng nhập Số điện thoại chính xác.');
            $("#err_info").fadeIn();
            return false;
        }
        // if($("#contact_email").val() == '') {
        //     $("#contact_email").css({"border":"1px solid #F00"});
        //     $("#contact_email").focus();
        //     $("#err_info").html('Vui lòng nhập Email.');
        //     $("#err_info").fadeIn();
        //     return false;
        // }
        if($("#contact_email").val() == '') {
            return true;
        }
        if(regexGmail.test($("#contact_email").val()) == false){
            $("#contact_email").css({"border":"1px solid #F00"});
            $("#contact_email").focus();
            $("#err_info").html('Vui lòng nhập Gmail chính xác.');
            $("#err_info").fadeIn();
            return false;
        }
        /*if($("#contact_city").val() == ''){
            $("#contact_city").css({"border":"1px solid #F00"});
            $("#contact_city").focus();
            $("#err_info").html('Thông tin thành phố không được bỏ trống.');
            $("#err_info").fadeIn();
            return false;
        }
        if($("#contact_address").val() == ''){
            $("#contact_address").css({"border":"1px solid #F00"});
            $("#contact_address").focus();
            $("#err_info").html('Thông tin địa chỉ không được bỏ trống.');
            $("#err_info").fadeIn();
            return false;
        }*/
    });
});
