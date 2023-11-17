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
    <font size="3" color="blue"><b>Supplier Information</b></font>
    <div class="widget-toolbar">
        <a href="{{route('suppliers.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
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
  <form action="{{route('suppliers.search')}}" method="post">
  {{ csrf_field() }}
   <div class="row">
       <div class="col-md-3">
         <a href="{{route('suppliers.create')}}" class="btn btn-success btn-sm">
           <i class="fa fa-plus"></i>Add New</a>
        </div>

      <div class="col-md-4">
          <div class="input-group ss-item-required">
              <div class="input-group ">
                  <select name="supplier_id" class="col-xs-6 col-sm-4 chosen-select" id="supplier_id" required>
                      <option value="" disabled selected>- Select Suppliers -</option>
                      @foreach($suppliers as $supplier)
                          <option {{ old('supplier_id') == $supplier->id ? 'selected' : '' }} value="{{ $supplier->id }}">{{ $supplier->supp_name }}</option>
                      @endforeach
                  </select>
                  @error('supplier_id')
                  <span class="text-danger">{{ $message }}</span>
                  @enderror
              </div>
         </div>
      </div>
      <div class="col-md-3">
        <button type="submit" name="submit"  class="btn btn-sm btn-info">Search</button>
      </div>
  </div>
  </form>
<br/>
  <div class="row">
    <div class="col-md-12">
      @csrf
      <table class="table table-striped table-view" id="datatable">
        <thead class="thead-blue">
          <th class="text-center" scope="col">ID</th>
          <th class="text-center" scope="col">Name</th>
          <th class="text-center" scope="col">Address1</th>
          <th class="text-center" scope="col">Address2</th>
          <th class="text-center" scope="col">District</th>
          <th class="text-center" scope="col">Mobile</th>
          <th class="text-center" scope="col">Phone</th>
          <th class="text-center" scope="col">Email</th>
          <th class="text-center">Options</th>
        </thead>
        <tbody>
          @foreach($rows as $row)
          <tr>
            <td width="10%">AJS-000{{$row->id}}</td>
            <td width="15%">{{ $row->supp_name }}</td>
            <td width="20%">{{ $row->supp_add1 }}</td>
            <td width="20%">{{ $row->supp_add2 }}</td>
            <td width="8%">{{ $row->vCityName }}</td>
            <td width="8%">{{ $row->supp_mobile }}</td>
            <td width="8%">{{ $row->supp_phone }}</td>
            <td width="8%">{{ $row->supp_email }}</td>
            <td width="15%">
              <a><a href="{{ route('suppliers.edit',$row->id) }}" class="btn btn-xs btn-primary edit">EDIT</a>
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
              
          </p>
        </ul>
      </div>
    </div>
</div>
</div>
</section>

<script>
      $(document).ready( function () {
        console.log('datatable')
        $('#datatable').DataTable();
    });
</script>

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
