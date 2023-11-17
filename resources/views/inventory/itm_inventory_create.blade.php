@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/blogic_css/sales_tb.css') }}" />

@stop
@section('content')
<section class="content">
  <input type="hidden" name="menu_selection" id="menu_selection" value="MM@1" class="form-control" required>

<div class="title">
  <div  style="background-color:#e0e0e0" class="widget-header widget-header-small">
    <h6 class="widget-title smaller">
    <font size="2" color="blue"><b>Purchase Form</b></font>
    </h6>
    <div class="widget-toolbar">
      <a href="{{route('itm.inventory.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
    </div>
  </div>
</div>
@if(Session::has('message'))
 <div class="row">
   <div class="col-md-12">
     <p class="alert alert-success"><font size="4" color="red"><b>{{ Session::get('message') }}</b></font></p>
   </div>
</div>
@endif
    <div class="widget-body">
      <div class="widget-main">
        <form id="get_Form" action="{{ route('get.po') }}" method="post"  class="delete_form">
        {!! csrf_field() !!}
         <div class="row">
          <div class="col-md-3">
            <div class="input-group ss-item-required">
              <select name="purchase_no" class="chosen-select" id="purchase_no" required>
              <option value="" >--Select PO--</option>
                  @if ($po_list->count())
                      @foreach($po_list as $list)
                          <option {{ $purchase_no == $list->id ? 'selected' : '' }} value="{{$list->id}}" >{{ $list->po_order_ref }}</option>
                      @endforeach
                  @endif
              </select>
             </div>
           </div>
           <div class="col-md-1">
               <div class="input-group">
                <button type="submit" class="btn btn-primary btn-sm" >Get PO</button>
              </div>
           </div>
        </form>
      </div>


      <form id="inv_Form" action="{{route('itm.inventory.store')}}" method="post">
      {!! csrf_field() !!}
      <input type="hidden" name="res_purchase_no" id="res_purchase_no" value="{{$purchase_no}}" class="form-control" />
      <div class="row">
          <div class="col-md-3">
            <div class="input-group ss-item-required">
                <div class="input-group-prepend ">
                    <div class="input-group-text" style="min-width:70px">Purchase Date:</div>
                </div>
                <input type="text" size = "10" name="purchase_date" onclick="displayDatePicker('purchase_date');"  value={{ date('d-m-Y',strtotime($purchase_date)) }} />
                <a href="javascript:void(0);" onclick="displayDatePicker('purchase_date');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
            </div>
          </div>
          <div class="col-md-3">
            <div class="input-group ss-item-required">
              <div class="input-group-prepend">
                <div class="input-group-text" style="min-width:80px">PI No:</div>
              </div>
              <input type="text" name="pi_no" id="pi_no" value="{{$pi_no}}" class="form-control" />
            </div>
          </div>
          <div class="col-md-4">
               <div class="input-group">
                 <div class="input-group-prepend">
                     <div class="input-group-text" style="min-width:80px">Company:</div>
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
        <div class="col-md-4">
            <div class="input-group ss-item-required">
              <select name="supplier_id" id="supplier_id" class="chosen-select"  onchange="getSupplierDetails(this.value)">
                  <option value="" disabled selected>- Select Supplier -</option>
                  @foreach($suppliers as $supplier)
                      <option {{ $supplier_id == $supplier->id ? 'selected' : '' }} value="{{ $supplier->id }}">{{ $supplier->supp_name }}</option>
                  @endforeach
              </select>
              @error('supplier_id')
              <span class="text-danger">{{ $message }}</span>
              @enderror
              </div>
          </div>
          <div class="col-md-4">
             <div class="input-group-prepend ss-item-required">
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
          
         <div class="col-md-3">
           <div class="input-group ss-item-required">
             <input type="hidden" name="result_supplier_id" id="result_supplier_id" value="{{$supplier_id}}" class="form-control" readonly required/>
             <input type="hidden" name="result_storage_id" id="result_storage_id" value="{{old('result_storage_id')}}" class="form-control" readonly required/>
             <input type="hidden" name="currencyValue" id="currencyValue" value="{{$po_m_curr_rate}}" class="form-control changesCurrency" required/> 
           </div>
         </div>
        </div>
        <div class="row">
             <div class="col-md-8">
                 <div class="input-group ss-item-required">
                     <div class="input-group-prepend">
                         <div class="input-group-text" style="min-width:80px">Comments:</div>
                     </div>
                     <textarea name="comments" rows="2" cols="100" class="form-control config" maxlength="500" required>{{ old('comments') }}</textarea>
                 </div>
              </div>
       </div>
       <br/>
       <div class="row">
         <div class="col-md-12">
           <div class="tab-content">
               <div role="tabpanel" class="tab-pane active" id = "itemdetails">
                 <div class="row">
                   <div class="col-md-12 input-group">
                     <table id="salesTable" class="table table-striped table-data table-view">
                       <thead class="salesTable">
                         <th width="1.5%" class="text-center">&nbsp;&nbsp;</th>
                         <th width="2%" style="display: none" class="text-center">Id</th>
                         <th width="5%" style="display: none" class="text-center">Code</th>
                         <th width="7%" class="text-center">Barcode</th>
                         <th width="21%" class="text-center">Item Name</th>
                         <th width="8%" style="display: none" class="text-center">Item Desc</th>
                         <th width="5%" class="text-center">Unit</th>
                         <th width="10%" class="text-center">Expire Date</th>
                         <th width="7%" class="text-center">Order Qty</th>
                         <th width="6%" class="text-center">Balance</th>
                         <th width="15%" class="text-center">LOT No</th>
                         <th width="7%" class="text-center">Rec Qty</th>
                         <th width="5%" class="text-center">Rate</th>
                         <th width="8%" class="text-center">Amount<br/>(USD)</th>
                         <th width="8%" class="text-center">Amount<br/>(BDT)</th>
                         <th width="15%" class="text-center">Remarks</th>
                         <th width="3.5%" class="text-center">&nbsp;</th>
                     </thead>
                   <tbody class="salesTable" style="background-color: #ffffff;">
                     <?php $i = 1; ?>
                     @foreach($rows_d as $d)
                     <tr>
                       <td width="1.5%" class="text-center">{{$i}}</td>
                       <td width="2%"style="display: none"><input type="text" data-type="PurorderDetId" name="PurorderDetId[]" id="PurorderDetId_{{$i}}" value="{{$d->id}}"  class="form-control item_id_class" autocomplete="off"></td>
                       <td width="2%" style="display: none"><input type="text" data-type="ItemCodeId" name="ItemCodeId[]" id="ItemCodeId_{{$i}}" value="{{$d->po_item_id}}"  class="form-control item_id_class" autocomplete="off"></td>
                       <td width="5%" style="display: none"><input type="text" data-type="ItemCode" name="ItemCode[]" id="ItemCode_{{$i}}" value="{{$d->item_code}}" class="form-control autocomplete_txt" autocomplete="off"></td>
                       <td width="7%"><input type="text" data-type="ItemBarCode" name="ItemBarCode[]" id="ItemBarCode_{{$i}}" value="{{$d->item_bar_code}}" onKeyUp="loadItemsDetByBarcode(this.id,this.value)" class="form-control autocomplete_barcode_txt" autocomplete="off"></td>
                       <td width="23%">
                       <div><select data-type="itemid" name="itemid[]"  id ="itemid_{{$i}}" class="form-control chosen-select" onchange="loadItemsDet(this.id,this.value)">
                           <option value="" disabled selected>- Select Item -</option>
                           @foreach($item_list as $cmb)
                               <option {{ $cmb->id == $d->po_item_id ? 'selected' : '' }} value="{{ $cmb->id }}">{{ $cmb->item_desc }}:{{ $cmb->item_name }}({{ $cmb->itm_cat_name }})</option>
                           @endforeach
                         </select></div>
                      </td>
                       <td width="8%" style="display: none"><input type="text" data-type="ItemDesc" name="ItemDesc[]" id="ItemDesc_{{$i}}" class="form-control" autocomplete="off" ></td>
                       <td width="5%" style="display: none" align="center">
                         <div><select data-type="Storage" name="Storage[]"  id ="Storage_{{$i}}" class="form-control chosen-select">
                             @foreach($stor_list as $stor)
                                 <option {{ $stor->id == $d->po_storage_id ? 'selected' : '' }} value="{{ $stor->id }}">{{ $stor->stor_code }}({{ $stor->stor_name }})</option>
                             @endforeach
                           </select></div>
                       </td>
                       <td width="4%" align="center"><input type="text" data-type="Unit" name="Unit[]" id="Unit_{{$i}}" value="{{$d->po_item_unit}}" class="form-control input-sm"  autocomplete="off" readonly></td>
                       <td width="12%" align="center"> <input type="text" size = "8" name="exp_date_{{$i}}" id="exp_date_{{$i}}" onclick="displayDatePicker('exp_date_{{$i}}');"  value={{ date('d-m-Y',strtotime($purchase_date)) }} />
                       <a href="javascript:void(0);" onclick="displayDatePicker('exp_date_{{$i}}');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a></td>
                       <td width="8%" align="center"><input type="text" data-type="OrderQty" name="OrderQty[]" id="OrderQty_{{$i}}" value="{{$d->po_item_qty}}" class="form-control input-sm" style="font-weight:bold; text-align: center;" autocomplete="off" readonly></td>
                       <td width="8%" align="center"><input type="text" data-type="Stock" name="Stock[]" id="Stock_{{$i}}" value="{{$d->po_item_qty-$d->po_item_qty_rec}}" class="form-control input-sm" style="font-weight:bold; text-align: center;" autocomplete="off" readonly></td>
                       <td width="15%" align="center"><input type="text" data-type="LotNo" name="LotNo[]" id="LotNo_{{$i}}" class="form-control input-sm" style="font-weight:bold; text-align: center;" autocomplete="off" ></td>
                       <td width="7%" align="center"><input type="text" data-type="Qty" name="Qty[]" id="Qty_{{$i}}" value="{{$d->po_item_qty-$d->po_item_qty_rec}}" onkeydown="enter(this.id,this.value)" class="form-control input-sm changesNo iQty" style="font-weight:bold; text-align: center;" autocomplete="off" ></td>
                       <td width="5%" align="right"><input type="text" data-type="Rate" name="Rate[]" id="Rate_{{$i}}" value="{{$d->po_item_price}}" onkeydown="enter(this.id,this.value)" class="form-control input-sm changesNo iRate" style="font-weight:bold; text-align: center;" autocomplete="off" readonly></td>
                       <td width="8%" align="right"><input type="text" data-type="Amount" name="Amount[]" id="Amount_{{$i}}" value="{{$d->po_item_qty * $d->po_item_price}}" class="form-control input-sm iAmount" style="font-weight:bold; text-align: right;" autocomplete="off" readonly></td>
                       <td width="8%" align="right"><input type="text" data-type="AmountBDT" name="AmountBDT[]" id="AmountBDT_{{$i}}" value="{{$d->po_item_qty * $d->po_item_price*$d->po_d_curr_rate}}"class="form-control input-sm iAmountBDT" style="font-weight:bold; text-align: right;" autocomplete="off" readonly></td>
                       <td width="15%" align="right"><input type="text" data-type="Remarks" name="Remarks[]" id="Remarks_{{$i}}" class="form-control input-sm" style="font-weight:bold; text-align: left;" autocomplete="off" ></td>
                       <td width="3.5%"><div class="btn-group btn-corner"><button type="button" tabindex="-1" class="btn btn-danger btn-xs delete" title="Delete This Row" onclick="removeRow(this)"><i class="fa fa-trash"></i></button></div></td>
                   </tr>
                   <?php $i = $i + 1; ?>
                   @endforeach
                    </tbody>
                 </table>
               </div>

              </div>

              <div class="row">
                <div class="col-md-10">
                 <table class="table table-striped table-data table-view">
                   <tbody style="background-color: #ffffff;">
                     <tr>
                       <td colspan="10" align="right">&nbsp;</td>
                       <td width="2%" align="center"><input type="text" data-type="total_qty" name="total_qty" id="total_qty" value="0" style="font-weight:bold; text-align: right;" readonly>&nbsp;</td>
                       <td width="2%" align="right"><input type="text" data-type="total_amount" name="total_amount" id="total_amount" value="0.00" style="font-weight:bold; text-align: right;" readonly>(USD)</td>
                       <td width="2%" align="right"><input type="text" data-type="total_amount_bdt" name="total_amount_bdt" id="total_amount_bdt" value="{{$po_total_amount_BDT}}" style="font-weight:bold; text-align: right;" readonly>(BDT)</td>
                       <td width="3%"></td>
                     </tr>
                    </tbody>
                 </table>
               </div></div>

               </div>
           </div>
         </div>
       </div>
      <div class="row justify-content-left">
        <div class="col-sm-12 text-left">
            <button class="btn btn-sm btn-success" type="button" id='btn1' ><i class="fa fa-save"></i> Save</button>
            <button class="btn btn-sm btn-success" type="button" id='btn2'><i class="fa fa-save"></i>Print</button>
            <a href="{{route('itm.inventory.index')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> List</a>
        </div>
      </div>
    </div>
    </div>
  </form>
