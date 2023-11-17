@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/blogic_css/sales_tb.css') }}" />
    <!-- include summernote css/js -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

@stop
@section('content')
<section class="content">
  <input type="hidden" name="menu_selection" id="menu_selection" value="MM@1" class="form-control" required>
  <div class="title">
  <div  style="background-color:#e0e0e0" class="widget-header widget-header-small">
    <h6 class="widget-title smaller">
    <font size="2" color="blue"><b>Edit: Raw Materials Stock Receive Form</b></font>
    </h6>
    <div class="widget-toolbar">
      <a href="{{route('raw.itm.receive.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
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
  <form id="inv_Form" action="{{route('raw.itm.receive.update')}}" method="post">
    {{ csrf_field() }}
    <input type="hidden" id="id" name="id" value="{{ $rows_m->id }}" class="form-control  input-sm" autocomplete="off" required/>
    <input type="hidden" name="pur_order_no" id="pur_order_no" value="{{ $rows_m->raw_order_no}}" class="form-control  input-sm" autocomplete="off" />
    <div class="widget-body">
      <div class="widget-main">
         <div class="row"> 
          <div class="col-md-3">
            <div class="input-group ss-item-required">
                <div class="input-group-prepend ">
                    <div class="input-group-text" style="min-width:70px">Purchase Date:</div>
                </div>
                <input type="text" size = "15" name="purchase_date" onclick="displayDatePicker('purchase_date');"  value={{ date('d-m-Y',strtotime($rows_m->raw_order_date)) }} />
                <a href="javascript:void(0);" onclick="displayDatePicker('purchase_date');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
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
                             <option {{ $rows_m->raw_comp_id == $company->comp_id ? 'selected' : '' }} value="{{$company->comp_id}}" >{{ $company->comp_id }}-{{ $company->name }}</option>
                         @endforeach
                     @endif
                 </select>
                </div>
           </div> 
            <input type="hidden" name="currencyValue" id="currencyValue" value="{{$rows_m->raw_m_curr_rate}}" class="form-control changesCurrency" required/>
            <input type="hidden" name="currencyName" id="currencyName" value="{{$rows_m->raw_m_curr}}" class="form-control" required readonly/>
              
      </div>
      <div class="row">
        <div class="col-md-3">
          <div class="input-group ss-item-required">
            <div class="input-group-prepend">
              <div class="input-group-text" style="min-width:80px">PO No:</div>
            </div>
            <input type="text" name="purchase_no" id="purchase_no" value="{{$rows_m->raw_order_ref}}" class="form-control" required readonly/>
          </div>
        </div>

        <div class="col-md-5">
            <div class="input-group ss-item-required">
              <select name="supplier_id" id="supplier_id" class="chosen-select"  onchange="getSupplierDetails(this.value)">
                  <option value="" disabled selected>- Select Supplier -</option>
                  @foreach($suppliers as $supplier)
                      <option {{ $rows_m->raw_supp_id == $supplier->id ? 'selected' : '' }} value="{{ $supplier->id }}">{{ $supplier->supp_name }}</option>
                  @endforeach
              </select>
              @error('supplier_id')
              <span class="text-danger">{{ $message }}</span>
              @enderror
              </div>
          </div>

          <div class="col-md-3">
            <div class="input-group ss-item-required">
              <div class="input-group-prepend">
                <div class="input-group-text" style="min-width:80px">Supplier Code:</div>
              </div>
              <input type="text" name="result_supplier_id" id="result_supplier_id" value="{{$rows_m->raw_supp_id}}" class="form-control" readonly required/>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3">
            <div class="input-group ss-item-required">
              <div class="input-group-prepend">
                <div class="input-group-text" style="min-width:80px">PI No:</div>
              </div>
              <input type="text" name="pi_no" id="pi_no" value="{{ $rows_m->raw_pi_no }}" class="form-control" />
            </div>
          </div>
        </div>
        <div class="row">
         <div class="col-md-12">
             <div class="input-group ss-item-required">
                 <div class="input-group-prepend">
                     <div class="input-group-text" style="min-width:80px">Comments:</div>
                 </div>
                 <!--div class="summernote">summernote 1</div-->
                 <textarea class="summernote" rows="100" cols="100" name="comments" id="comments">{!! $rows_m->raw_comments !!}</textarea>
             </div>
          </div>
       </div>

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
                         <th width="7%" style="display: none" class="text-center">Barcode</th>
                         <th width="23%" class="text-center">Item Name</th>
                         <th width="23%" class="text-center">Item Specification</th>
                         <th width="4%" class="text-center">Unit</th>
                         <!--th width="10%" class="text-center">Expire Date</th>
                         <th width="8%" class="text-center">LOT No</th -->
                         <th width="6%" class="text-center">Qty</th>
                         <th width="6%" class="text-center">Rate</th>
                         <th width="8%" class="text-center">Amount</th>
                         <th width="8%" style="display: none"  class="text-center">Amount (BDT)</th>
                         <th width="15%" class="text-center">Remarks</th>
                         <th width="3.5%" class="text-center">&nbsp;</th>
                     </thead>
                   <tbody class="salesTable" style="background-color: #ffffff;">
                    <?php $i = 1; ?>
                    @foreach($rows_d as $d)
                       <tr>
                       <td width="1.5%" class="text-center">{{$i}}</td>
                       <td width="2%" style="display: none"><input type="text" data-type="ItemCodeId" name="ItemCodeId[]" id="ItemCodeId_{{$i}}" value="{{$d->raw_item_id}}" class="form-control item_id_class" autocomplete="off"></td>
                       <td width="5%" style="display: none"><input type="text" data-type="ItemCode" name="ItemCode[]" id="ItemCode_{{$i}}" value="{{$d->item_code}}" class="form-control autocomplete_txt" autocomplete="off"></td>
                       <td width="7%"  style="display: none" ><input type="text" data-type="ItemBarCode" name="ItemBarCode[]" id="ItemBarCode_{{$i}}" value="{{$d->item_bar_code}}" onKeyUp="loadItemsDetByBarcode(this.id,this.value)" class="form-control autocomplete_barcode_txt" autocomplete="off"></td>
                       <td width="23%">
                       <div><select data-type="itemid" name="itemid[]"  id ="itemid_{{$i}}" class="form-control chosen-select" onchange="loadItemsDet(this.id,this.value)">
                           <option value="" disabled selected>- Select Item -</option>
                           @foreach($item_list as $cmb)
                               <option  {{ $cmb->id == $d->raw_item_id ? 'selected' : '' }} value="{{ $cmb->id }}">{{ $cmb->item_name }}({{ $cmb->itm_cat_name }})</option>
                           @endforeach
                         </select></div>
                      </td>
                       <td width="23%"><input type="text" data-type="ItemDesc" name="ItemDesc[]" id="ItemDesc_{{$i}}" value="{{$d->raw_item_desc}}"  class="form-control" autocomplete="off" ></td>

                       <td width="4%" align="center"><input type="text" data-type="Unit" name="Unit[]" id="Unit_{{$i}}" value="{{$d->raw_item_unit}}" class="form-control input-sm"  autocomplete="off" readonly></td>

                       <!--td width="10%" align="center"> <input type="text" size = "10" name="exp_date_{{$i}}" id="exp_date_{{$i}}" onclick="displayDatePicker('exp_date_{{$i}}');"  value={{ date('d-m-Y',strtotime($d->pur_item_exp_dt)) }} />
                       <a href="javascript:void(0);" onclick="displayDatePicker('exp_date_{{$i}}');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a></td>
                       <td width="8%" align="center"><input type="text" data-type="LotNo" name="LotNo[]" id="LotNo_{{$i}}" value="{{$d->pur_lot_no}}" class="form-control input-sm" style="font-weight:bold; text-align: center;" autocomplete="off" ></td>
                       <td width="8%" style="display: none" align="center" ><input type="text" data-type="Stock" name="Stock[]" id="Stock_{{$i}}"  value="{{$d->item_bal_stock - $d->pur_item_qty }}" class="form-control input-sm" style="font-weight:bold; text-align: center;" autocomplete="off" readonly></td>
                       <td width="6%" style="display: none" align="center"><input type="text" data-type="OldQty" name="OldQty[]" id="OldQty_{{$i}}" value="{{$d->pur_item_qty}}" class="form-control input-sm" style="font-weight:bold; text-align: center;" autocomplete="off" ></td -->

                       <td width="6%" align="center"><input type="text" data-type="Qty" name="Qty[]" id="Qty_{{$i}}" value="{{$d->raw_item_qty}}" onkeydown="enter(this.id,this.value)" class="form-control input-sm changesNo iQty" style="font-weight:bold; text-align: center;" autocomplete="off" ></td>
                       <td width="6%" align="right"><input type="text" data-type="Rate" name="Rate[]" id="Rate_{{$i}}" value="{{$d->raw_item_price}}" onkeydown="enter(this.id,this.value)" class="form-control input-sm changesNo iRate" style="font-weight:bold; text-align: center;" autocomplete="off"></td>
                       <td width="8%" align="right"><input type="text" data-type="Amount" name="Amount[]" id="Amount_{{$i}}" value="{{$d->raw_item_qty * $d->raw_item_price}}" class="form-control input-sm iAmount" style="font-weight:bold; text-align: right;" autocomplete="off" readonly></td>
                       <td width="8%" align="right" style="display: none"><input type="text" data-type="AmountBDT" name="AmountBDT[]" id="AmountBDT_{{$i}}" value="{{$d->po_item_qty * $d->po_item_price * $d->po_d_curr_rate}}" class="form-control input-sm iAmountBDT" style="font-weight:bold; text-align: right;" autocomplete="off" readonly></td>
                       <td width="15%" align="right"><input type="text" data-type="Remarks" name="Remarks[]" id="Remarks_{{$i}}" value="{{$d->raw_item_remarks}}" class="form-control input-sm" style="font-weight:bold; text-align: left;" autocomplete="off" ></td>
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
                       <td width="2%" align="center"><input type="text" data-type="total_qty" name="total_qty" id="total_qty" value="{{$rows_m->raw_total_qty}}" style="font-weight:bold; text-align: right;" readonly></td>
                       <td width="2%" align="right"><input type="text" data-type="total_amount" name="total_amount" id="total_amount" value="{{$rows_m->raw_total_amount}}" style="font-weight:bold; text-align: right;" readonly></td>
                       <td width="2%" align="right" style="display: none" ><input type="text" data-type="total_amount_bdt" name="total_amount_bdt" id="total_amount_bdt" value="{{$rows_m->raw_total_amount_BDT}}" style="font-weight:bold; text-align: right;" readonly></td>
                      <td width="3%"></td>
                     </tr>
                    </tbody>
                 </table>
               </div></div>

               </div>


                <!--  upload image  -->

                <div class="container row">
                  <div class="mb-3">
                      <label class="form-label">More Image</label>

                      <fieldset class="form-group" style="background: green; color : white">
                          <a href="javascript:void(0)" onclick="$('#pro-image').click()"  
                          style="color : white">Upload Image</a>
                          <input type="file" id="pro-image" name="pro-image" style="display: none;"
                              class="form-control" multiple>
                      </fieldset>
                      <div class="preview-images-zone">
                        <!-- <div class="preview-image preview-show-1">
                          <div class="image-cancel" data-no="1">x</div>
                          <div class="image-zone"><img id="pro-img-1" src="https://img.purch.com/w/660/aHR0cDovL3d3dy5saXZlc2NpZW5jZS5jb20vaW1hZ2VzL2kvMDAwLzA5Ny85NTkvb3JpZ2luYWwvc2h1dHRlcnN0b2NrXzYzOTcxNjY1LmpwZw=="></div>
                          <div class="tools-edit-image"><a href="javascript:void(0)" data-no="1" class="btn btn-light btn-edit-image">edit</a></div>
                        </div> -->
                      <?php 
                        $images = explode(",", $rows_m->purchaseImages); //json_decode($row->purchaseImages);
                        foreach ($images as $key => $value) {     
                      ?>
                        <div class="preview-image preview-show-1">
                          <div class="image-cancel" data-no="1">x</div>
                          <div class="image-zone"><img width="48" src="{{ $value }}"></div>
                          <div class="tools-edit-image"><a href="javascript:void(0)" data-no="1" class="btn btn-light btn-edit-image">edit</a></div>
                        </div>
                      <?php
                        }
                      ?>
                          
                      </div>

                      <input type="hidden" name="multiImage" value="{{ $rows_m->purchaseImages }}" class="form-control multiImage">
                      <input type="button" class="form-control btn btn-success" value="Upload" id="uploadMultiImage">
                  </div>

                  <img class="loading" src="{{ url('/assets/loading.gif') }}" width="120" height="120" style="display:none">
              </div>


           </div>
         </div>
       </div>
      <div class="row justify-content-left">
        <div class="col-sm-12 text-left">
            <button class="btn btn-sm btn-success" type="button" id='btn1' ><i class="fa fa-save"></i> Update</button> 
            <a href="{{route('raw.itm.receive.index')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> List</a>
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

    // image upload
  $(document).ready(function() {
    document.getElementById('pro-image').addEventListener('change', readImage, false);
    
    $( ".preview-images-zone" ).sortable();
    
    $(document).on('click', '.image-cancel', function() {
        let no = $(this).data('no');
        $(".preview-image.preview-show-"+no).remove();
    });
});


