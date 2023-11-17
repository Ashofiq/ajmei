@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/blogic_css/sales_tb.css') }}" />

    <link href="{{ asset('assets/bootstrap/3.4.1/css/bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/bootstrap/3.4.1/js/bootstrap.min.js') }}"></script>
 
@stop
@section('content')
<section class="content">
  <input type="hidden" name="menu_selection" id="menu_selection" value="SD@1" class="form-control" required>

<div class="title">
  <div  style="background-color:#e0e0e0" class="widget-header widget-header-small">
    <h6 class="widget-title smaller">
    <font size="2" color="blue"><b>Loan Received From Customer</b></font>
    </h6>
    <div class="widget-toolbar">
      <a href="{{route('sales.loan.issue')}}" class="blue"><i class="fa fa-list"></i> List</a>
    </div>
  </div>
</div>
@if(Session::has('message'))
 <div class="row">
   <div class="col-md-12">
     <p class="alert alert-success"><b>{{ Session::get('message') }}</b></p>
   </div>
</div>
@endif
    <div class="widget-body">
      <div class="widget-main">
        <form id="so_Form" action="{{route('sales.loan.issue.store')}}" method="post">
        {{ csrf_field() }}
        <input type="hidden" id="so_del_no" name="so_del_no" value="0" class="form-control  input-sm" autocomplete="off" required/>
        <input type="hidden" id="so_inv_no" name="so_inv_no" value="0" class="form-control  input-sm" autocomplete="off" required/>
         <div class="row">
          <div class="col-md-3">
            <div class="input-group ss-item-required">
                <div class="input-group-prepend ">
                    <div class="input-group-text" style="min-width:70px">Loan Issue Date:</div>
                </div>
                <input type="text" size = "15" name="order_date" id="order_date" onclick="displayDatePicker('order_date');"  value="{{ old('order_date') == "" ?  date('d-m-Y') :  date('d-m-Y',strtotime(old('order_date'))) }}"  required />
                <a href="javascript:void(0);" onclick="displayDatePicker('order_date');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>

                <!-- input type="date" name="order_date" value="{{ old('order_date') == "" ? $order_date : old('order_date') }}" class="form-control  input-sm" autocomplete="off" required/ -->
           </div>
          </div>
          <div class="col-md-4">
               <div class="input-group">
                 <div class="input-group-prepend">
                     <div class="input-group-text" style="min-width:130px">Company:</div>
                 </div>
                 <select name="company_code" class="form-control-sm autocomplete" id="company_code"  style="max-width:150px" required>
                 <option value="" >--Select--</option>
                     @if ($companies->count())
                         @foreach($companies as $company)
                             <option {{ $company_code == $company->comp_id ? 'selected' : '' }} value="{{$company->comp_id}}" >{{ $company->comp_id }}-{{ $company->name }}</option>
                         @endforeach
                     @endif
                 </select>
                </div>
           </div>
      </div>
      <div class="row">
        <div class="col-md-3">
          <div class="input-group">
            <div class="input-group-prepend">
              <div class="input-group-text" style="min-width:70px">Loan Refernce:</div>
            </div>
            <input type="text" name="reference_no" id="reference_no" value="{{$reference_no}}" class="form-control" required readonly/>
          </div>
        </div>

        <div class="col-md-5">
            <div class="input-group">
              <select name="customer_id" id="customer_id" class="chosen-select"  onchange="getCustomerDetails(this.value)">
                  <option value="" disabled selected>- Select Customer -</option>
                  @foreach($customers as $customer)
                      <option {{ old('customer_id') == $customer->id ? 'selected' : '' }} value="{{ $customer->id }}">{{ $customer->cust_name }}</option>
                  @endforeach
              </select>
              @error('customer_id')
              <span class="text-danger">{{ $message }}</span>
              @enderror
              </div>
          </div>
           
       </div>

       <div class="row">
         <div class="col-md-4">
            <div class="input-group-prepend">
              <div class="input-group-prepend">
                  <div class="input-group-text" style="min-width:80px">Wearhouse:</div>
              </div>
              <select name="itm_warehouse" class="chosen-select" id="itm_warehouse"
               onchange="getStorageByWearId(this.value)" required>
              <option value="" >--Select Wearhouse--</option>
                  @if ($warehouse_list->count())
                      @foreach($warehouse_list as $list)
                          <option {{ old('itm_warehouse') == $list->w_ref_id ? 'selected' : '' }} value="{{$list->w_ref_id}}" >{{ $list->ware_name }}</option>
                      @endforeach
                  @endif
              </select>
             </div>
        </div>
        <div class="col-md-6">
          <div class="input-group ss-item-required">
            <input type="hidden" name="result_wh_id" id="result_wh_id" value="{{old('result_wh_id')}}" class="form-control" readonly required/>
            <input type="hidden" name="result_storage_id" id="result_storage_id" value="{{old('result_storage_id')}}" class="form-control" readonly required/> 
          </div>
        </div>
      </div>

       <div class="row">
         <div class="col-md-12">
           <ul class="nav nav-tabs">
             <li class="nav-item">
               <a href="#itemdetails" class="nav-link" role="tab" data-toggle="tab"></a>
             </li> 
           </ul>
           <div class="tab-content">
               <div role="tabpanel" class="tab-pane active" id = "itemdetails">
                 <div class="row">
                   <div class="col-md-12 input-group">
                     <table id="salesTable" class="table table-striped table-data table-view">
                       <thead class="salesTable">
                         <th width="2%" class="text-center">&nbsp;&nbsp;</th>
                         <th width="2%" style="display: none" class="text-center">Id</th>
                         <th width="5%" style="display: none" class="text-center">Code</th>
                         <th width="10%" class="text-center">Barcode</th>
                         <th width="25%" class="text-center">Item Name</th>
                         <th width="20%" style="display: none" class="text-center">Item Desc</th>
                         <th width="5%" style="display: none" class="text-center">Storage</th>
                         <th width="15%" class="text-center">Item LOT</th> 
                         <th width="5%" class="text-center">Qty</th>
                         <th width="4%" class="text-center">Unit</th>  
                         <th width="3%" class="text-center">&nbsp;</th>
                     </thead>
                   <tbody class="salesTable" style="background-color: #ffffff;">
                     <tr>
                       <td width="2%" class="text-center">1</td>
                       <td width="2%" style="display: none"><input type="text" data-type="ItemCodeId" name="ItemCodeId[]" id="ItemCodeId_1" class="form-control item_id_class" autocomplete="off"></td>
                       <td width="5%" style="display: none"><input type="text" data-type="ItemCode" name="ItemCode[]" id="ItemCode_1" class="form-control autocomplete_txt" autocomplete="off"></td>
                       <td width="10%"><input type="text" data-type="ItemBarCode" name="ItemBarCode[]" id="ItemBarCode_1" onKeyUp="loadItemsDetByBarcode(this.id,this.value)" class="form-control autocomplete_barcode_txt" autocomplete="off"></td>
                       <td width="25%">
                         <div><select data-type="itemid" name="itemid[]"  id ="itemid_1" class="form-control chosen-select" onchange="loadItemsDet(this.id,this.value)">
                           <option value="" disabled selected>- Select Item -</option>
                           @foreach($item_list as $cmb)
                               <option  value="{{ $cmb->id }}">{{ $cmb->item_name }}</option>
                           @endforeach
                         </select></div>
                      </td>
                      <td width="20%" style="display: none"><input type="text" data-type="ItemDesc" name="ItemDesc[]" id="ItemDesc_1" class="form-control" autocomplete="off" ></td>
                      <td width="5%" style="display: none" align="center">
                        <div><select data-type="Storage" name="Storage[]"  id ="Storage_1" class="form-control chosen-select">
                            @foreach($stor_list as $stor)
                                <option  value="{{ $stor->id }}">{{ $stor->stor_code }}({{ $stor->stor_name }})</option>
                            @endforeach
                          </select></div>
                      </td>
                      <td width="15%">
                        <div><input type="text" data-type="lotno" name="lotno[]" id="lotno_1"  class="form-control input-sm" style="font-weight:bold; text-align: left;" autocomplete="off"></div>
                      </td>
                          
                       <td width="5%" align="center"><input type="text" data-type="Qty" name="Qty[]" id="Qty_1" onkeydown="enter(this.id,this.value)" class="form-control input-sm changesNo iQty" style="font-weight:bold; text-align: center;" autocomplete="off" ></td>
                       <td width="4%" align="center"><input type="text" data-type="Unit" name="Unit[]" id="Unit_1"  class="form-control input-sm"  autocomplete="off" readonly></td> 
                       <td width="3%"></td>
                   </tr>
                    </tbody>
                 </table>
               </div>
             </div>
              <div class="row">
               <div class="col-md-12 input-group">
                 <table class="table table-striped table-data table-view">
                   <tbody style="background-color: #ffffff;">  
                    <tr>
                       <td width="5%">&nbsp;</td>
                       <td width="5%">&nbsp;</td>
                       <td width="15%">&nbsp;</td>
                       <td width="5%">&nbsp;</td>
                       <td width="8%">&nbsp;</td>
                       <td width="15%">&nbsp;</td> 
                       <td width="5%"><b>Total&nbsp;Qty:</b></td>
                       <td width="8%"><input type="text" data-type="total_qty" name="total_qty" id="total_qty" value="0" style="font-weight:bold; text-align: right;" readonly></td>
                       <td width="15%">&nbsp;</td>
                    </tr>
                 </tbody>
                 </table>
               </div>
              </div>

              <div class="row">
                <div class="col-md-10">
                 <table class="table table-striped table-data table-view">
                   <tbody style="background-color: #ffffff;">
                     <tr style="display: none">
                       <td width="2%" align="right" style="display: none"><input type="hidden" data-type="total_discount" name="total_discount" id="total_discount" value="0.00" style="font-weight:bold; text-align: right;" readonly></td>
                       <td width="2%" align="right" style="display: none"><input type="hidden" data-type="total_amount" name="total_amount" id="total_amount" value="0.00" style="font-weight:bold; text-align: right;" readonly></td>
                       <td width="3%"></td>
                     </tr>
                    </tbody>
                 </table>
               </div>
             </div>
            </div> 
         </div>
       </div>
      <div class="row justify-content-left">
        <div class="col-sm-6 text-left">
            <button class="btn btn-sm btn-success" type="button" id='btn1' ><i class="fa fa-save"></i> Save</button> 
        </div> 
        </form>
        <div class="col-sm-4 text-left"> 
            <a href="{{route('sales.loan.issue')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> List</a>
        </div>
      </div> 
    </div>
