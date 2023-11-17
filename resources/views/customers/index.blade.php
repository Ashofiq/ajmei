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
    <font size="3" color="blue"><b>Customer Information</b></font>
    <div class="widget-toolbar">
        <a href="{{route('cust.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
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
  <form action="{{route('cust.search')}}" method="post">
  {{ csrf_field() }}
   <div class="row">
      <div class="col-md-5">
          <div class="input-group ss-item-required"> 
                  <select name="customer_id" class="col-xs-6 col-sm-4 chosen-select" id="customer_id" onchange="customer()" required>
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
      <div class="col-md-3">
        <button type="submit" name="submit"  class="btn btn-sm btn-info"><span class="fa fa-search"></span></button>
      </div>
  </div>
  </form>
<br/>
  <div class="row">
    <div class="col-md-12">
      @csrf
      <table class="table table-striped table-view">
        <thead class="thead-blue">
          <th style="display:none;" class="text-center" scope="col">Sys.ID</th>
          <th class="text-center" scope="col">Id</th>
          <th class="text-center" scope="col">Name</th>
          <th class="text-center" scope="col">Address1</th>
          <th class="text-center" scope="col">Address2</th>
          <th class="text-center" scope="col">Mobile</th>
          <!-- <th class="text-center" scope="col">District</th>
          <th class="text-center" scope="col">Sales Person</th>
          <th class="text-center" scope="col">Courrier To</th>
          <th class="text-center" scope="col">Customer Disc(%)</th>
          <th class="text-center" scope="col">SP Comm(%)</th>
          <th class="text-center" scope="col">VAT</th> -->
          <th class="text-center" colspan="2">Options</th>
        </thead>
        <tbody>
          @foreach($rows as $row)
          <tr>
            <td style=display:none;>{{ $row->id }}</td>
            <td width="5%">{{ $row->cust_slno }}</td>
            <td width="15%"><a href="{{route('cust.create')}}">{{ $row->cust_name }}</a></td>
            <td width="20%">{{ $row->cust_add1 }}</td>
            <td width="16%">{{ $row->cust_add2 }}</td>
            <td width="8%">{{ $row->cust_mobile }}</td>
            <!-- <td width="8%">{{ $row->vCityName }}</td>
            <td width="8%">{{ $row->sales_name }}</td>
            <td width="8%">{{ $row->courrier_to }}</td>
            <td width="3%">{{ $row->cust_own_comm }}</td>
            <td width="3%">{{ $row->cust_overall_comm }}</td>
            <td width="3%">{{ $row->cust_VAT }}</td> -->
            <td aling="right " width="18%">
              <a><a href="{{ route('cust.edit', $row->id) }}" class="btn btn-xs btn-primary edit">Edit</a>
              <!--<a><a href="{{ route('cust.delv.index',$row->id) }}" class="btn btn-xs btn-warning edit">Delivery</a>-->
              <!--<a><a href="{{ url('cust-price-index/'.$row->id.'/'.$row->cust_com_id) }}" class="btn btn-xs btn-info edit">Price</a>-->
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
