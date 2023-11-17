@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
@stop
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="CRM@1" class="form-control" required>
<div class="title"></div>
  <legend>
<div class="title">
  <div class="widget-header widget-header-small">
    <font size="3" color="blue"><b> Sales Order To PP Production List</b></font>
    <div class="widget-toolbar">
        <a href="{{route('sales.pp.prod.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
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
  <form action="{{route('sales.pp.prod.index')}}" method="post">
  {{ csrf_field() }}
   <div class="row"> 
        <div class="col-md-2">
          <input type="text" name="order_no" id="order_no" value="{{old('order_no')}}" class="form-control" placeholder="Enter Order No"/>
        </div>
        <div class="col-md-4">
          <div class="input-group">
              <div class="input-group ">
                  <select name="customer_id" class="col-xs-6 col-sm-4 chosen-select" id="customer_id">
                      <option value="" disabled selected>- Select Customer -</option>
                      @foreach($customers as $customer)
                          <option {{ old('customer_id') == $customer->id ? 'selected' : '' }} value="{{ $customer->id }}">AJC-000{{ $customer->cust_slno }} -> {{ $customer->cust_name }}</option>
                      @endforeach
                  </select>
                  @error('customer_id')
                  <span class="text-danger">{{ $message }}</span>
                  @enderror
              </div>
         </div>
      </div>
      
      <div class="col-md-2">
            <div class="form-group">
              <input type="text" size = "9" name="fromdate" onclick="displayDatePicker('fromdate');"  value={{ date('d-m-Y',strtotime($fromdate)) }} />
              <a href="javascript:void(0);" onclick="displayDatePicker('fromdate');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
              <!-- input type="date" name="fromdate" id="fromdate" value="{{$fromdate}}" class="form-control" placeholder="dd/mm/YYYY" required/ -->
           </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
              <input type="text" size = "9" name="todate" onclick="displayDatePicker('todate');"  value={{ date('d-m-Y',strtotime($todate)) }} />
              <a href="javascript:void(0);" onclick="displayDatePicker('todate');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
    
              <!-- input type="date" name="todate" id="todate" value="{{ old('todate') == "" ? $todate : old('todate') }}" class="form-control" placeholder="To Date" required/ -->
           </div>
        </div>
        
      <div class="col-md-2">
        <button type="submit" name="submit"  class="btn btn-sm btn-info"><span class="fa fa-search"></span></button>
      </div>
  </div>
  </form>
