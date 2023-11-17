@extends('layouts.app')
@section('css')

    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
@stop
@section('content')
<!-- Start Add Modal -->
  @include('inc.showAccModal')
<!-- End Add Modal -->
<section class="content">
  <input type="hidden" name="menu_selection" id="menu_selection" value="MM@1" class="form-control" required>
  <div class="title">
    <legend>
    <div class="widget-header widget-header-small">
        <h6 class="widget-title smaller">
          <font size="3" color="blue"><b>Finish Goods Yearly Report</b></font>
        </h6>
       <div class="widget-toolbar">
         <!-- <a href="{{route('fin.goods.rec.index')}}" class="blue"><i class="fa fa-list"></i> List</a> -->
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
  <form action="{{route('yearlyReports')}}" method="post">
  {{ csrf_field() }}
   <div class="row">

        <div class="col-md-2">
            <div class="form-group">
                <input type="text" size = "15" name="fromdate" onclick="displayDatePicker('fromdate');"  value="{{ date('d-m-Y',strtotime($fromdate)) }}" />
                <a href="javascript:void(0);" onclick="displayDatePicker('fromdate');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <input type="text" size = "15" name="todate" onclick="displayDatePicker('todate');"  value="{{ date('d-m-Y',strtotime($todate)) }}" />
                <a href="javascript:void(0);" onclick="displayDatePicker('todate');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
            </div>
        </div>

        <div class="col-md-1">
            <button type="submit" name="submit"  class="btn btn-sm btn-info"><span class="fa fa-search">Search</span></button>
        </div>
 </div>
</form>
<br/>

<div class="row justify-content-center">
    <div class="col-md-12">
      <table class="table table-striped table-data table-view">
        <thead class="thead-dark">
          <tr style="background: black;color: white;">
            <td align="center">Montd</td> 
            <td align="center">Year</td>
            <td align="center">Total Production <br> (PCS)</td>
            <td align="center">Total Weight</td>
            <td align="center">Average</td>
            <!-- <td align="center">Average Weight</td> -->
            <td align="center">Remark</td>
          </tr>
        </thead>
        <tbody>
          @foreach($rows as $row)

          <tr>
            <td align="center"><?php $monthNum = $row->month;
                    $monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
                    echo $monthName; ?></td> 
            <td align="center">{{ $row->year }}</td>
            <td align="center">{{ $row->qty }}</td> 
            <td align="center">{{ $row->weight }}</td>
            <td align="center">{{ number_format( $row->weight / $row->qty , 3) }}</td>
            <td></td>
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
      // show modal
      $('.viewModal').click(function(event) {
          event.preventDefault();

          var url = $(this).attr('href');
        //  alert(url);
          $('#exampleModal').modal('show');
          $.ajax({
              url: url,
              type: 'GET',
              dataType: 'html',
          })
          .done(function(response) {
              $("#exampleModal").find('.modal-body').html(response);
          });
        });
    });
  </script>

@stop