var num = 4;
function readImage() {
    if (window.File && window.FileList && window.FileReader) {
        var files = event.target.files; //FileList object
        var output = $(".preview-images-zone");

        for (let i = 0; i < files.length; i++) {
            var file = files[i];
            if (!file.type.match('image')) continue;
            
            var picReader = new FileReader();
            
            picReader.addEventListener('load', function (event) {
                var picFile = event.target;

                var html =  '<div class="preview-image preview-show-' + num + '">' +
                            '<div class="image-cancel" data-no="' + num + '">x</div>' +
                            '<div class="image-zone"><img class="multipleImage" id="pro-img-' + num + '" src="' + picFile.result + '"></div>' +
                            '<div class="tools-edit-image"><a href="javascript:void(0)" data-no="' + num + '" class="btn btn-light btn-edit-image">edit</a></div>' +
                            '</div>';

                output.append(html);
                num = num + 1;
            });

            picReader.readAsDataURL(file);
        }
        $("#pro-image").val('');
    } else {
        console.log('Browser not support');
    }
}

//  end image upload


      //  
      $("#uploadMultiImage").click(function(event) {
        $('.loading').show();

        console.log($('.multipleImage').attr('src'));
        var baseImage = [];
        $(".multipleImage").each(function() {
            baseImage.push($(this).attr('src'));
        });
        console.log(baseImage);
        $.ajax({
            type: 'POST',
            url: '{{ url('/') }}/upload-image',
            data: {
                baseImage
            },
            success: function(data) {
                $('.multiImage').val(data);
                console.log(data);

                $('.loading').hide();
            },
            error: function(e) {
                console.log(e);
            }
        });
      });

