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
    <font size="2" color="blue"><b>Item opening Entry Form</b></font>
    </h6>
    <div class="widget-toolbar">
      <a href="{{route('itm.op.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
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
  <form id="itm_OP_Form" action="{{route('itm.op.store')}}" method="post">
    {{ csrf_field() }}
     <div class="widget-body">
      <div class="widget-main">
         <div class="row">
           <div class="col-md-4">
             <div class="input-group ss-item-required">
                 <div class="input-group-prepend ">
                     <div class="input-group-text" style="min-width:70px">Opening Date:</div>
                 </div>
                 <input type="text" size = "10" name="opening_date" id="opening_date" onclick="displayDatePicker('opening_date');"  value="{{ old('opening_date') == "" ? date('d-m-Y') : date('d-m-Y',strtotime(old('opening_date'))) }}" />
                 <a href="javascript:void(0);" onclick="displayDatePicker('opening_date');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a></td>

                 <!--input type="date" name="opening_date" value="{{ old('opening_date') == "" ? $opening_date : old('opening_date') }}" class="form-control  input-sm" autocomplete="off" required/ -->
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
       <div class="col-md-3">
         <div class="input-group ss-item-required">
           <input type="hidden" name="result_storage_id" id="result_storage_id" value="{{old('result_storage_id')}}" class="form-control" readonly required/>
         </div>
       </div>
     </div>
      <div class="row">
        <div class="col-md-8">
          <div class="input-group ss-item-required">
              <div class="input-group-prepend ">
                  <div class="input-group-text" style="min-width:70px">Description:</div>
              </div>
              <input type="text" name="itm_desc" value="{{ old('itm_desc')}}" class="form-control  input-sm" autocomplete="off" required/>
         </div>
        </div>
      </div>
       <div class="row">
         <div class="col-md-12">
          <table id="salesTable" class="table table-striped table-data table-report ">
            <thead class="salesTable">
              <tr>
                <th width="1%" class="text-center">&nbsp;&nbsp;</th>
                <th width="2%" style="display: none" class="text-center">Item Id</th>
                <th width="5%" style="display: none" class="text-center">Item Code</th>
                <th width="20%" class="text-center">Item Name</th>
                <th width="23%" class="text-center">Item Specification</th> 
                <th width="4%" class="text-center">Unit</th>
                <th width="6%" class="text-center">Opening Qty</th>
                <th width="6%" class="text-center">Rate</th>
                <th width="8%" class="text-center">Amount</th>
                <th width="15%" class="text-center">Remarks</th>
                <th width="3%" class="text-center">&nbsp;</th>
              </tr>
            </thead>
            <tbody class="salesTable" style="background-color: #ffffff;">
              <tr>
                <td width="1%" class="text-center">1</td>
                <td width="2%" style="display: none"><input type="text" data-type="ItemCodeId" name="ItemCodeId[]" id="ItemCodeId_1" class="form-control item_id_class" autocomplete="off"></td>
                <td width="5%" style="display: none"><input type="text" data-type="ItemCode" name="ItemCode[]" id="ItemCode_1" class="form-control autocomplete_txt" autocomplete="off"></td>
                <td width="20%">
                <div><select data-type="itemid" name="itemid[]"  id ="itemid_1" class="form-control chosen-select" onchange="loadItemsDet(this.id,this.value)">
                    <option value="" disabled selected>- Select Item -</option>
                      @foreach($item_list as $cmb)
                        <option  value="{{ $cmb->id }}">{{ $cmb->item_name }}({{ $cmb->itm_cat_name }})</option>
                      @endforeach
                  </select></div>
               </td>
                <td width="23%"><input type="text" data-type="ItemDesc" name="ItemDesc[]" id="ItemDesc_1" class="form-control" autocomplete="off"></td> 
                <td width="4%" align="center"><input type="text" data-type="Unit" name="Unit[]" id="Unit_1" class="form-control input-sm"  autocomplete="off" readonly></td>
                <td width="6%" align="center"><input type="text" data-type="Stock" name="Stock[]" id="Stock_1" class="form-control input-sm changesNo iQty" autocomplete="off" ></td>
                <td width="6%" align="center"><input type="text" data-type="Price" name="Price[]" id="Price_1" class="form-control input-sm changesNo iRate" onkeydown="enter(this.id,this.value)" class="form-control input-sm" autocomplete="off" ></td>
                <td width="8%" align="right"><input type="text" data-type="Amount" name="Amount[]" id="Amount_1" class="form-control input-sm iAmount" style="font-weight:bold; text-align: right;" autocomplete="off" readonly></td>
                <td width="15%" align="right"><input type="text" data-type="Remarks" name="Remarks[]" id="Remarks_1" onkeydown="enter(this.id,this.value)" class="form-control input-sm" style="font-weight:bold; text-align: left;" autocomplete="off" ></td> 
                <td width="3%"></td>
            </tr>
             </tbody>
          </table>
          </div>
     </div>
    <div class="row justify-content-left">
          <div class="col-sm-12 text-left">
              <button class="btn btn-sm btn-success" type="button" onclick="formcheck(); return false"><i class="fa fa-save"></i> Save</button>

              <a href="{{route('itm.op.index')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> List</a>
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

