@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
@stop
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="ACC@1" class="form-control" required>

  <div class="title">
    <legend>
    <div class="widget-header widget-header-small">
        <h6 class="widget-title smaller">
          <font size="3" color="blue"><b>Transaction Cross Check</b></font>
        </h6>
       <div class="widget-toolbar">
         <a href="#" class="blue"><i class="fa fa-list"></i> List</a>
        </div>
    </div></legend>
  </div>
<div class="container">

  <div class="row">
    <div class="col-12">
        @if(Session::has('message'))
           <p class="alert alert-success">{{ Session::get('message') }}</p>
        @endif
    </div>
  </div>
<br/>
<div class="row justify-content-center">
    <div class="col-md-12">
      <table class="table table-striped table-data table-view">
        <thead class="thead-dark">
          <tr>
            <th>Voucher No</th>
            <th>Invoice No</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Difference</th>
          </tr>
        </thead>
        <tbody>
          @foreach($rows as $row)
          <tr>
            @if($row->trans_type == 'SV')
              <td>{{ $row->trans_type }}-{{ $row->voucher_no }}</td>
            @else
              <td><a href="{{ url('/acctrans/acctrans-edit/'.$row->trans_type_no.'/'.$row->id) }}"  title="Edit">
                 {{ $row->trans_type }}-{{ $row->voucher_no }}</a></td>
            @endif
            <td>{{ $row->acc_invoice_no }}</td>
            <td>{{ $row->DR }}</td>
            <td>{{ $row->CR }}</td>
            <td>{{ $row->DIFF }}</td>
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

    });
  </script>

@stop
