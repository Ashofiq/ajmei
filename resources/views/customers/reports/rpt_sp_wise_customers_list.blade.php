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
         <font size="3" color="blue"><b>Sales Person Wise Customer List</b></font>
      </h6>
    </div>
  </div>
  <div class="container">
    <form id='myform' action="{{route('rpt.sp.wise.cust.list')}}" id="acc_form" method="post">
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
        <div class="col-md-3">
        <div class="input-group mb-2">
           <div class="input-group ss-item-required">
             <select name="salesperson_id" class="col-xs-10 col-sm-8 chosen-select" id="salesperson_id"  required>
             <option value="" disabled selected>--Select Person--</option>
                 @if ($salespersons->count())
                     @foreach($salespersons as $cmb)
                         <option {{ $salesperson_id == $cmb->id ? 'selected' : '' }} value="{{$cmb->id}}" >{{ $cmb->sales_name }}</option>
                     @endforeach
                 @endif
             </select>
               @error('salesperson_id')
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
            <th class="text-center" scope="col">Name</th>
            <th class="text-center" scope="col">Designation</th>
            <th class="text-center" scope="col">Cell No</th>
          </thead>
          <tbody>
            @if ($sales_person_data->count())
                @foreach($sales_person_data as $d)
                <tr>
                  <td align="center">{{ $d->sales_name }}</td>
                  <td align="center">{{ $d->vComboName }}</td>
                  <td align="center">{{ $d->sales_mobile }}</td>
                </tr>
              @endforeach
            @else
                <tr>
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
           <th class="text-center" scope="col">Code</th>
           <th class="text-center" scope="col">Name</th>
           <th class="text-center" scope="col">Address</th>
           <th class="text-center" scope="col">Phone</th>
         </thead>
         <tbody>
           @foreach($rows as $row)
           <tr>
            <td style=display:none;>{{ $row->id }}</td>
             <td align="center">{{ $row->cust_slno }}</td>
             <td align="left">{{ $row->cust_name }}</td>
             <td align="left">{{ $row->cust_add1 }} {{ $row->cust_add2 }}</td>
             <td align="left">{{ $row->cust_mobile }} {{ $row->cust_phone }}</td>
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