</section>
@stop
@section('pagescript')
<script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/chosen.jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/ace-elements.min.js') }}"></script>
<script src="{{ asset('assets/js/ace.min.js') }}"></script>
<script src="{{ asset('assets/blogic_js/sel_box_search.js') }}"></script>

<script>
var form = document.getElementById('so_Form');

document.getElementById('btn1').onclick = function() {
  form.action = '{{route("sales.loan.issue.store")}}';
  if(formcheck()){
    form.submit();
  }
}

document.getElementById('btn6').onclick = function() {

  var customer_id = $('#customer_id').val();
  var company_code = $('#company_code').val();
  var order_date = $('#order_date').val();

  //alert(customer_id + company_code + order_date);

  if(customer_id != null) {
    url = "{{url('/rpt/sub-ledger1/')}}/"+order_date+'/'+company_code+'/'+customer_id;
    form.action = url;
    form.target = "_blank";
    form.submit();
  }else{
    alert("Customer can't be empty");
  }
}


function formcheck() {
  var isSubmit = true;
  var fields = $(".ss-item-required")
  .find("select, textarea, input").serializeArray();

  $.each(fields, function(i, field) {
    if (!field.value){
      alert(field.name + ' is required');
      isSubmit = false;
      return isSubmit;
    }
  });

  var total_qty = $('#total_qty').val();
  var n_net_amount = $('#n_net_amount').val();
  if( total_qty<=0 ){
    alert('Qty Can Not be 0');
    isSubmit = false;
    return isSubmit;
  } else if(n_net_amount<=0){
    alert('Net Amount Can Not be 0');
    isSubmit = false;
    return isSubmit;
  }

  return isSubmit;
  //if(isSubmit) formSubmit();
  //console.log(fields);
}

