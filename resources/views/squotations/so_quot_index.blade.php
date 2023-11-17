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
    <font size="3" color="blue"><b>Quotation Information</b></font>
    <div class="widget-toolbar">
        <a href="{{route('sales.quot.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
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
  <form action="{{route('sales.quot.search')}}" method="post">
  {{ csrf_field() }}
   <div class="row">
       <div class="col-md-2">
         <a href="{{route('sales.quot.create')}}" class="btn btn-success btn-sm">
           <i class="fa fa-plus"></i>Add New</a>
        </div>

      <div class="col-md-4">
          <div class="input-group ss-item-required"> 
                <select name="customer_id" class="col-xs-10 col-sm-8 chosen-select" id="customer_id" onchange="customer()" required>
                      <option value="" disabled selected>- Select Customer Name -</option>
                      @foreach($customers as $customer)
                          <option {{ old('customer_id') == $customer->id ? 'selected' : '' }} value="{{ $customer->id }}">{{ $customer->cust_name }}</option>
                      @endforeach
                </select>
                @error('customer_id')
                <span class="text-danger">{{ $message }}</span>
                @enderror 
         </div>
      </div>
      <div class="col-md-1">
        <button type="submit" name="submit"  class="btn btn-sm btn-info"><span class="fa fa-search">Search</span></button>
      </div>
  </div>
  </form>
<br/>
  <div class="row">
    <div class="col-md-12">
      @csrf
      <table width="100%" class="table table-striped table-view">
        <thead class="thead-blue">
          <th style="display:none;" class="text-center" scope="col">Sys.ID</th>
          <th class="text-center" scope="col">Quotation</th>
          <th class="text-center" scope="col">Quotation Date</th>
          <th class="text-center" scope="col">Name</th>
          <th class="text-center" scope="col">Address1</th>
          <th class="text-center" scope="col">Subject</th>
          <th class="text-center" colspan="2">Options</th>
        </thead>
        <tbody>
          @foreach($rows as $row)
          <tr>
            <td style=display:none;>{{ $row->id }}</td>
            <td>{{ $row->quot_ref_no }}</td>
            <td>{{ $row->quot_date }}</td>
            <td>{{ $row->quot_cust_name }}</td>
            <td>{{ $row->quot_cust_add }}</td>
            <td>{{ $row->quot_subj }}</td>
            <td>
                <form  method="post" action="{{ url('/sales-quot-del/destroy/'.$row->id) }}" class="delete_form">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <div class="btn-group btn-corner">
                  <a><a href="{{ url('sales-quot-edit/e/'.$row->id) }}" class="btn btn-xs btn-primary edit">Edit</a>
                  <button class='btn btn-danger btn-sm delete'  type="submit" onclick="return confirm('Are You Sure? Want to Delete It.');">Delete</button>
                  <a><a href="{{ url('sales-quot-edit/c/'.$row->id) }}" class="btn btn-xs btn-warning edit">Copy</a>
                  <a><a href="{{ url('sales-quot-print/'.$row->id) }}" class="btn btn-xs btn-info edit" target="_blank">Print</a>
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
