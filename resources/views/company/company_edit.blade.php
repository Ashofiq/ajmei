@extends('layouts.app')
@section('content')
<div class="title">Company Profile</div>
<input type="hidden" name="menu_selection" id="menu_selection" value="SYS@1" class="form-control" required>
<div class="container">
<div class="form" id="form-insert-config"><div class="row">
    <div class="col-md-12">
        <div id="alert" class="alert" role="alert" style="display:none"></div>
    </div>
                <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Company Name :</div>
                    </div>
                    <input type="text" name="companyName" value="Enterprise Business Solutions" class="form-control config" placeholder="Company Name" maxlength="100" autocomplete="off" />                </div>
            </div>
                <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Address :</div>
                    </div>
                    <textarea name="companyAddress" rows="4" cols="50" class="form-control config" placeholder="Address" maxlength="500">House 395, Road 29,
Mohakhali New DOHS
Dhaka-1206</textarea>                </div>
            </div>
                <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Logo :</div>
                    </div>
                    <input type="file" data-image="." name="companyLogo" />                </div>
            </div>
                <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">BIN :</div>
                    </div>
                    <input type="text" name="companyBin" value="4567 68 82334" class="form-control config" placeholder="BIN" maxlength="100" autocomplete="off" />                </div>
            </div>
                <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Registration No. :</div>
                    </div>
                    <input type="text" name="companyReg" value="3930 29 24442" class="form-control config" placeholder="Registration No." maxlength="100" autocomplete="off" />                </div>
            </div>
                <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Timezone :</div>
                    </div>
                    <select name="companyTimezone" class="autocomplete" style="max-width:150px"><option value="0" selected>(GMT-11:00) Midway Island</option><option value="1">(GMT-11:00) Samoa</option><option value="2">(GMT-10:00) Hawaii</option><option value="3">(GMT-09:00) Alaska</option><option value="4">(GMT-08:00) Pacific Time (US & Canada)</option><option value="5">(GMT-08:00) Tijuana</option><option value="6">(GMT-07:00) Arizona</option><option value="7">(GMT-07:00) Mountain Time (US & Canada)</option><option value="8">(GMT-07:00) Chihuahua</option><option value="9">(GMT-07:00) Mazatlan</option><option value="10">(GMT-06:00) Mexico City</option><option value="11">(GMT-06:00) Monterrey</option><option value="12">(GMT-06:00) Saskatchewan</option><option value="13">(GMT-06:00) Central Time (US & Canada)</option><option value="14">(GMT-05:00) Eastern Time (US & Canada)</option><option value="15">(GMT-05:00) Indiana (East)</option><option value="16">(GMT-05:00) Bogota</option><option value="17">(GMT-05:00) Lima</option><option value="18">(GMT-04:30) Caracas</option><option value="19">(GMT-04:00) Atlantic Time (Canada)</option><option value="20">(GMT-04:00) La Paz</option><option value="21">(GMT-04:00) Santiago</option><option value="22">(GMT-03:30) Newfoundland</option><option value="23">(GMT-03:00) Buenos Aires</option><option value="24">(GMT-03:00) Greenland</option><option value="25">(GMT-02:00) Stanley</option><option value="26">(GMT-01:00) Azores</option><option value="27">(GMT-01:00) Cape Verde Is.</option><option value="28">(GMT) Casablanca</option><option value="29">(GMT) Dublin</option><option value="30">(GMT) Lisbon</option><option value="31">(GMT) London</option><option value="32">(GMT) Monrovia</option><option value="33">(GMT+01:00) Amsterdam</option><option value="34">(GMT+01:00) Belgrade</option><option value="35">(GMT+01:00) Berlin</option><option value="36">(GMT+01:00) Bratislava</option><option value="37">(GMT+01:00) Brussels</option><option value="38">(GMT+01:00) Budapest</option><option value="39">(GMT+01:00) Copenhagen</option><option value="40">(GMT+01:00) Ljubljana</option><option value="41">(GMT+01:00) Madrid</option><option value="42">(GMT+01:00) Paris</option><option value="43">(GMT+01:00) Prague</option><option value="44">(GMT+01:00) Rome</option><option value="45">(GMT+01:00) Sarajevo</option><option value="46">(GMT+01:00) Skopje</option><option value="47">(GMT+01:00) Stockholm</option><option value="48">(GMT+01:00) Vienna</option><option value="49">(GMT+01:00) Warsaw</option><option value="50">(GMT+01:00) Zagreb</option><option value="51">(GMT+02:00) Athens</option><option value="52">(GMT+02:00) Bucharest</option><option value="53">(GMT+02:00) Cairo</option><option value="54">(GMT+02:00) Harare</option><option value="55">(GMT+02:00) Helsinki</option><option value="56">(GMT+02:00) Istanbul</option><option value="57">(GMT+02:00) Jerusalem</option><option value="58">(GMT+02:00) Kyiv</option><option value="59">(GMT+02:00) Minsk</option><option value="60">(GMT+02:00) Riga</option><option value="61">(GMT+02:00) Sofia</option><option value="62">(GMT+02:00) Tallinn</option><option value="63">(GMT+02:00) Vilnius</option><option value="64">(GMT+03:00) Baghdad</option><option value="65">(GMT+03:00) Kuwait</option><option value="66">(GMT+03:00) Nairobi</option><option value="67">(GMT+03:00) Riyadh</option><option value="68">(GMT+03:00) Moscow</option><option value="69">(GMT+03:30) Tehran</option><option value="70">(GMT+04:00) Baku</option><option value="71">(GMT+04:00) Volgograd</option><option value="72">(GMT+04:00) Muscat</option><option value="73">(GMT+04:00) Tbilisi</option><option value="74">(GMT+04:00) Yerevan</option><option value="75">(GMT+04:30) Kabul</option><option value="76">(GMT+05:00) Karachi</option><option value="77">(GMT+05:00) Tashkent</option><option value="78">(GMT+05:30) Kolkata</option><option value="79">(GMT+05:45) Kathmandu</option><option value="80">(GMT+06:00) Ekaterinburg</option><option value="81">(GMT+06:00) Almaty</option><option value="82">(GMT+06:00) Dhaka</option><option value="83">(GMT+07:00) Novosibirsk</option><option value="84">(GMT+07:00) Bangkok</option><option value="85">(GMT+07:00) Jakarta</option><option value="86">(GMT+08:00) Krasnoyarsk</option><option value="87">(GMT+08:00) Chongqing</option><option value="88">(GMT+08:00) Hong Kong</option><option value="89">(GMT+08:00) Kuala Lumpur</option><option value="90">(GMT+08:00) Perth</option><option value="91">(GMT+08:00) Singapore</option><option value="92">(GMT+08:00) Taipei</option><option value="93">(GMT+08:00) Ulaan Bataar</option><option value="94">(GMT+08:00) Urumqi</option><option value="95">(GMT+09:00) Irkutsk</option><option value="96">(GMT+09:00) Seoul</option><option value="97">(GMT+09:00) Tokyo</option><option value="98">(GMT+09:30) Adelaide</option><option value="99">(GMT+09:30) Darwin</option><option value="100">(GMT+10:00) Yakutsk</option><option value="101">(GMT+10:00) Brisbane</option><option value="102">(GMT+10:00) Canberra</option><option value="103">(GMT+10:00) Guam</option><option value="104">(GMT+10:00) Hobart</option><option value="105">(GMT+10:00) Melbourne</option><option value="106">(GMT+10:00) Port Moresby</option><option value="107">(GMT+10:00) Sydney</option><option value="108">(GMT+11:00) Vladivostok</option><option value="109">(GMT+12:00) Magadan</option><option value="110">(GMT+12:00) Auckland</option><option value="111">(GMT+12:00) Fiji</option></select>                </div>
            </div>
                <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Export Reg No. :</div>
                    </div>
                    <input type="text" name="companyExportReg" value="" class="form-control config" placeholder="Export Reg No." maxlength="100" autocomplete="off" />                </div>
            </div>
                <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Import Reg No. :</div>
                    </div>
                    <input type="text" name="companyImportReg" value="" class="form-control config" placeholder="Import Reg No." maxlength="100" autocomplete="off" />                </div>
            </div>
                <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Exp. Reg. Issue Date :</div>
                    </div>
                    <input type="text" name="companyExportRegIssuingDate" value="" class="form-control date config" autocomplete="off" />                </div>
            </div>
                <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Exp. Reg. Expiry Date :</div>
                    </div>
                    <input type="text" name="companyExportRegExpiryDate" value="" class="form-control date config" autocomplete="off" />                </div>
            </div>
                <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Imp. Reg. Issue Date :</div>
                    </div>
                    <input type="text" name="companyImportRegIssuingDate" value="" class="form-control date config" autocomplete="off" />                </div>
            </div>
                <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Imp. Reg. Expiry Date :</div>
                    </div>
                    <input type="text" name="companyImportRegExpiryDate" value="" class="form-control date config" autocomplete="off" />                </div>
            </div>
                <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Base Currency :</div>
                    </div>
                    <select name="companyCurrency" class="autocomplete" style="max-width:150px"><option value="0">None</option><option value="3">AFN</option><option value="1">ALL</option><option value="73">ANG</option><option value="4">ARS</option><option value="6">AUD</option><option value="5">AWG</option><option value="7">AZN</option><option value="15">BAM</option><option value="9">BBD</option><option value="113">BDT</option><option value="115" selected>BDT</option><option value="17">BGN</option><option value="13">BMD</option><option value="14">BOB</option><option value="18">BRL</option><option value="16">BWP</option><option value="10">BYR</option><option value="12">BZD</option><option value="22">CAD</option><option value="63">CHF</option><option value="24">CLP</option><option value="25">CNY</option><option value="26">COP</option><option value="27">CRC</option><option value="29">CUP</option><option value="30">CZK</option><option value="31">DKK</option><option value="32">DOP </option><option value="34">EGP</option><option value="11">EUR</option><option value="37">FJD</option><option value="36">FKP</option><option value="19">GBP</option><option value="41">GGP</option><option value="38">GHC</option><option value="39">GIP</option><option value="40">GTQ</option><option value="42">GYD</option><option value="44">HKD</option><option value="43">HNL</option><option value="28">HRK</option><option value="45">HUF</option><option value="48">IDR</option><option value="51">ILS</option><option value="50">IMP</option><option value="47">INR</option><option value="49">IRR</option><option value="46">ISK</option><option value="54">JEP</option><option value="52">JMD</option><option value="53">JPY</option><option value="58">KGS</option><option value="21">KHR</option><option value="56">KPW</option><option value="57">KRW</option><option value="23">KYD</option><option value="55">KZT</option><option value="59">LAK</option><option value="61">LBP</option><option value="96">LKR</option><option value="62">LRD</option><option value="64">LTL</option><option value="60">LVL</option><option value="65">MKD</option><option value="69">MNT</option><option value="67">MUR</option><option value="68">MXN</option><option value="66">MYR</option><option value="70">MZN</option><option value="71">NAD</option><option value="76">NGN</option><option value="75">NIO</option><option value="77">NOK</option><option value="72">NPR</option><option value="74">NZD</option><option value="78">OMR</option><option value="80">PAB</option><option value="82">PEN</option><option value="83">PHP</option><option value="79">PKR</option><option value="84">PLN</option><option value="81">PYG</option><option value="85">QAR</option><option value="86">RON</option><option value="90">RSD</option><option value="87">RUB</option><option value="89">SAR</option><option value="93">SBD</option><option value="91">SCR</option><option value="97">SEK</option><option value="92">SGD</option><option value="88">SHP</option><option value="94">SOS</option><option value="98">SRD</option><option value="35">SVC</option><option value="99">SYP</option><option value="101">THB</option><option value="104">TRL</option><option value="103">TRY</option><option value="102">TTD</option><option value="105">TVD</option><option value="100">TWD</option><option value="106">UAH</option><option value="2">USD</option><option value="107">UYU</option><option value="108">UZS</option><option value="109">VEF</option><option value="110">VND</option><option value="33">XCD</option><option value="111">YER</option><option value="95">ZAR</option><option value="112">ZWD</option></select>                </div>
            </div>
                <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Stock Management :</div>
                    </div>
                    <select name="companyManualStock" class="autocomplete" style="max-width:150px"><option value="1">Manual</option><option value="0" selected>Automatic (FEFO/FIFO)</option></select>                </div>
            </div>
                <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Service Oriented :</div>
                    </div>
                    <input type="hidden" name="companyIsService" value="0" /><input type="checkbox" data-toggle="toggle" data-width="45" data-height="25" data-on="YES" data-off="NO" data-onstyle="success" name="companyIsService" value="1" class="form-control config"/>                </div>
            </div>
                <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Sell Single Product :</div>
                    </div>
                    <input type="hidden" name="companySingleProduct" value="0" /><input type="checkbox" data-toggle="toggle" data-width="45" data-height="25" data-on="YES" data-off="NO" data-onstyle="success" name="companySingleProduct" checked value="1" class="form-control config"/>                </div>
            </div>
                <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Manages VAT :</div>
                    </div>
                    <input type="hidden" name="companyTaxManage" value="0" /><input type="checkbox" data-toggle="toggle" data-width="45" data-height="25" data-on="YES" data-off="NO" data-onstyle="success" name="companyTaxManage" checked value="1" class="form-control config"/>                </div>
            </div>
            </div>
    <div class="footer">
        <div class="btn-group float-right"><a href="#" id="submit-insert-config" class="btn btn-primary " data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Working...">Save</a></div>
    </div>
