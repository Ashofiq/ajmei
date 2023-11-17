@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/blogic_css/acc_tb.css') }}" />
@stop
@section('content')
<section class="content">
  <input type="hidden" name="menu_selection" id="menu_selection" value="SD@1" class="form-control" required>
  <div class="title">
    <div  style="background-color:#e0e0e0;" class="widget-header widget-header-small">
      <h6 class="widget-title smaller">
         <font size="3" color="blue"><b>Customer Wise Price List</b></font>
      </h6>
    </div>
  </div>
  <div class="container">
    <form id='myform' action="{{route('rpt.cust.wise.price.list')}}" id="acc_form" method="post">
      {{ csrf_field() }}
      <div class="row justify-content-center">
        <div class="col-md-2">
           <div class="input-group mb-2">
             <select class="form-control m-bot15" id="company_code" name="company_code" required>
               <option value="" >--Select Company--</option>
                @if ($companies->count())
                    @foreach($companies as $company)
                        <option {{ $company_code == $company->comp_id ? 'selected' : '' }} value="{{ $company->comp_id  }}" >{{ $company->comp_id }}--{{ $company->name }}</option>
                    @endforeach
                @endif
            </select>
           </div>
        </div>
        <div class="col-md-4">
        <div class="input-group mb-2">
           <div class="input-group ss-item-required">
             <select name="customer_id" class="col-xs-10 col-sm-8 chosen-select" id="customer_id"  required>
             <option value="" disabled selected>--Select Customer--</option>
                 @if ($customers->count())
                     @foreach($customers as $cmb)
                         <option {{ $customer_id == $cmb->id ? 'selected' : '' }} value="{{$cmb->id}}" >{{ $cmb->cust_name }}</option>
                     @endforeach
                 @endif
             </select>
               @error('customer_id')
               <span class="text-danger">{{ $message }}</span>
               @enderror
           </div>
         </div>
       </div>


        <div class="col-md-1.5">
          &nbsp;<button type="submit" name="submit" id='btn1' value='html'  class="btn btn-sm btn-info"><span class="fa fa-search">Search</span></button>
          &nbsp;<button type="submit" name="submit" id='btn2' value='pdf' data-target="_blank" class="btn btn-sm btn-info"><span class="fa fa-search">PDF</span></button>
        </div>

      </div>
      </form>

      <div class="row justify-content-center">
        <div class="col-md-12">
          <table class="table table-striped table-data table-view">
            <thead class="thead-blue">
            <th class="text-center" scope="col">Customer Id</th>
            <th class="text-center" scope="col">Name</th>
            <th class="text-center" scope="col">Address</th>
            <th class="text-center" scope="col">District</th>
          </thead>
          <tbody>
            @if ($customer_data->count())
                @foreach($customer_data as $d)
                <tr>
                  <td align="center">{{ $d->cust_name }}</td>
                  <td align="center">{{ $d->cust_code }}</td>
                  <td align="center">{{ $d->cust_add1 }} {{ $d->cust_add2 }}</td>
                  <td align="center">{{ $d->vCityName }}</td>
                </tr>
              @endforeach
            @else
                <tr>
                  <td align="center">&nbsp;</td>
                  <td align="center">&nbsp;</td>
                  <td align="center">&nbsp;</td>
                  <td align="center">&nbsp;</td>
                </tr>
            @endif
            </table>
            </div>
        <div class="col-md-12">
        <table class="table table-striped table-data table-view">
         <thead class="thead-blue">
           <th style=display:none; class="text-center" scope="col">Sys.ID</th>
           <th class="text-center" scope="col">Item Code</th>
           <th class="text-center" scope="col">Item Name</th>
           <th class="text-center" scope="col">Price</th>
           <th class="text-center" scope="col">Valid</th>
           <th class="text-center" scope="col">To</th>
         </thead>
         <tbody>
           @foreach($rows as $row)
           <tr>
            <td style=display:none;>{{ $row->id }}</td>
            <td>{{ $row->item_code }}</td>
            <td>{{ $row->item_name }} ({{ $row->itm_cat_name }})</td>
            <td align="right">{{ $row->cust_price }}</td>
            <td align="center">{{date('d-m-Y',strtotime($row->p_valid_from))}}</td>
            <td align="center">{{date('d-m-Y',strtotime($row->p_valid_to))}}</td>
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
  <script>
    var form = document.getElementById('myform');
    document.getElementById('btn2').onclick = function() {
      form.target = '_blank';
      form.submit();
  }
  document.getElementById('btn1').onclick = function() {
    form.target = '_self';
    form.submit();
  }
  </script>
@stop