</section>
@stop
@section('pagescript')
<script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/chosen.jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/ace-elements.min.js') }}"></script>
<script src="{{ asset('assets/js/ace.min.js') }}"></script>
<script src="{{ asset('assets/blogic_js/sel_box_search.js') }}"></script>

<script>
var form = document.getElementById('inv_Form');
document.getElementById('btn1').onclick = function() {
  form.action = '{{route("itm.inventory.store")}}';
  if(formcheck()){
    form.submit();
  }
}

document.getElementById('btn2').onclick = function() {
  form.action = '{{route("itm.inventory.store")}}';
  if(formcheck()){
    form.submit();
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
  return isSubmit;
  //if(isSubmit) formSubmit();
  //console.log(fields);
}

function formSubmit()
{
    $('#inv_Form').submit()
}
</script>

<script>
$(document).ready(function() {
  totalQuantityCount();
  totalAmountCount();
  totalAmountBDTCount();
});

$(document).on('change keyup blur', '.changesNo', function () {
  id_arr = $(this).attr('id');
  id = id_arr.split("_");
  //alert(id[0]);
  currencyValue = $('#currencyValue').val();
  price         = $('#Rate_' + id[1]).val();
  quantity      = $('#Qty_' + id[1]).val();

  if (quantity != '' && price != ''){
      amount = (parseFloat(price)*parseFloat(quantity)).toFixed(2);
      $('#Amount_' + id[1]).val(amount);
      bdamount = (parseFloat(amount) * parseFloat(currencyValue)).toFixed(2);
      $('#AmountBDT_' + id[1]).val(bdamount);
  }else{
    $('#Amount_' + id[1]).val(0);
    $('#AmountBDT_' + id[1]).val(0);
  }

  stockQuantity = parseFloat($('#Stock_' + id[1]).val()).toFixed(2);
  if(stockQuantity >= 0){
      if(parseFloat(quantity) > parseFloat(stockQuantity)){
          $('#Qty_' + id[1]).val(stockQuantity);
      }
  }

  totalQuantityCount();
  totalAmountCount();
  totalAmountBDTCount();
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

function totalAmountCount()
{
    var total_amount = 0;
    $('.iAmount').each(function(){
        if(parseFloat($(this).val())>0)
            total_amount += parseFloat($(this).val());
    });
    $('#total_amount').val(parseFloat(total_amount).toFixed(2));
}

function totalAmountBDTCount()
{
    var total_amount_bdt = 0;
    $('.iAmountBDT').each(function(){
        if(parseFloat($(this).val())>0)
            total_amount_bdt += parseFloat($(this).val());
    });
  //  alert(total_amount_bdt);
    $('#total_amount_bdt').val(parseFloat(total_amount_bdt).toFixed(2));
}

function getStorageByWearId(w_house){
    var comp_code = $('#company_code').val();
  //  alert('get-storage-inf/getdetails/'+comp_code+'/'+w_house+'/getfirst');

    $.ajax({  //create an ajax request to display.php
      type: "GET",
      url: '/get-storage-inf/getdetails/'+comp_code+'/'+w_house+'/getfirst',
      success: function (data) {
        $("#result_storage_id").val(data.id)
      }
    });
}

function loadItemsDet(el,itemid){
    var purchase_no = $('#purchase_no').val();
    //alert(purchase_no);
    /*alert(el+' '+itemid)
    id_arr = el
    id = id_arr.split("_")
    alert(id[1]); */
    //alert('/get-po-item-code/getdetails/'+itemid+'/'+purchase_no+'/getfirst');
    $.get('/get-po-item-code/getdetails/'+itemid+'/'+purchase_no+'/getfirst', function(data){
    item = data.data
    }).then(function(){
      id_arr = el
      id = id_arr.split("_")
      $('tr.duplicate').removeClass('duplicate')
      checkDuplicateItem(id, item,false)
      getDropdownStorageList(id[1])
      //  calcluteTotalBill()
      //  totalQuantityCount()
    })

}

function loadItemsDetByBarcode(el,itembarcode){
    var so_id  = $('#so_id').val();
    //alert(el+' Item:'+itembarcode+' SO:'+so_id);
    if (itembarcode != ''){
      $.get('/get-item-bar-code/getdetails/'+itembarcode+'/getfirst', function(data){
      item = data.data
      }).then(function(){
        id_arr = el
        id = id_arr.split("_")
        $('tr.duplicate').removeClass('duplicate')
        checkDuplicateItem(id, item,false)
        getDropdownItemList(id[1],item.id)
      })
    }
}

function getSupplierDetails(custid){
  var customer_id = $('#supplier_id').val();
  $('#result_supplier_id').val(customer_id);
}

function enter(id,amount) {
    if(event.keyCode == 13) {
        field = id.split("_")[0];
        i = id.split("_")[1];
        if(amount > 0 && field == 'Qty' || field == 'Rate' ) row_increment();
    }
}

function removeRow(el) {
    $(el).parents("tr").remove();
    totalQuantityCount();
    totalAmountCount();
    totalAmountBDTCount();
}

function row_increment() {

    var i = $('#salesTable tr').length;
    exp_date = "'exp_date_"+i+"'";
    html = '<tr>';
    html += '<td width="1.5%" class="text-center">' + i + '</td>';
    html += '<td width="5%" style="display: none"><input type="text" data-type="PurorderDetId" name="PurorderDetId[]" id="PurorderDetId_' + i + '"  class="form-control item_id_class" autocomplete="off"></td>';
    html += '<td width="2%" style="display: none"><input type="text" data-type="ItemCodeId" name="ItemCodeId[]" id="ItemCodeId_' + i + '"  class="form-control item_id_class" autocomplete="off"></td>';
    html += '<td width="5%" style="display: none"><input type="text" data-type="ItemCode" name="ItemCode[]" id="ItemCode_' + i + '" class="form-control autocomplete_txt" autocomplete="off"></td>';
    html += '<td width="7%"><input type="text" data-type="ItemBarCode" name="ItemBarCode[]" id="ItemBarCode_' + i + '" onKeyUp="loadItemsDetByBarcode(this.id,this.value)" class="form-control autocomplete_barcode_txt" autocomplete="off"></td>';
    html += '<td width="23%">';
    html += '<div><select data-type="itemid" name="itemid[]"  id ="itemid_' + i + '" class="chosen-select" onchange="loadItemsDet(this.id,this.value)">';
    html += '<option value="" disabled selected>-- Select Item --</option>';
    html += '</select></div></td>';
    html += '<td width="8%" style="display: none"><input type="text" data-type="ItemDesc" name="ItemDesc[]" id="ItemDesc_' + i + '" class="form-control" autocomplete="off" readonly></td>';
    html += '<td width="5%" style="display: none">';
    html += '<div><select data-type="Storage" name="Storage[]"  id ="Storage_' + i + '" class="chosen-select" >';
    html += '</select></div></td>';
    html += '<td width="4%"><input type="text" data-type="Unit" name="Unit[]" id="Unit_' + i + '" class="form-control" autocomplete="off" readonly></td>';
    html += '<td width="12%" align="center"><input type="text" size = "8" name="exp_date_' + i + '" id="exp_date_' + i + '" onclick="displayDatePicker(' + exp_date + ');"  value={{ date("d-m-Y") }} />';
    html += '<a href="javascript:void(0);" onclick="displayDatePicker(' + exp_date + ');"><img src="{{ asset("assets/images/calendar.png") }}" alt="calendar" border="0"></a></td>';
    html += '<td width="8%"><input type="text" data-type="OrderQty" name="OrderQty[]" id="OrderQty_' + i + '" class="form-control input-sm" style="font-weight:bold; text-align: center;" autocomplete="off" readonly></td>';
    html += '<td width="8%"><input type="text" data-type="Stock" name="Stock[]" id="Stock_' + i + '" class="form-control input-sm" style="font-weight:bold; text-align: center;" autocomplete="off" readonly></td>';
    html += '<td width="15%"><input type="text" data-type="LotNo" name="LotNo[]" id="LotNo_' + i + '"  class="form-control input-sm" style="font-weight:bold; text-align: center;" autocomplete="off"></td>';
    html += '<td width="7%"><input type="text" data-type="Qty" name="Qty[]" id="Qty_' + i + '"  onkeydown="enter(this.id,this.value)" class="form-control input-sm changesNo iQty" style="font-weight:bold; text-align: center;" autocomplete="off"></td>';
    html += '<td width="5%"><input type="text" data-type="Rate" name="Rate[]" id="Rate_' + i + '" onkeydown="enter(this.id,this.value)" class="form-control input-sm changesNo iRate" style="font-weight:bold; text-align: center;" autocomplete="off"></td>';
    html += '<td width="8%"><input type="text" data-type="Amount" name="Amount[]" id="Amount_' + i + '" class="form-control input-sm iAmount" style="font-weight:bold; text-align: right;" autocomplete="off" readonly></td>';
    html += '<td width="15%"><input type="text" data-type="Remarks" name="Remarks[]" id="Remarks_' + i + '" class="form-control input-sm" style="font-weight:bold; text-align: left;" autocomplete="off" ></td>';
    html += '<td width="3.5%"><div class="btn-group btn-corner"><button type="button" tabindex="-1" class="btn btn-danger btn-xs delete" title="Delete This Row" onclick="removeRow(this)"><i class="fa fa-trash"></i></button></div></td>';
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
        $('LotNo_'+id[1]).focus()
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
        $('LotNo_'+id[1]).focus()
        //  calcluteTotalBill()
        //  totalQuantityCount()
      })
    }
  })
 })

 function checkDuplicateItem(id, names,s_tag){
      //  alert(id+'-'+names);
        var arr = [];
        var item_id_class = $('.item_id_class');
        if(item_id_class.length>0){
            item_id_class.each(function(index, item){
                arr.push({item:$(item).val(), id:$(item).attr('id').split('_')[1]});
            })
        }
        var flag = inArray(names.id, arr);
      //alert(flag[0]);
        if(flag[0]){
            var duplicateItemId = flag[1];
            $('#ItemCode_'+duplicateItemId).parent().parent('tr').addClass('duplicate');
            alert('You have selected duplicate Item!');
        }else{

      //  alert(names.id+'@'+names.item_name+'@'+names.item_desc+'@'+
      //    names.vUnitName+'@'+names.item_bal_stock+'@'+names.item_unit+'@'+
      //    names.item_price);

        /*var qty = 0;
        var j = 1;
        //Checked the user qty
        $('.iQty').each(function(){
            item = $('#ItemCodeId_'+j).val();
            //alert(j+'-'+item+' L-'+lot)
            if(parseFloat($(this).val())>0 && item == itemid)
                qty += parseFloat($(this).val());
            j += 1;
        });*/
        var bal = parseFloat(names.item_ord_qty) - parseFloat(names.item_bal_stock);
        //alert(names.item_level);
        $('#PurorderDetId_' + id[1]).val(names.item_level);
        $('#ItemCodeId_' + id[1]).val(names.id);
        $('#ItemCode_' + id[1]).val(names.item_code);
        $('#ItemBarCode_' + id[1]).val(names.item_bar_code);
        $('#itemid_' + id[1]).val(names.item_name);
        $('#ItemDesc_' + id[1]).val(names.item_desc);
        $('#Rate_' + id[1]).val(names.item_price);
        $('#Unit_' + id[1]).val(names.item_unit);
        $('#OrderQty_' + id[1]).val(names.item_ord_qty);
        $('#Stock_' + id[1]).val(bal);
        if(s_tag) setDropdownItemList(names.id,id[1]); // this is for selection item code
        else $('#ItemCode_' + id[1]).val(names.item_code); // this is for selection item box
        $('#LotNo_'+id[1]).focus()
      }
    }

    function inArray(needle, haystack) {
        var length = haystack.length;
        for(var i = 0; i < length; i++) {
            if(haystack[i].item == needle) return [true, haystack[i].id];
        }
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

    function getDropdownItemList(i,oldItem){
      var compcode = $('#company_code').val();
      var purchase_no = $('#purchase_no').val();
      i = parseInt(i);
      //alert('{{ url('/') }}/itempoLookup/'+compcode+'/'+purchase_no);
      $.get('{{ url('/') }}/itempoLookup/'+compcode+'/'+purchase_no, function(response) {
        var selectList = $('select[id="itemid_'+i+'"]');
        selectList.chosen();
        selectList.empty();
        //$('#itemid_' + i).append('<option value="">--Select Item--</option>');
        selectList.append('<option value="" disabled selected>--Select Item_'+i+'--</option>');
        $.each(response, function(index, element) {
          //alert(element.id+ ' SD ' +element.item_name);
          if (oldItem ==  element.id){
            selectList.append('<option value="' + element.id + '" selected>' + element.item_desc +':'+ element.item_name +' ('+ element.itm_cat_name +')</option>');
          }else{
            selectList.append('<option value="' + element.id + '">' + element.item_desc +':'+ element.item_name +' ('+ element.itm_cat_name +')</option>');
          }
        });
        selectList.trigger('chosen:updated');
      });
    }

  </script>
@stop
