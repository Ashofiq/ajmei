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
    <font size="3" color="blue"><b>Sales Order List </b></font>
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
  <form action="{{route('sales.order.index')}}" method="post">
  {{ csrf_field() }}
   <div class="row">
        <div class="col-md-2">
         <a href="{{route('sales.order.create')}}" class="btn btn-dark">
           <i class="mdi mdi-plus"></i>Add New</a>
        </div>
        <div class="col-md-2">
          <input type="text" name="order_no" id="order_no" value="{{old('order_no')}}" class="form-control" placeholder="Enter Order No"/>
        </div>
        <div class="col-md-3">
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
            <input type="text" size="9" name="todate" onclick="displayDatePicker('todate');"  value={{ date('d-m-Y',strtotime($todate)) }} />
            <a href="javascript:void(0);" onclick="displayDatePicker('todate');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
  
            <!-- input type="date" name="todate" id="todate" value="{{ old('todate') == "" ? $todate : old('todate') }}" class="form-control" placeholder="To Date" required/ -->
          </div>
      </div>


      <div class="col-md-1">
        <button type="submit" name="submit"  class="btn btn-sm btn-info"><span class="fa fa-search"></span></button>
      </div>
  </div>
  </form>
<br/>
  <div class="row">
    <div class="col-md-12">
      @csrf
      <table class="table table-striped table-data table-view">
        <thead class="thead-blue">
          <th class="text-center" scope="col" width="5%">Date</th>
          <th class="text-center" scope="col" width="5%">Order No</th>
          <th class="text-center" scope="col" width="5%">Order Ref</th>
          <th class="text-center" scope="col" width="5%">Customer</th> 
          <th class="text-center" scope="col" width="5%">Expected Delivery Date</th>
          <th class="text-center" scope="col" width="5%">Total Amount</th>
          <th class="text-center" scope="col" width="5%">Total Disc</th>
          <th class="text-center" scope="col" width="5%">Carring Cost</th>
          <th class="text-center" scope="col" width="5%">Labour Cost</th>
          <th class="text-center" scope="col" width="5%">Load/Unload Cost</th>
          <th class="text-center" scope="col" width="5%">Service Charge</th>
          <th class="text-center" scope="col" width="5%">Other Cost</th>
          <th class="text-center" scope="col" width="5%">Net Amount</th>
          <th class="text-center" scope="col" width="5%">Conf Date</th>
          <th class="text-center" scope="col" width="5%">FPO No</th>
          <th class="text-center" scope="col" width="5%">Remark</th>
          <th class="text-center" width="5%">Options</th>
        </thead>
        <tbody>
          @foreach($rows as $row)
          <tr>
            <td style="display:none"><input type="hidden" id="id" value="{{ $row->id }}"></td>
            <td width="5%">{{ date('d-m-Y',strtotime($row->so_order_date)) }}</td>
            <td width="5%">{{ $row->so_order_no }}</td>
            <td width="5%">{{ $row->so_reference }}</td>
            <td width="5%">{{ $row->cust_name }}</td> 
            <td width="5%">{{ date('d-m-Y',strtotime($row->so_req_del_date)) }}</td>
            <td width="5%">{{ $row->so_sub_total }}</td>
            <td width="5%">{{ $row->so_total_disc }}</td>
            <td width="5%" style="display: none">{{ $row->so_vat_value }}</td>
            <td width="5%">{{ $row->so_carring_cost }}</td>
            <td width="5%">{{ $row->so_labour_cost }}</td>
            <td width="5%">{{ $row->so_load_unload_cost }}</td>
            <td width="5%">{{ $row->so_service_charge }}</td>
            <td width="5%">{{ $row->so_other_cost }}</td>
            <td width="5%">{{ $row->so_net_amt }}</td>
            <td width="5%">{{ $row->so_confirmed_date }}</td>
            <td width="5%">@if($row->so_fpo_no != 0)
                  000{{ $row->so_fpo_no }}
                @endif
            </td>
            <td width="5%">
              <input style="width:65px" value="{{ $row->remark }}" name="remark" class="remark" id="remark">
            </td>
            <td width="5%">
            <a href="{{ route('sales.order.edit',$row->id) }}" class="btn btn-xs">Edit</a>

              <form  method="post" action="{{ url('/sales-order/destroy/'.$row->id.'/'.$row->so_is_confirmed) }}" class="delete_form">
                {{ csrf_field() }}
                @method('DELETE')
                <div class="btn-group btn-corner">
                    @if( $row->so_is_confirmed == 0 ) 
                        <!-- <a href="{{ route('sales.order.edit',$row->id) }}" class="btn btn-xs">Edit</a> -->
                        <a href="{{ route('sales.conf.confirmed1',$row->id) }}" class="btn btn-xs btn-warning">Confirm To Production</a> 
                        <button class='btn btn-danger btn-sm delete'  type="submit" onclick="return confirm('Are You Sure? Want to Delete It.');">Delete</button> 
                    @else  
                      <a href="{{ route('sales.conf.creation',$row->id) }}" class="btn btn-xs btn-success">Confirmed</a>
                    @endif 
                    <!-- href="{{ route('sales.order.pdf',$row->id) }}" -->
                </div>

              </form>

              <button class="btn btn-xs btn-info salseOrderpdf{{ $row->id }}" title="Invoice" data="{{ $row->id }}" id="salseOrderpdf{{ $row->id }}">Sales Order</button>

            </td>
          </tr>
          @endforeach
          </tbody>
        </table>
      </div>
  </div>
  <div class="col-md-12">
    <div class="card-tools">
        <ul class="pagination pagination-sm float-right">
          <p class="pull-right">
            {{ $rows->render("pagination::bootstrap-4") }}
          </p>
        </ul>
      </div>
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
<script type="text/javascript">

  <?php 
    foreach($rows as $row){
  ?>
         $('#salseOrderpdf{{ $row->id }}').click(function(e) {
            e.preventDefault();
            var remark = $(this).closest("tr").find("#remark").val();
            var id = $(this).closest("tr").find("#id").val();
            console.log(id);
            window.open("{{ url('/') }}/sales-order-pdf/"+ id + "?remark="+remark, '_blank'); 
            // window.location.href = "{{ url('/') }}/sales-order-pdf/"+ id + '?remark='+remark;

          });
  <?php  } ?>

</script>

@stop
