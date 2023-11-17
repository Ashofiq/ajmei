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
    <font size="3" color="blue"><b>Customer Price Information</b></font>
    <div class="widget-toolbar">
        <a href="{{ url('/cust-price-index/'.$id.'/'.$comp) }}" class="blue"><i class="fa fa-list"></i>&nbsp;List</a>
    </div>
    <div class="widget-toolbar">
        <a href="{{ route('cust.index') }}" class="blue"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>
    </div>
  </div>
</div></legend>

<div class="container">

  <div class="row">
    <div class="col-12">
        @if(Session::has('message'))
           <p class="alert alert-success">{{ Session::get('message') }}</p>
        @endif
    </div>
  </div>

  <form action="{{route('cust.price.store1')}}" method="POST">
  {{ csrf_field() }}
  <input type="hidden" name="cust_id" id="cust_id" value="{{$id}}" class="form-control" readonly required/>
  <div class="row justify-content-center">
    <div class="col-md-3">
      <div class="input-group mb-2">
        <div class="input-group-prepend">
          <span class="input-group-text">Company&nbsp;:</span>
        </div>
        <input type="hidden" name="comp_id" id="comp_id" value="{{$comp}}" class="form-control" readonly required/>
        <input type="text" name="company" id="company" value="{{$company_name}}" class="form-control" readonly required/>
      </div>
    </div>
    <div class="col-md-2">
      <div class="input-group mb-2">
        <div class="input-group-prepend">
          <span class="input-group-text">Customer Code&nbsp;:</span>
        </div>
        <input type="hidden" name="id" id="id" value="{{$id}}" class="form-control" readonly required/>
        <input type="text" name="cust_code" id="cust_code" value="{{$cust_code}}" class="form-control" readonly required/>
      </div>
    </div>
    <div class="col-md-6">
     <div class="input-group mb-2">
       <div class="input-group-prepend">
         <span class="input-group-text">Customer Name&nbsp;:</span>
       </div>
       <input type="text" name="cust_name" id="cust_name" value="{{$cust_name}}" class="form-control" readonly required/>
    </div>
    </div>
  </div>
  <div class="row justify-content-center">
     <div class="col-md-2.5">
       <div class="input-group mb-1">
         <div class="input-group-prepend">
           <span class="input-group-text">Valid&nbsp;:</span>
         </div>
         <input type="date" name="fromdate" id="fromdate" value="{{old('fromdate')}}" class="form-control" placeholder="From Date" required/>
       </div>
     </div>
     <div class="col-md-2.5">
       <div class="input-group mb-1">
         <div class="input-group-prepend">
           <span class="input-group-text">To&nbsp;:</span>
         </div>
         <input type="date" name="todate" id="todate" value="{{old('todate')}}" class="form-control" placeholder="To Date" required/>
       </div>
     </div>

      <div class="col-md-2">
        <button type="submit" class="btn btn-sm btn-info" onclick="return confirm('Are You Sure? Want to Save It.');"
               title="Save">Save
        </button>
        <a><a href="{{ url('cust-price-index/'.$id.'/'.$comp) }}" class="btn btn-xs btn-warning edit">Price</a>
      </div>
   </div>

  <div class="row justify-content-center">
    <div class="col-md-8">
      @csrf
      <table class="table table-striped table-view">
        <thead class="thead-blue">
          <th style="display:none;" class="text-center" scope="col">Sys.ID</th>
          <th class="text-center" scope="col">Item Code</th>
          <th class="text-center" scope="col">Item Name</th>
          <th class="text-center" scope="col">Packing</th>
          <th class="text-center" scope="col">Size</th>
          <th class="text-center" scope="col">Unit</th>
          <th class="text-center" scope="col">Price</th>
        </thead>
        <tbody>
          @foreach($item_list as $row)
          <tr>
            <td style=display:none;>{{ $row->id }}</td>
            <td style=display:none;><input type="text" data-type="ItemCodeId" name="ItemCodeId[]" id="ItemCodeId_1" value="{{$row->id}}" class="form-control item_id_class" autocomplete="off"></td>
            <td><input type="hidden" data-type="ItemCode" name="ItemCode[]" id="ItemCode_1" value="{{$row->item_code}}::{{ $row->item_name }}" class="form-control" autocomplete="off">
              {{ $row->item_code }}</td>
            <td>{{ $row->item_name }} ({{ $row->itm_cat_name }})</td>
             <td>{{ $row->packing_id }}</td>
              <td>{{ $row->size }}</td>
               <td>{{ $row->vUnitName }}</td>
            <td align="center"><input type="text" data-type="Price" name="Price[]" id="Price_1" onkeydown="enter(this.id,this.value)" class="form-control input-sm" style="font-weight:bold; text-align: center;" autocomplete="off"></td>
          </tr>
          @endforeach
          </tbody>
        </table>
      </div>
  </div>
 </form>

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

  function enter(id,qty) {
      if(event.keyCode == 13) {
          field = id.split("_")[0];
          i = id.split("_")[1];
          //alert(i);
          document.getElementById('Qty_'+i).focus();
      }
  }
</script>

@stop