function formSubmit()
{
    $('#so_Form').submit();
}
</script>

<script>
$(document).on('change keyup blur', '.changesNo', function () {
  id_arr = $(this).attr('id');
  id = id_arr.split("_");
  //alert(id[0]);
  //total_qty = $('#total_qty').val();

  price         = 1; 
  quantity      = $('#Qty_' + id[1]).val();
  perDisc       = 0; 

  n_disc_per = 0;
  n_discount = 0;
  n_vat_per  = 0;
 
   
  totalQuantityCount();
  // totalDiscountCount();
  // totalAmountCount();
});

function totalQuantityCount()
{
    var total_qty = 0;
    $('.iQty').each(function(){
        if(parseFloat($(this).val())>0)
            total_qty += parseFloat($(this).val());
    });
    $('#total_qty').val(total_qty);
}

function totalDiscountCount()
{
    var total_discount = 0;
    $('.iDiscount').each(function(){
        if(parseFloat($(this).val())>0)
            total_discount += parseFloat($(this).val());
    });
    $('#total_discount').val(total_discount.toFixed(2));

    n_discount = $('#n_discount').val();
    if(n_discount>0) discount = parseFloat(total_discount) + parseFloat(n_discount);
    else discount = total_discount;
    $('#n_total_disc').val(parseFloat(discount).toFixed(2));
}

