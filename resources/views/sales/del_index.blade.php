@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
@stop
@section('content')
<!-- Start Add Modal -->
  @include('inc.showSalesModal')
<!-- End Add Modal -->
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="MM@1" class="form-control" required>
<div class="title"></div>
  <legend>
<div class="title">
  <div class="widget-header widget-header-small">
    <font size="3" color="blue"><b>Delivered List</b></font>
    <div class="widget-toolbar">
        <a href="{{route('sales.delivery.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
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
  <form action="{{route('sales.delivery.index')}}" method="post">
  {{ csrf_field() }}
   <div class="row">
       <div class="col-md-3">
         <a href="{{route('sales.delivery.create', -1)}}" class="btn btn-dark">
           <i class="mdi mdi-plus"></i>Add New</a>
        </div>
        <div class="col-md-2">
          <input type="text" name="delivery_no" id="delivery_no" value="{{old('delivery_no')}}" class="form-control" placeholder="Enter Delivery No"/>
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
          <th style="display:none;" class="text-center" scope="col">Sys.ID</th>
          <th class="text-center" scope="col">Date</th>
          <th class="text-center" scope="col">Delivery No</th>
          <th class="text-center" scope="col">Order Ref</th>
          <th class="text-center" scope="col">Sales Order No</th>
          <th class="text-center" scope="col">Customer</th>
          <!-- <th class="text-center" scope="col">Delivered To</th>
          <th class="text-center" scope="col">Delivered Address</th>
          <th class="text-center" scope="col">Delivered Phone</th> -->
          <th class="text-center" scope="col">Total Amount</th>
          <th class="text-center" scope="col">Total Disc</th>
          <th class="text-center" scope="col">Carring Cost</th>
          <th class="text-center" scope="col">Labour Cost</th>
          <th class="text-center" scope="col">Load/Unload Cost</th>
          <th class="text-center" scope="col">Service Charge</th>
          <th class="text-center" scope="col">Other Cost</th> 
          <th class="text-center" scope="col">Net Amount</th>
          <th class="text-center" colspan="2">Options</th>
        </thead>
        <tbody>
          @foreach($rows as $row)
          <tr>
            <td style=display:none;>{{ $row->id }}</td>
            <td>{{ $row->del_date }}</td>
            <td>{{ $row->del_no }}</td>
            <td>{{ $row->del_po_no }}</td>
            <td>{{ $row->so_order_no }}</td> 
            <td>{{ $row->cust_name }}</td>
            <!-- <td>{{ $row->del_customer }}</td>
            <td>{{ $row->del_add }}</td>
            <td>{{ $row->del_cont_no }}</td> -->
            <td>{{ $row->del_sub_total }}</td>
            <td>{{ $row->del_total_disc }}</td>
            <td>{{ $row->del_carring_cost }}</td>
            <td>{{ $row->del_labour_cost }}</td>
            <td>{{ $row->del_load_unload_cost }}</td>
            <td>{{ $row->del_service_charge }}</td>
            <td>{{ $row->del_other_cost }}</td>
            <td>{{ $row->del_net_amt }}</td>
            <td>
              <form  method="post" action="{{ url('/itm/op/destroy/'.$row->item_op_stock.'/'.$row->item_ref_id.'/'.$row->id) }}" class="delete_form">
              {{ csrf_field() }}
              {{ method_field('DELETE') }}
              <div class="btn-group btn-corner">
              @if( $row->del_is_invoiced == 0 )
                <a href="{{ route('sales.delivery.edit',$row->id) }}" class="btn btn-sm btn-success"><i class="fa fa-pencil-square-o"></i>Edit</a>
                <a href="{{route('sales.delivery.invoice',['delid'=>$row->id, 'finyearid'=> $row->del_fin_year_id ])}}" class="btn btn-sm btn-success">Invoice&nbsp;Pending</a>
                <a href="{{ route('delivery.challan.pdf',$row->id) }}" class="btn btn-xs btn-primary" title="Delivery Challan" target="_blank">Challan</a>
                <!-- button class='btn btn-danger btn-sm delete'  type="submit" onclick="return confirm('Are You Sure? Want to Delete It.');"><i class="fa fa-trash"></i></button -->
              @else
                <span href="{{ url('/') }}/delivery/item-m-view/{{$row->id}}" data-toggle="modal" data-id="{{$row->id}}"
                    class="btn btn-sm btn-info viewModal" title="View Details" data-placement="top" >
                   View
                </span>
                <a href="{{ route('delivery.gatepass.pdf',$row->id) }}" class="btn btn-xs btn-warning" title="Gate Pass" target="_blank">Gate Pass</a>
                <a href="{{ route('delivery.challan.pdf',$row->id) }}" class="btn btn-xs btn-primary" title="Delivery Challan" target="_blank">Challan</a>
                <a href="{{ route('delivery.invoice.pdf',$row->id) }}" class="btn btn-xs btn-info" title="Invoice" target="_blank">Sales Bill</a>
                <a href="{{ route('sales.delivery.edit',$row->id) }}" class="btn btn-sm btn-success">Edit</a>
              @endif
              </div>
              </form>
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
  $(document).ready(function() {

    // show modal
    $('.viewModal').click(function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        //alert(url);
        $('#salesModal').modal('show');
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'html',
        })
        .done(function(response) {
            $("#salesModal").find('.modal-body').html(response);
        });
      });

  });
</script>

@stop