var form = document.getElementById('inv_Form');
document.getElementById('btn1').onclick = function() {
  if(formcheck()){
    form.submit();
  }
}

document.getElementById('btn2').onclick = function() {
  form.action = '{{route("raw.itm.receive.update")}}';
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

  var currencyValue = $('#currencyValue').val();
  if(currencyValue <= 0 || currencyValue == '' ) {
    alert('Currency Value is required');
    isSubmit = false;
    return isSubmit;
  }

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
  $('.summernote').summernote();
});
$(document).on('change keyup blur', '.changesCurrency', function () {
    currencyValue = 1;
    var i = parseInt($('#salesTable tr').length);
  //  alert(currencyValue+i);
    for(j=0; j<i; j++){
      //alert(currencyValue+i+j);
      price     = $('#Rate_' + j).val();
      quantity  = $('#Qty_' + j).val();
      if (quantity != '' && price != ''){
          amount = (parseFloat(price)*parseFloat(quantity)).toFixed(2);
          $('#Amount_' + j).val(amount);
          bdamount = (parseFloat(amount) * parseFloat(currencyValue)).toFixed(2);
          $('#AmountBDT_' + j).val(bdamount);
      }else{
        $('#Amount_' + j).val(0);
        $('#AmountBDT_' + j).val(0);
      }
    }
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
    //alert(total_amount_bdt);
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
    //var itemid = $('#itemid_1').val()
    //alert(el+' '+itemid)
    $.get('/get-po-item-code/getdetails/'+itemid+'/getfirst', function(data){
    item = data.data
    }).then(function(){
      id_arr = el
      id = id_arr.split("_")
      $('tr.duplicate').removeClass('duplicate')
      checkDuplicateItem(id, item,false)
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
    html += '<td width="2%" style="display: none"><input type="text" data-type="ItemCodeId" name="ItemCodeId[]" id="ItemCodeId_' + i + '"  class="form-control item_id_class" autocomplete="off"></td>';
    html += '<td width="5%" style="display: none"><input type="text" data-type="ItemCode" name="ItemCode[]" id="ItemCode_' + i + '" class="form-control autocomplete_txt" autocomplete="off"></td>';
    html += '<td width="7%"  style="display: none" ><input type="text" data-type="ItemBarCode" name="ItemBarCode[]" id="ItemBarCode_' + i + '" onKeyUp="loadItemsDetByBarcode(this.id,this.value)" class="form-control autocomplete_barcode_txt" autocomplete="off"></td>';
    html += '<td width="23%">';
    html += '<div><select data-type="itemid" name="itemid[]"  id ="itemid_' + i + '" class="chosen-select" onchange="loadItemsDet(this.id,this.value)">';
    html += '<option value="" disabled selected>-- Select Item --</option>';
    html += '</select></div></td>';
    html += '<td width="23%"><input type="text" data-type="ItemDesc" name="ItemDesc[]" id="ItemDesc_' + i + '" class="form-control" autocomplete="off"></td>';
    //html += '<td width="5%" style="display: none">';
    //html += '<div><select data-type="Storage" name="Storage[]"  id ="Storage_' + i + '" class="chosen-select" >';
    //html += '</select></div></td>';
    html += '<td width="4%"><input type="text" data-type="Unit" name="Unit[]" id="Unit_' + i + '" class="form-control" autocomplete="off" readonly></td>';
    //html += '<td width="10%" align="center"><input type="text" size = "10" name="exp_date_' + i + '" id="exp_date_' + i + '" onclick="displayDatePicker(' + exp_date + ');"  value={{ date("d-m-Y") }} />';
    //html += '<a href="javascript:void(0);" onclick="displayDatePicker(' + exp_date + ');"><img src="{{ asset("assets/images/calendar.png") }}" alt="calendar" border="0"></a></td>';
    //html += '<td width="8%" align="center"><input type="text" data-type="LotNo" name="LotNo[]" id="LotNo_' + i + '" class="form-control input-sm" style="font-weight:bold; text-align: center;" autocomplete="off" ></td>';
    //html += '<td width="8%" style="display: none"><input type="text" data-type="Stock" name="Stock[]" id="Stock_' + i + '" class="form-control input-sm" style="font-weight:bold; text-align: center;" autocomplete="off" readonly></td>';
    html += '<td width="6%" style="display: none"><input type="text" data-type="OldQty" name="OldQty[]" id="OldQty_' + i + '"  class="form-control" style="font-weight:bold; text-align: center;" autocomplete="off"></td>';
    html += '<td width="6%"><input type="text" data-type="Qty" name="Qty[]" id="Qty_' + i + '"  onkeydown="enter(this.id,this.value)" class="form-control input-sm changesNo iQty" style="font-weight:bold; text-align: center;" autocomplete="off"></td>';
    html += '<td width="6%"><input type="text" data-type="Rate" name="Rate[]" id="Rate_' + i + '" onkeydown="enter(this.id,this.value)" class="form-control input-sm changesNo iRate" style="font-weight:bold; text-align: center;" autocomplete="off"></td>';
    html += '<td width="8%"><input type="text" data-type="Amount" name="Amount[]" id="Amount_' + i + '" class="form-control input-sm iAmount" style="font-weight:bold; text-align: right;" autocomplete="off" readonly></td>';
    html += '<td width="8%"  style="display: none" ><input type="text" data-type="AmountBDT" name="AmountBDT[]" id="AmountBDT_' + i + '" class="form-control input-sm iAmountBDT" style="font-weight:bold; text-align: right;" autocomplete="off" readonly></td>';
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
      $.get('/get-item-code/getdetails/'+ui.item.itm_id+'/getfirst', function(data){
      item = data.data
      }).then(function(){

        id_arr = ui.item.el
        id = id_arr.split("_")
        $('tr.duplicate').removeClass('duplicate')
        checkDuplicateItem(id, item,true)
        $('Qty_'+id[1]).focus()
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
    //  alert(ui.item.itm_id)
      $.get('/get-item-code/getdetails/'+ui.item.itm_id+'/getfirst', function(data){
      item = data.data
      }).then(function(){

        id_arr = ui.item.el
        id = id_arr.split("_")
        $('tr.duplicate').removeClass('duplicate')
        checkDuplicateItem(id, item,true)
        $('Qty_'+id[1]).focus()
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
        $('#itemid_' + id[1]).val(names.item_name);
        $('#ItemDesc_' + id[1]).val(names.item_desc);
        $('#Price_' + id[1]).val(names.item_price);
        $('#Unit_' + id[1]).val(names.item_unit);
        $('#Stock_' + id[1]).val(names.item_bal_stock);
        if(s_tag) setDropdownItemList(names.id,id[1]); // this is for selection item code
        else $('#ItemCode_' + id[1]).val(names.item_code); // this is for selection item box
        $('#Qty_'+id[1]).focus()
      }
    }

    function inArray(needle, haystack) {
        var length = haystack.length;
        for(var i = 0; i < length; i++) {
            if(haystack[i].item == needle) return [false, haystack[i].id];
        }
        return [false];
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

    function setDropdownItemList(itemid,id){
      //alert(document.getElementById('itemid_'+id)+':::');
      //alert(itemid+':::'+id);
      $("#itemid_"+id+" > [value=" + itemid + "]").attr("selected", "true").trigger('chosen:updated');
    //  $("#delivered_to").attr("selected", "true").trigger('chosen:updated');

    }

    function getDropdownItemList(i,oldItem){
      var compcode = $('#company_code').val();
      i = parseInt(i);
    //  alert(i + ","+ compcode + "," + custid);
      $.get('{{ url('/') }}/rawitemLookup/'+compcode, function(response) {
        var selectList = $('select[id="itemid_'+i+'"]');
        selectList.chosen();
        selectList.empty();
        //$('#itemid_' + i).append('<option value="">--Select Item--</option>');
        selectList.append('<option value="" disabled selected>--Select Item_'+i+'--</option>');
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

  </script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
@stop