function totalAmountCount()
{
    var total_amount = 0;
    $('.iTotal').each(function(){
        if(parseFloat($(this).val())>0)
            total_amount += parseFloat($(this).val());
    });
    $('#total_amount').val(parseFloat(total_amount).toFixed(2));
    $('#n_sub_total').val(parseFloat(total_amount).toFixed(2));

    n_total_disc = $('#n_total_disc').val();

    if (n_total_disc != ''){
      n_sub_total = $('#n_sub_total').val();
      n_discount = $('#n_discount').val();
      //alert(n_sub_total+ ' - ' +n_total_disc);
      n_net_amount = parseFloat(n_sub_total) - parseFloat(n_discount);
      $('#n_total_gross').val(parseFloat(n_net_amount).toFixed(2));
    }else{
        n_net_amount  = total_amount;
    }

    n_total_vat = $('#n_total_vat').val()==""?'0.00':$('#n_total_vat').val();
    if (n_total_vat != ''){
      n_total_gross = $('#n_total_gross').val();
      n_net_amount = parseFloat(n_total_gross);// + parseFloat(n_total_vat);
    }else{
        n_net_amount  = parseFloat(n_total_gross);
    }

    $('#n_net_amount').val(parseFloat(n_net_amount).toFixed(2));


}

function getDropdownWarehosueList(w_id){
  var comp_code = $('#company_code').val();
  //alert(comp_code+' A '+w_id);
  //i = parseInt(i);
  $.get('{{ url('/') }}/warehouseLookup1/'+comp_code, function(response) {
    var selectList = $('select[id="itm_warehouse"]');
    selectList.chosen();
    selectList.empty();
    $.each(response, function(index, element) {
      if (w_id ==  element.w_ref_id){
        selectList.append('<option value="' + element.w_ref_id + '" selected>' + element.ware_name +'</option>');
      }else{
        selectList.append('<option value="' + element.w_ref_id + '">' + element.ware_name +'</option>');
      }
    });
    selectList.trigger('chosen:updated');
  });
}

function getStorageByWearId(w_house){
    var comp_code = $('#company_code').val();
    var total_qty = $('#total_qty').val();
    var result_wh_id = $('#result_wh_id').val();

    if(total_qty>0){
      alert("Can't change warehoue if LOT No/Qty is already Defined");
      getDropdownWarehosueList(result_wh_id);
      return false;
    }
  //  alert('get-storage-inf/getdetails/'+comp_code+'/'+w_house+'/getfirst');

    $.ajax({  //create an ajax request to display.php
      type: "GET",
      url: 'get-storage-inf/getdetails/'+comp_code+'/'+w_house+'/getfirst',
      success: function (data) {
        $("#result_storage_id").val(data.id)
        $("#result_wh_id").val(w_house)
        getDropdownStorageList(1)
      }
    });
}

function loadItemsDet(el,itemid){
    i = el.split("_")[1];
    var storgae_id = $('#Storage_'+i).val();
    //alert(storgae_id);
    var w_id = $("#itm_warehouse").val();
    if (w_id == ''){
      alert("Please Select Customer & Warehoue");
      return false;
    }
    //alert(el+' '+itemid)
    var customer_id = $('#customer_id').val();
    $.get('get-item-code/getdetails/'+itemid+'/'+customer_id+'/getfirst', function(data){
    item = data.data
    }).then(function(){
      id_arr = el
      id = id_arr.split("_")
      $('tr.duplicate').removeClass('duplicate')
      checkDuplicateItem(id, item,false)
      getDropdownStorageList(id[1])
      // getDropdownLotList(id[1],item.id,storgae_id,false)
      //  calcluteTotalBill()
      //  totalQuantityCount()
    })

}