<br/>
<form id="confirm_Form" action="{{route('sales.pp.prod.confirmed')}}" method="post">
  <div class="row">
    <div class="col-md-12">
      @csrf
      <table class="table table-striped table-data table-view">
        <thead class="thead-blue">
          <th style="display:none;" class="text-center" scope="col">Sys.ID</th>
          <th style="display:none;" class="text-center" scope="col">SO Comp Id</th>
          <th style="display:none;" class="text-center" scope="col">SO Order Id</th> 
          <th style="display:none;" class="text-center" scope="col">SO Order Det Id</th>
          <th style="display:none;" class="text-center" scope="col">Item Id</th>
          <th class="text-center" scope="col">Date</th>
          <th class="text-center" scope="col">Order No</th>
          <th class="text-center" scope="col">Order Ref</th>
          <th class="text-center" scope="col">FPO No</th>
          <th class="text-center" scope="col">Customer</th> 
          <th class="text-center" scope="col">Agree</th> 
          <th class="text-center" scope="col">Expected<br/>Delivery Date</th>
          <th class="text-center" scope="col">Item Name</th>
          <th class="text-center" scope="col">Item Specification</th> 
          <th class="text-center" scope="col">Size</th> 
          <th class="text-center" scope="col">Size/Pcs<br/>Weight (GM)</th> 
          <th class="text-center" scope="col">Qty(Pcs)</th> 
          <th class="text-center" scope="col">Total<br/>Weight(KG)</th> 
          <th class="text-center" scope="col">Sales<br/>Unit</th>  
          <th class="text-center" scope="col">Stock<br/>(KG)</th> 
          <th class="text-center" scope="col">Balance</th> 
          <th class="text-center" scope="col">Proposed<br/>Qty(Pcs)</th> 
          <th class="text-center" scope="col">Proposed<br/>Weight</th> 
        </thead>
        <tbody>
          <?php $order_no = '';  $i=1; ?>
          @foreach($rows as $row)  
            @if ($order_no == '' || $row->so_order_no != $order_no)
              <tr> 
                <td>{{ date('d-m-Y',strtotime($row->so_order_date)) }}</td>
                <td>{{ $row->so_order_no }}</td>
                <td>{{ $row->so_reference }}</td>
                <td>000{{ $row->so_fpo_no }}</td>
                <td>{{ $row->cust_name }}</td> 
                <td style="font-weight: bold; color : green"> {{ ($row->production_agree == 1) ? 'Yes' : ''  }}</td> 
                <td>{{ date('d-m-Y',strtotime($row->so_req_del_date)) }}</td>
                <td colspan="10" align="right">&nbsp;</td>
                <td align="right"><a href="{{ route('sales.order.prod.pdf',['id'=>$row->id, 'tag'=>'PP']) }}" class="btn btn-xs btn-info" title="Invoice" target="_blank">Print SO</a>
                  @if($row->production_agree == 0)
                    <a href="{{ route('sales.jute.prod.agree', $row->id) }}" class="btn btn-xs btn-info" title="Invoice" <?php echo ($row->production_agree == 1) ? 'disabled' : '' ?> >Agree</a>
                  @else
                    <button class="btn btn-xs btn-danger" title="Invoice" <?php echo ($row->production_agree == 1) ? 'disabled' : 'disabled'; ?> >Agree</button>
                  @endif
              </td>
              </tr> 
            @endif
            <tr>
              <td style="display:none;" ><input type="text" name="comp_id[]" id="comp_id{{$i}}" value="{{ $row->so_comp_id  }}" readonly/></td>
              <td style="display:none;" ><input type="text" name="so_order_id[]" id="so_order_id{{$i}}" value="{{ $row->id }}" readonly/></td> 
              <td style="display:none;" ><input type="text" name="orderno[]" id="orderno{{$i}}" value="{{ $row->so_order_no  }}" readonly/></td>
              <td style="display:none;" ><input type="text" name="so_order_det_id[]" id="so_order_det_id{{$i}}" value="{{ $row->so_order_det_id }}" readonly/></td>
              <td style="display:none;" ><input type="text" name="so_item_id[]" id="so_item_id{{$i}}" value="{{ $row->so_item_id }}" readonly/></td>
              <td colspan="7">&nbsp;</td>
              <td>{{ $row->item_name }}</td>
              <td>{{ $row->so_item_spec }}</td>
              <td>{{ $row->so_item_size }}</td> 
              <td>{{ $row->so_item_weight }}</td> 
              <td align="center"><input type="text" data-type="ActualQty" name="ActualQty[]" id="ActualQty_{{$i}}" value="{{ $row->so_item_pcs }}" 
              style="font-weight:bold; text-align: center;" size="8" readonly/></td> 
              <td>{{ $row->so_order_qty }}</td>
              <td>{{ $row->so_item_unit }}</td> 
              <td>{{ $row->stock }}</td>   
              <td>{{ $row->so_item_pcs - $row->so_order_con_qty }}</td>   
              <!-- <td align="center" width="8%"><input type="text" data-type="ConfirmQty" name="ConfirmQty[]" id="ConfirmQty_{{$i}}" value="{{ $row->so_item_pcs - $row->so_order_con_qty }}" class="form-control input-sm changesNo" style="font-weight:bold; text-align: center;"/></td> -->
              <td align="center" width="8%"><input type="text" data-type="ConfirmQty" name="ConfirmQty[]" id="ConfirmQty_{{$i}}" value="0" class="form-control input-sm changesNo" style="font-weight:bold; text-align: center;"/></td>

              <td align="center" width="8%"><input type="text" data-type="ConfirmWeight" name="ConfirmWeight[]" id="ConfirmWeight_{{$i}}" value="0" class="form-control input-sm changesNo" style="font-weight:bold; text-align: center;"/></td>

          </tr> 
          <?php $i+=1; $order_no = $row->so_order_no; ?>
          @endforeach
          </tbody>
        </table>
      </div>
  </div> 

  <div class="row justify-content-right">
      <div class="col-sm-12 text-right">
          <button class="btn btn-sm btn-success" type="button" onclick="formcheck(); return false"><i class="fa fa-save"></i> Confirm</button>
      </div>
  </div> 
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
      // alert("Not more than Order Qty");
      // $('#ConfirmQty_'+id[1]).val(parseInt(ActualQty));
      // $('ConfirmQty_'+id[1]).focus();
    }
  });
</script>

@stop
