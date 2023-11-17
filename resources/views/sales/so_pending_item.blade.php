@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
@stop
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="MM@1" class="form-control" required>
<div class="title"></div>
  <legend>
<div class="title">
  <div class="widget-header widget-header-small">
    <font size="3" color="blue"><b>Delivery Pending Items</b></font>
    <div class="widget-toolbar">
        <a href="{{route('sales.delivery.index')}}" class="blue"><i class="fa fa-list"></i> Delivery List</a>
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
  
<br/>
  <div class="row">
    <div class="col-md-12">
      @csrf
      <table class="table table-striped table-view">
        <thead class="thead-blue">
            <th class="text-center" scope="col">#</th>
          <th class="text-center" scope="col">Items</th>
          <th class="text-center" scope="col">Quantity</th>
          <th class="text-center" scope="col">Customer</th>
          <th class="text-center" scope="col">Expected Delivery Date</th>
          <th class="text-center" scope="col">FPO No</th>
          <th class="text-center" scope="col">Order No</th>
          
        </thead>
        <tbody>
          @foreach($pendingItems as $key => $row)
          <tr>
            <td class="text-center">{{ $key + 1 }}</td>
            <td class="text-center">{{ $row->item_name }}</td>
            <td class="text-center">{{ $row->so_order_bal_qty }}</td>
            <td class="text-center">{{ $row->cust_name }}</td>
            <td>{{ date('d-m-Y',strtotime($row->so_req_del_date)) }}</td>
            <td>000{{ $row->so_fpo_no }}</td>
            <td class="text-center">{{ $row->so_order_no }}</td>
          </tr>
          @endforeach
          </tbody>
        </table>
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
    $('.table').dataTable();

  });
</script>

@stop