function formSubmit()
{
    $('#itm_OP_Form').submit()
}

function formcheck() {
  var isSubmit = true;
  var fields = $(".ss-item-required")
  .find("select, textarea, input").serializeArray();

  $.each(fields, function(i, field) {
    if (!field.value){
      alert(field.name + ' is required');
      isSubmit = false;
    }
  });
  if(isSubmit) formSubmit();
  console.log(fields);
}

function getStorageByWearId(w_house){
    var comp_code = $('#company_code').val();
  //  alert('get-storage-inf/getdetails/'+comp_code+'/'+w_house+'/getfirst');

    $.ajax({  //create an ajax request to display.php
      type: "GET",
      url: 'get-storage-inf/getdetails/'+comp_code+'/'+w_house+'/getfirst',
      success: function (data) {
        $("#result_storage_id").val(data.id)
      }
    });
}

function loadItemsDet(el,itemid){

    //var itemid = $('#itemid_1').val()
    //alert(el+' '+itemid)
   //alert('get-item-op-code/getdetails/'+itemid+'/getfirst');
    $.get('get-item-op-code/getdetails/'+itemid+'/getfirst', function(data){
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

</script>

<script>

function enter(id,amount) {
    if(event.keyCode == 13) {
        if(amount > 0) row_increment();
        i = id.split("_")[1];
        //alert(i);
        //getDropdownItemList(i,0);
    }
}

$(document).on('change keyup blur', '.changesNo', function () {
  id_arr = $(this).attr('id');
  id = id_arr.split("_");
  //alert(id[0]); 
  price         = $('#Price_' + id[1]).val();
  quantity      = $('#Stock_' + id[1]).val();

  if (quantity != '' && price != ''){
      amount = (parseFloat(price)*parseFloat(quantity)).toFixed(2);
      $('#Amount_' + id[1]).val(amount);
      bdamount = parseFloat(amount); 
  }else{
    $('#Amount_' + id[1]).val(0); 
  }
  totalQuantityCount();
  totalAmountCount(); 
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

function removeRow(el) {
    $(el).parents("tr").remove()
  //  totalAmount()
    //calcluteTotalBill();
}

function row_increment() {

    var i = $('#salesTable tr').length;
    exp_date = "'exp_date_"+i+"'";
    html = '<tr>';
    html += '<td width="1%" class="text-center">' + i + '</td>';
    html += '<td width="2%" style="display: none" ><input type="text" data-type="ItemCodeId" name="ItemCodeId[]" id="ItemCodeId_' + i + '"  class="form-control item_id_class" autocomplete="off"></td>';
    html += '<td width="5%" style="display: none"><input type="text" data-type="ItemCode" name="ItemCode[]" id="ItemCode_' + i + '" class="form-control autocomplete_txt" autocomplete="off"></td>';
    html += '<td width="20%">';
    html += '<div><select data-type="itemid" name="itemid[]"  id ="itemid_' + i + '" class="chosen-select" onchange="loadItemsDet(this.id,this.value)">';
    html += '<option value="" disabled selected>-- Select Item --</option>';
    html += '</select></div></td>';
    html += '<td width="23%"><input type="text" data-type="ItemDesc" name="ItemDesc[]" id="ItemDesc_' + i + '" class="form-control" autocomplete="off"></td>';
     
    html += '<td width="4%"><input type="text" data-type="Unit" name="Unit[]" id="Unit_' + i + '" class="form-control" autocomplete="off" readonly></td>';
    html += '<td width="6%"><input type="text" data-type="Stock" name="Stock[]" id="Stock_' + i + '" class="form-control input-sm changesNo iQty" autocomplete="off"></td>';
    html += '<td width="6%"><input type="text" data-type="Price" name="Price[]" id="Price_' + i + '"  onkeydown="enter(this.id,this.value)" class="form-control input-sm changesNo iRate" autocomplete="off"></td>';
    html += '<td width="8%"><input type="text" data-type="Amount" name="Amount[]" id="Amount_' + i + '" class="form-control input-sm iAmount" style="font-weight:bold; text-align: right;" autocomplete="off" readonly></td>';
    html += '<td width="15%"><input type="text" data-type="Remarks" name="Remarks[]" id="Remarks_' + i + '" class="form-control input-sm" style="font-weight:bold; text-align: left;" autocomplete="off" ></td>';

    html += '<td width="3%"><div class="btn-group btn-corner"><button type="button" tabindex="-1" class="btn btn-danger btn-xs delete" title="Delete This Row" onclick="removeRow(this)"><i class="fa fa-trash"></i>Del</button></div></td>';
    html += '</tr>';

    $('#salesTable').append(html);
    getDropdownItemList(i,0); 
    document.getElementById('ItemCode_'+i).focus();
    i++;
}

$(document).on('keypress', '.autocomplete_txt', function () {
    compcode = $('#company_code').val()
    //  alert(compcode)
    el = $(this).attr('id')
    //alert(el)
    $(this).autocomplete({
      source: function(req, res){
      $.ajax({
          url: "/get-item-op-code/all",
          dataType: "json",
          data:{'itemcode':encodeURIComponent(req.term),
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
      $.get('get-item-op-code/getdetails/'+ui.item.itm_id+'/getfirst', function(data){
      item = data.data
      }).then(function(){

        id_arr = ui.item.el
        id = id_arr.split("_")
        $('tr.duplicate').removeClass('duplicate')
        checkDuplicateItem(id, item,true)
        $('Stock_'+id[1]).focus()
        //  calcluteTotalBill()
        //  totalQuantityCount()
      })
    }
  })
 })

    function checkDuplicateItem(id, names,s_tag){
      //  alert(id);
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
      //    names.vUnitName+'@'+names.item_bal_stock+'@'+names.item_unit);

        $('#ItemCodeId_' + id[1]).val(names.id);
        $('#itemid_' + id[1]).val(names.item_name);
        $('#ItemDesc_' + id[1]).val(names.item_desc);
        $('#Unit_' + id[1]).val(names.item_unit);
        $('#Price_' + id[1]).val(names.item_price); 
        if(s_tag) setDropdownItemList(names.id,id[1]); // this is for selection item code
        else $('#ItemCode_' + id[1]).val(names.item_code); // this is for selection item box
        $('#Stock_'+id[1]).focus()
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

    }

    // function getDropdownItemList(i){
    //   var compcode = $('#company_code').val();
    //   i = parseInt(i);
    // //  alert(i + ","+ compcode + "," + custid);
    //   $.get('{{ url('/') }}/itemLookup1/'+compcode, function(response) {
    //     var selectList = $('select[id="itemid_'+i+'"]');
    //     selectList.chosen();
    //     selectList.empty();
    //     //$('#itemid_' + i).append('<option value="">--Select Item--</option>');
    //     selectList.append('<option value="" disabled selected>--Select Item1--</option>');
    //     $.each(response, function(index, element) {
    //       //alert(element.id+ ' SD ' +element.item_name);
    //       selectList.append('<option value="' + element.id + '">' + element.item_desc +':'+ element.item_name +'('+ element.itm_cat_name +')</option>');
    //     });
    //     selectList.trigger('chosen:updated');
    //   });
    // }

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
  </script>
@stop