function loadLotDet(el,lotno){
    i = el.split("_")[1];
    
    var result_cust_own_comm = 0; //$('#result_cust_own_comm').val();
    var storgae_id = $('#Storage_'+i).val();
    var itemid = $('#ItemCodeId_'+i).val();
    //alert(i+'-'+itemid+'-'+lotno)

    var qty = 0;
    var j = 1;
    //Checked the user qty
    $('.iQty').each(function(){
        item = $('#ItemCodeId_'+j).val();
        lot    = $('#lotno_'+j).val();
        //alert(j+'-'+item+' L-'+lot)
        if(parseFloat($(this).val())>0 && item == itemid && lotno == lot)
            qty += parseFloat($(this).val());
        j += 1;
    });
    
    $.ajax({  //create an ajax request to display.php
      type: "GET",
      url: 'get-stock-inf/'+storgae_id+'/'+itemid+'/'+lotno+'/getfirst',
      success: function (data) {
        //alert(data.stock);
        qty = parseFloat(data.stock) - parseFloat(qty);
        $("#Stock_"+i).val(qty);
        $('#Qty_'+i).focus();
        //$("#Discp_"+i).val(result_cust_own_comm);
      }
    });
}


function loadItemsDetByBarcode(el,itembarcode){
    var so_id  = $('#so_id').val();
    //alert(el+' Item:'+itembarcode+' SO:'+so_id);
    if (itembarcode != ''){
      $.get('get-item-bar-code/getdetails/'+itembarcode+'/getfirst', function(data){
      item = data.data
      }).then(function(){
        id_arr = el
        id = id_arr.split("_")
        $('tr.duplicate').removeClass('duplicate')
        checkDuplicateItem(id, item,false)
        //getDropdownItemList(id[1],item.id)
      })
    }
}

function loadDeliveredDet(el,delid){
    //alert(delid);
    $.ajax({  //create an ajax request to display.php
      type: "GET",
      url: 'get-delivered-inf/getdetails/'+delid+'/getfirst',
      success: function (data) {
        //alert(data.deliv_add +'::'+ data.deliv_mobile);
        $("#address1").val(data.deliv_add);
        $("#contact_no").val(data.deliv_mobile);
      }
    });
}

function getDeliveredInformByCustId(custid){
    //alert(custid);
    $.ajax({  //create an ajax request to display.php
      type: "GET",
      url: 'get-delivered-inf_d/getdetails/'+custid+'/getfirst',
      success: function (data) {
        //alert(data.deliv_add +'::'+ data.deliv_mobile);
        $("#address1").val(data.deliv_add);
        $("#contact_no").val(data.deliv_mobile);
      }
    });
}

function getCustomerVAT(compid,custid){
    //alert(compid+'--'+custid);
    $.ajax({  //create an ajax request to display.php
      type: "GET",
      url: 'get-cust-vat-inf/getdetails/'+compid+'/'+custid+'/getfirst',
      success: function (data) {
        //alert(data.cust_VAT);
        $("#n_vat_per").val(data.cust_VAT);
        //$("#result_cust_comm").val(data.cust_overall_comm);
        //$("#result_cust_own_comm").val(data.cust_own_comm);
      //  $("#n_disc_per").val(data.cust_own_comm);
      }
    });
}

function getCustomerOutstanding(compid,custid){
    //alert(compid+'--'+custid);
    $.ajax({  //create an ajax request to display.php
      type: "GET",
      url: 'get-cust-oustanding-inf/getdetails/'+compid+'/'+custid+'/getfirst',
      success: function (data) {
        //alert(data.outstanding);
        $("#result_cust_outstanding").val(data.outstanding);
      }
    });
}

function getCustomerDetails(custid){
  var customer_id = $('#customer_id').val();
  var company_code = $('#company_code').val();
  $('#result_customer_id').val(customer_id);
  getDropdownItemList(1,0);
  getDropdownDeliveredToList();
  getDeliveredInformByCustId(customer_id);
  getCustomerOutstanding(company_code,customer_id);
  getCustomerVAT(company_code,customer_id);
  getDropdownCourrierToList(company_code,customer_id);
}

