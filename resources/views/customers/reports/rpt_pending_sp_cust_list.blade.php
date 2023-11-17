@extends('layouts.app')
@section('css')

@stop
@section('content')
<!-- Start Add Modal -->
  @include('inc.showAccModal')
<!-- End Add Modal -->

<section class="content">
  <input type="hidden" name="menu_selection" id="menu_selection" value="SD@1" class="form-control" required>

  <div class="title">
    <div  style="background-color:#e0e0e0;" class="widget-header widget-header-small">

      <h6 class="widget-title smaller">
        <font size="3" color="blue"><b>Sales Person Assign Pending Customer List</b></font>
      </h6>
    </div>
  </div>
<div class="container">
<form id="myform" action="{{route('rpt.pending.sp.cust.list')}}" method="POST">
  {{ csrf_field() }}
  <div class="row justify-content-center">
    <div class="col-md-3">
       <div class="input-group mb-2">
         <div class="input-group-prepend">
           <span class="input-group-text">Company Code&nbsp;:</span>
         </div>
         <select class="form-control m-bot15" name="company_code" required>
           <option value="" >--Select--</option>
            @if ($companies->count())
                @foreach($companies as $company)
                    <option {{ $company_code == $company->comp_id ? 'selected' : '' }} value="{{ $company->comp_id  }}" >{{ $company->comp_id }}--{{ $company->name }}</option>
                @endforeach
            @endif
        </select>
       </div>
    </div>
    <div class="col-md-6">
      <input type="text" name="customer_name" id="customer_name" value="{{$customer_name}}" class="form-control" placeholder="Enter Sales Person Name"/>
    </div>

    <div class="col-md-2">
      <button type="submit" name="submit" value='html' id='btn1' class="btn btn-sm btn-info">Search</button>
    </div>

  </div>
  </form>


  <div class="row justify-content-center">
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
<script type="text/javascript">
  var form = document.getElementById('myform');
  document.getElementById('btn2').onclick = function() {
      form.target = '_blank';
      form.submit();
  }
</script>
@stop
