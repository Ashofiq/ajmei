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
    <font size="3" color="blue"><b>Loan Received List</b></font>
    <div class="widget-toolbar">
        <a href="{{route('sales.loan.issue')}}" class="blue"><i class="fa fa-list"></i> List</a>
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
  <form action="{{route('sales.loan.issue')}}" method="post">
  {{ csrf_field() }}
   <div class="row">
       <div class="col-md-3">
         <a href="{{route('sales.loan.issue.create')}}" class="btn btn-dark">
           <i class="mdi mdi-plus"></i>Add New</a>
        </div>
        <div class="col-md-2">
          <input type="text" name="order_no" id="order_no" value="{{old('order_no')}}" class="form-control" placeholder="Enter Order No"/>
        </div>
        <div class="col-md-4">
          <div class="input-group">
              <div class="input-group ">
                  <select name="customer_id" class="col-xs-6 col-sm-4 chosen-select" id="customer_id">
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
          <th class="text-center" scope="col">Order No</th>
          <th class="text-center" scope="col">Order Ref</th>
          <th class="text-center" scope="col">Customer</th> 
          <th class="text-center" scope="col">Total Qty</th>
          <th class="text-center" scope="col">Total Rec</th> 
          <th class="text-center" scope="col">Pending Qty</th>
          <th class="text-center" colspan="2">Options</th>
        </thead>
        <tbody>
          @foreach($rows as $row)
          <tr>
            <td style=display:none;>{{ $row->id }}</td>
            <td>{{ date('d-m-Y',strtotime($row->loan_i_order_date)) }}</td>
            <td>{{ $row->loan_i_order_no }}</td>
            <td>{{ $row->loan_i_reference }}</td>
            <td>{{ $row->cust_name }}</td>
           
            <td align="center">{{ $row->loan_i_total_qty }}</td>
            <td align="center">{{ $row->loan_i_total_bal_qty }}</td>
            <td align="center">{{ number_format($row->loan_i_total_qty - $row->loan_i_total_bal_qty,2) }}</td>
            
            <td>
              <form  method="post" action="{{ url('/sales-loan-issue/destroy/'.$row->id.'/'.$row->loan_i_done) }}" class="delete_form">
              {{ csrf_field() }}
              {{ method_field('DELETE') }}
              <div class="btn-group btn-corner">
               
              @if( $row->loan_i_done == 0 )
                <a href="{{ route('sales.loan.issue.edit',$row->id) }}" class="btn btn-xs">Edit</a> 
                <button class='btn btn-danger btn-sm delete'  type="submit" onclick="return confirm('Are You Sure? Want to Delete It.');">Delete</button> 
                <a href="{{ route('sales.loan.return.create',$row->id) }}" class="btn btn-xs">Return</a>
              @else
                <a href="{{ route('sales.loan.return.create',$row->id) }}" class="btn btn-xs">Return</a>
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


  });
</script>

@stop