function getAutoComments(el,contag){
  var n_net_amount = $('#n_net_amount').val();
  //alert(n_net_amount +' :: '+contag);
  if(contag == 'Condition'){
    $('#comments').val(n_net_amount);
  }else{
    $('#comments').val('');
  }
}

function enter(id,amount) {
    if(event.keyCode == 13) {
        field = id.split("_")[0];
        i = id.split("_")[1];
        if(amount > 0 && field == 'Qty') row_increment();
        else if(field == 'Discp') row_increment();
        //alert(i);
        //getDropdownItemList(i);
    }
}

function removeRow(el) {
    $(el).parents("tr").remove();
    totalQuantityCount();
    totalDiscountCount();
    totalAmountCount();
}

function row_increment() {

    var i = $('#salesTable tr').length;
    html = '<tr>';
    html += '<td width="2%" class="text-center">' + i + '</td>';
    html += '<td width="2%" style="display: none"><input type="text" data-type="ItemCodeId" name="ItemCodeId[]" id="ItemCodeId_' + i + '"  class="form-control item_id_class" autocomplete="off"></td>';
    html += '<td width="5%" style="display: none"><input type="text" data-type="ItemCode" name="ItemCode[]" id="ItemCode_' + i + '" class="form-control autocomplete_txt" autocomplete="off"></td>';
    html += '<td width="10%"><input type="text" data-type="ItemBarCode" name="ItemBarCode[]" id="ItemBarCode_' + i + '" onKeyUp="loadItemsDetByBarcode(this.id,this.value)" class="form-control autocomplete_barcode_txt" autocomplete="off"></td>';
    html += '<td width="25%">';
    html += '<div><select data-type="itemid" name="itemid[]"  id ="itemid_' + i + '" class="form-control chosen-select" onchange="loadItemsDet(this.id,this.value)">';
    html += '<option value="" disabled selected>-- Select Item --</option>';
    html += '</select></div></td>';
    html += '<td width="20%" style="display: none"><input type="text" data-type="ItemDesc" name="ItemDesc[]" id="ItemDesc_' + i + '" class="form-control" autocomplete="off" readonly></td>';
    html += '<td width="5%" style="display: none">';
    html += '<div><select data-type="Storage" name="Storage[]"  id ="Storage_' + i + '" class="chosen-select" >';
    html += '</select></div></td>';
    html += '<td width="15%">';
    html += '<div><input type="text" data-type="lotno" name="lotno[]" id="lotno_' + i + '" class="form-control input-sm" style="font-weight:bold; text-align: left;" autocomplete="off"></div></td>'; 
    html += '<td width="5%"><input type="text" data-type="Qty" name="Qty[]" id="Qty_' + i + '"  onkeydown="enter(this.id,this.value)" class="form-control input-sm changesNo iQty" style="font-weight:bold; text-align: center;" autocomplete="off"></td>';
    html += '<td width="4%"><input type="text" data-type="Unit" name="Unit[]" id="Unit_' + i + '" class="form-control" autocomplete="off" readonly></td>';
     
    html += '<td width="3%"><div class="btn-group btn-corner"><button type="button" tabindex="-1" class="btn btn-danger btn-xs delete" title="Delete This Row" onclick="removeRow(this)"><i class="fa fa-trash"></i></button></div></td>';
    html += '</tr>';

    $('#salesTable').append(html);
    getDropdownItemList(i,0);
    getDropdownStorageList(i);
    document.getElementById('ItemCode_'+i).focus();
    i++;
}

$(document).on('keypress', '.autocomplete_barcode_txt', function () {
    compcode = $('#company_code').val()
    custid  = $('#customer_id').val()
    //  alert(compcode)
    el = $(this).attr('id')
    //alert(el)
    $(this).autocomplete({
      source: function(req, res){
      $.ajax({
          url: "/get-item-code/all",
          dataType: "json",
          data:{'itemcode':encodeURIComponent(req.term),
                'custsid':encodeURIComponent(custid),
                'compcode':encodeURIComponent(compcode) },

          error: function (request, error) {
             console.log(arguments);
             alert(" Can't do because: " +  console.log(arguments));
          },

        success: function (data) {
          res($.map(data.data, function (item) {
            //alert('IQII:'+item.acc_head)
            return {
              label: item.item_bar_code,
              value: item.item_bar_code,
              itm_id: item.id,
              el:el,
            };
          }));
        }
      });
    },
      autoFocus:true,
      select: function(event, ui){
      //alert(ui.item.itm_id)
      $.get('get-item-code/getdetails/'+ui.item.itm_id+'/getfirst', function(data){
      item = data.data
      }).then(function(){

        id_arr = ui.item.el
        id = id_arr.split("_")
        $('tr.duplicate').removeClass('duplicate')
        checkDuplicateItem(id, item,true)
        $('lotno_'+id[1]).focus()
        //  calcluteTotalBill()
        //  totalQuantityCount()
      })
    }
  })
})