</div>
</div>
@stop

@section('pagescript')
<script>
$('#submit-insert-config').on( 'click', function(event){
    var btn = $(this);
    btn.addClass('disabled');
    btn.data('original-text', btn.html());
    btn.html(btn.data('loading-text'));

    var formData = new FormData();
    formData.append('postToken','1cd15eb13c303f71c0e0a93c9c30676302024654');
    $('#form-insert-config :input').each(function(){
        if($(this).attr('type')=='file' && $(this)[0].files[0]) formData.append($(this).attr('name'), $(this)[0].files[0]);
        else if($(this).attr('type')=='checkbox') formData.append($(this).attr('name'), $(this).is(':checked')?1:0 );
        else{formData.append($(this).attr('name'),$(this).val());}
    });
        $.ajax({
       type: "POST",
       dataType: "json",
       url: 'https://robi.erp2all.com/config/globals/company/save/',
       data: formData,
       processData: false,        contentType: false,        success: function(data){
            btn.html(btn.data('original-text'));
            btn.removeClass('disabled');
            if( typeof data['error'] !== 'undefined' ){
                                   showAlert(data, btn);
                           }else{
                                    closeThis(btn);
                    showAlert(data, '#crudomator-table-config');
                    reloadTable('config');
                           }
            if( typeof data['js'] !== 'undefined' ){
               eval(data['js']);
            }
        }
    });
    return false;
});
</script><script>
$.ajaxSetup({ cache: false });
if(typeof window.reloadTable === 'undefined'){
    function camelize(text){
        text = text.replace(/[-_\s.]+(.)?/g, (_, c) => c ? c.toUpperCase() : '');
        return text.substr(0, 1).toLowerCase() + text.substr(1);
    }
    function reloadTable(table){
        $('.alert-danger').slideUp();
                    eval(camelize('table-'+table)).setData();
            }
}
if (typeof window.crudomatorRedirect === 'undefined') {
    function crudomatorRedirect(uri){
        window.location = uri;
    }
}
if (typeof window.deselectAll === 'undefined') {
    function deselectAll(htmlID){
        $('#crudomator-table-'+htmlID+' tbody tr').removeClass('selected');
        $('#crudomator-selected-'+htmlID).prop('class','');
    }
}
</script>

@stop()
