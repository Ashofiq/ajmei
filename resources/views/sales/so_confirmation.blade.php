@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
@stop
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="SD@1" class="form-control" required>
<div class="title"></div>
  <legend>
<div class="title">
  <div class="widget-header widget-header-small">
    <font size="3" color="blue"><b>Sales Order Confirmation</b></font>
    <div class="widget-toolbar">
        <a href="{{route('sales.order.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
    </div>
  </div>
</div></legend>

<div class="widget-body">
  <div class="widget-main">
  <div class="row">
    <div class="col-12">
        @if(Session::has('message'))
           <p class="alert alert-success">{{ Session::get('message') }}</p>
        @endif
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      @csrf
      <table class="table table-striped table-data table-view">
        <thead class="thead-blue">
          <th style="display:none;" class="text-center" scope="col">Sys.ID</th>
          <th class="text-center" scope="col">Date</th>
          <th class="text-center" scope="col">Order No</th>
          <th class="text-center" scope="col">Order Ref</th>
          <th class="text-center" scope="col">Customer</th>
          <th class="text-center" scope="col">Delivered To</th>
          <th class="text-center" scope="col">Delivered Address</th>
          <th class="text-center" scope="col">Delivered Phone</th>
          <th class="text-center" scope="col">Total Amount</th>
          <th class="text-center" scope="col">Total Disc</th>
          <th class="text-center" scope="col">Total VAT</th>
          <th class="text-center" scope="col">Net Amount</th>
        </thead>
        <tbody>
          <?php $orderno = '';
          $comp_id = '';
          $isconfirmed = 0;?>
          @foreach($rows as $row)
          <?php $orderno = $row->so_order_no;
          $comp_id = $row->so_comp_id;
          $isconfirmed =  $row->so_is_confirmed;?>
          <tr>
            <td style=display:none;>{{ $row->id }}</td>
            <td>{{ $row->so_order_date }}</td>
            <td>{{ $row->so_order_no }}</td>
            <td>{{ $row->so_reference }}</td>
            <td>{{ $row->cust_name }}</td>
            <td>{{ $row->deliv_to }}</td>
            <td>{{ $row->so_del_add }}</td>
            <td>{{ $row->so_cont_no }}</td>
            <td>{{ $row->so_sub_total }}</td>
            <td>{{ $row->so_total_disc }}</td>
            <td>{{ $row->so_vat_value }}</td>
            <td>{{ $row->so_net_amt }}</td>
          </tr>
          @endforeach
          </tbody>
        </table>
      </div>
  </div>
  <br/>
  <form id="confirm_Form" action="{{route('sales.conf.confirmed')}}" method="post">
  <div class="row">
    <div class="col-md-12">
      @csrf
      <table class="table table-striped table-report">
        <thead class="thead-blue">
          <th style="display:none;" class="text-center" scope="col">SO Comp Id</th>
          <th style="display:none;" class="text-center" scope="col">SO Order Id</th>
          <th style="display:none;" class="text-center" scope="col">Order No</th>
          <th style="display:none;" class="text-center" scope="col">SO Order Det Id</th>
          <th style="display:none;" class="text-center" scope="col">Item Id</th>
          <th class="text-center" scope="col">Item Barcode</th>
          <th class="text-center" scope="col">Item Name</th>
          <th class="text-center" scope="col">Order Qty</th>
          <th class="text-center" scope="col">Confirm Qty</th>
        </thead>
        <tbody>
          <?php $i=1; ?>
          @foreach($det as $data)
          <tr>
            <td style="display:none;" ><input type="text" name="comp_id[]" id="comp_id{{$i}}" value="{{ $comp_id }}" readonly/></td>
            <td style="display:none;" ><input type="text" name="so_order_id[]" id="so_order_id{{$i}}" value="{{ $data->so_order_id }}" readonly/></td>
            <td style="display:none;" ><input type="text" name="orderno[]" id="orderno{{$i}}" value="{{ $orderno }}" readonly/></td>
            <td style="display:none;" ><input type="text" name="so_order_det_id[]" id="so_order_det_id{{$i}}" value="{{ $data->id }}" readonly/></td>
            <td style="display:none;" ><input type="text" name="so_item_id[]" id="so_item_id{{$i}}" value="{{ $data->so_item_id }}" readonly/></td>
            <td>{{ $data->item_bar_code }}</td>
            <td>{{ $data->item_name }} ({{ $data->itm_cat_name }})</td>
            <td align="center"><input type="text" data-type="ActualQty" name="ActualQty[]" id="ActualQty_{{$i}}" value="{{ $data->so_order_qty }}" style="font-weight:bold; text-align: center;" readonly/></td>
            <td align="center"><input type="text" data-type="ConfirmQty" name="ConfirmQty[]" id="ConfirmQty_{{$i}}" value="{{ ($data->so_order_con_qty>0) ? $data->so_order_con_qty:$data->so_order_qty }}" class="form-control input-sm changesNo" style="font-weight:bold; text-align: center;"/></td>
          </tr>
            <?php $i+=1;?>
          @endforeach
          </tbody>
        </table>
      </div>
  </div>
  @if($isconfirmed == 0)
  <div class="row justify-content-right">
      <div class="col-sm-12 text-right">
          <button class="btn btn-sm btn-success" type="button" onclick="formcheck(); return false"><i class="fa fa-save"></i> Confirm</button>
      </div>
  </div>
  @endif
 </form>
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

function formSubmit()
{
    $('#confirm_Form').submit()
}
</script>

<script type="text/javascript">
  $(document).on('change keyup blur', '.changesNo', function () {
    id_arr = $(this).attr('id');
    id = id_arr.split("_");
    ActualQty  = $('#ActualQty_' + id[1]).val();
    ConfirmQty = $('#ConfirmQty_' + id[1]).val();
    //alert(ActualQty +':::Con:'+ConfirmQty);
    if (parseInt(ConfirmQty)>parseInt(ActualQty)){
      alert("Not more than Order Qty");
      $('#ConfirmQty_'+id[1]).val(parseInt(ActualQty));
      $('ConfirmQty_'+id[1]).focus();
    }
  });
</script>

@stop