$(document).on('keypress', '.autocomplete_txt', function () {
    compcode = $('#company_code').val()
    custid  = $('#customer_id').val()
    //  alert(compcode)
    el = $(this).attr('id')
    //alert(el)
    $(this).autocomplete({
      source: function(req, res){
      $.ajax({
          url: "/get-item-code/all",
          dataType: "json",
          data:{'itemcode':encodeURIComponent(req.term),
                'custsid':encodeURIComponent(custid),
                'compcode':encodeURIComponent(compcode) },

          error: function (request, error) {
             console.log(arguments);
             alert(" Can't do because: " +  console.log(arguments));
          },

        success: function (data) {
          res($.map(data.data, function (item) {
            //alert('IQII:'+item.acc_head)
            return {
              label: item.item_code,
              value: item.item_code,
              itm_id: item.id,
              el:el,
            };
          }));
        }
      });
    },
      autoFocus:true,
      select: function(event, ui){
      //alert(ui.item.itm_id)
      $.get('get-item-code/getdetails/'+ui.item.itm_id+'/getfirst', function(data){
      item = data.data
      }).then(function(){

        id_arr = ui.item.el
        id = id_arr.split("_")
        $('tr.duplicate').removeClass('duplicate')
        checkDuplicateItem(id, item,true)
        $('lotno_'+id[1]).focus()
        //  calcluteTotalBill()
        //  totalQuantityCount()
      })
    }
  })
 })

 function checkDuplicateItem(id, names,s_tag){
        //alert(id);
        var arr = []
        var item_id_class = $('.item_id_class')
        if(item_id_class.length>0){
            item_id_class.each(function(index, item){
                arr.push({item:$(item).val(), id:$(item).attr('id').split('_')[1]})
            })
        }
        var flag = inArray(names.id, arr)
        if(flag[0]){
            var duplicateItemId = flag[1]
            $('#ItemCode_'+duplicateItemId).parent().parent('tr').addClass('duplicate')
            alert('You have selected duplicate Item!')
        }else{

      //  alert(names.id+'@'+names.item_name+'@'+names.item_desc+'@'+
      //    names.vUnitName+'@'+names.item_bal_stock+'@'+names.item_unit+'@'+
      //    names.item_price);

        $('#ItemCodeId_' + id[1]).val(names.id);
        $('#ItemCode_' + id[1]).val(names.item_code);
        $('#ItemBarCode_' + id[1]).val(names.item_bar_code);
        //$('#itemid_' + id[1]).val(names.item_name);
        $('#ItemDesc_' + id[1]).val(names.item_desc);
        //$('#Price_' + id[1]).val(names.item_price);
        $('#Unit_' + id[1]).val(names.item_unit);
        //$('#Stock_' + id[1]).val(names.item_bal_stock);
        //$('#Comm_' + id[1]).val(names.item_comm_val);
        if(s_tag) setDropdownItemList(names.id,id[1]); // this is for selection item code
        else $('#ItemCode_' + id[1]).val(names.item_code); // this is for selection item box
        $('#lotno_'+id[1]).focus()
      }
    }

    function inArray(needle, haystack) {
        var length = haystack.length;
        /*for(var i = 0; i < length; i++) {
            if(haystack[i].item == needle) return [true, haystack[i].id];
        }*/
        return [false];
    }

    function setDropdownItemList(itemid,id){
      //alert(document.getElementById('itemid_'+id)+':::');
      //alert(itemid+':::'+id);
      $("#itemid_"+id+" > [value=" + itemid + "]").attr("selected", "true").trigger('chosen:updated');
    //  $("#delivered_to").attr("selected", "true").trigger('chosen:updated');

    }

    function getDropdownStorageList(i){
      var comp_code = $('#company_code').val();
      var w_house = $('#itm_warehouse').val();
      i = parseInt(i);
      $.get('{{ url('/') }}/storageLookup/'+comp_code+'/'+w_house, function(response) {
        var selectList = $('select[id="Storage_'+i+'"]');
        selectList.chosen();
        selectList.empty();
        $.each(response, function(index, element) {
          selectList.append('<option value="' + element.id + '">' +element.stor_code +'('+ element.stor_name +')</option>');
        });
        selectList.trigger('chosen:updated');
      });
    }

    function getDropdownLotList(i,itemid,storageid,oldLot){
      i = parseInt(i);
      //alert(i + ","+ itemid);
      $.get('{{ url('/') }}/LotLookup/'+itemid+'/'+storageid, function(response) {
        var selectList = $('select[id="lotno_'+i+'"]');
        selectList.chosen();
        selectList.empty();
        //$('#itemid_' + i).append('<option value="">--Select Item--</option>');
        selectList.append('<option value="" disabled selected>--Item LOT'+i+'--</option>');
        $.each(response, function(index, element) {
          //alert(element.id+ ' SD ' +element.item_name);
          if (oldLot ==  element.item_lot_no){
            selectList.append('<option value="' + element.item_lot_no + '" selected>' + element.item_lot_no +' ('+ element.stock +')</option>');
          }else{
            selectList.append('<option value="' + element.item_lot_no + '">' + element.item_lot_no +' ('+ element.stock +')</option>');
          }
        });
        selectList.trigger('chosen:updated');
      });
    }

    function getDropdownItemList(i,oldItem){
      var compcode = $('#company_code').val();
      var custid = $('#customer_id').val();
      i = parseInt(i);
    //  alert(i + ","+ compcode + "," + custid);
      $.get('{{ url('/') }}/itemLookup/'+compcode+'/'+custid, function(response) {
        var selectList = $('select[id="itemid_'+i+'"]');
        selectList.chosen();
        selectList.empty();
        //$('#itemid_' + i).append('<option value="">--Select Item--</option>');
        selectList.append('<option value="" disabled selected>--Select Item1--</option>');
        $.each(response, function(index, element) {
          //alert(element.id+ ' SD ' +element.item_name);
          if (oldItem ==  element.id){
            selectList.append('<option value="' + element.id + '" selected>' + element.item_name +' ('+ element.itm_cat_name +')</option>');
          }else{
            selectList.append('<option value="' + element.id + '">' + element.item_name +' ('+ element.itm_cat_name +')</option>');
          }
        });
        selectList.trigger('chosen:updated');
      });
    }

    function getDropdownDeliveredToList(){
      var custid = $('#customer_id').val();
      //alert(custid);
      $.get('{{ url('/') }}/deliveredToLookup/' + custid, function(response) {
        var selectList = $('select[id="delivered_to"]');
        selectList.chosen();
        selectList.empty();
        //selectList.append('<option value="">--Delivered To--</option>');
        $.each(response, function(index, element) {
        //  alert(element.id + "," + element.deliv_to);
          selectList.append('<option value="' + element.id + '">' + element.deliv_to + '</option>');
        });
        selectList.trigger('chosen:updated');
      });

    }
    
    function getDropdownCourrierToList(company_code,customer_id){
      var compcode = company_code;
      var custid = customer_id;
      var result_cust_courrier_id = 0;
     // alert('get-cust-courrier-inf/getdetails/'+compcode+'/'+custid+'/getfirst');
      
      $.ajax({  //create an ajax request to display.php
        type: "GET",
        url: 'get-cust-courrier-inf/getdetails/'+compcode+'/'+custid+'/getfirst',
        success: function (data) {
            //alert(data.cust_courrier_id);
            result_cust_courrier_id = parseInt(data.cust_courrier_id);
        }
      });

      //alert('PP:'+result_cust_courrier_id);
      $.get('{{ url('/') }}/courrierToLookup/' + compcode, function(response) {
        var selectList = $('select[id="courr_id"]');
        selectList.chosen();
        selectList.empty();
        selectList.append('<option value="">--Courrier To--</option>');
        $.each(response, function(index, element) {
        //  alert(element.id + "," + element.deliv_to);
          if(result_cust_courrier_id == element.id){
              selectList.append('<option value="' + element.id + '" selected>' + element.courrier_to + '</option>');
          }
          selectList.append('<option value="' + element.id + '">' + element.courrier_to + '</option>');
        });
        selectList.trigger('chosen:updated');
      });

    }
    
  </script>
@stop
