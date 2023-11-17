@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/blogic_css/acc_tb.css') }}" />
@stop
@section('content')
<section class="content">
  <input type="hidden" name="menu_selection" id="menu_selection" value="HRM@1" class="form-control" required>
  <div class="title">
    <div  style="background-color:#e0e0e0;" class="widget-header widget-header-small">
      <h6 class="widget-title smaller">
        <font size="3" color="blue"><b>Raw Attendance Data</b></font>
      </h6>
    </div>
  </div>
  <div class="container">
    <form id='myform' action="{{route('get.raw.attendance.data')}}" id="acc_form" method="post">
      {{ csrf_field() }}
      <div class="row justify-content-center"> 
        <!--<div class="col-md-2">-->
        <!--  <input type="text" name="employee_id" id="employee_id" value="{{$employee_id}}" class="form-control" placeholder="Enter Employee Id"/>-->
        <!--</div>-->
        <div class="col-md-4">
           <div class="input-group mb-2">
              <select name="employee_id" class="col-xs-6 col-sm-4 chosen-select" id="employee_id" required>
               <option value="" >--Select Employee--</option>
                @if ($employees->count())
                    @foreach($employees as $employee)
                        <option {{ $employee_id == $employee->emp_id_no ? 'selected' : '' }} value="{{ $employee->emp_id_no  }}" >{{ $employee->emp_id_no }}--{{ $employee->emp_name }}</option>
                    @endforeach
                @endif
            </select>
           </div>
        </div>
        
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
        <div class="col-md-2">
          <button type="submit" name="submit" id='btn1' value='html'  class="btn btn-sm btn-info"><span class="fa fa-search">Search</span></button> 
        </div>

      </div>
      </form>

      <div class="row justify-content-center">
        <div class="col-md-12">
          <table class="table table-striped table-data table-report"> 
            <tbody>
              <thead>
                <tr>
                  <th class="text-center">SL</th>
                  <th class="text-center">Employee Id</th>
                  <th class="text-center">Employee Name</th>
                  <th class="text-center">Designation</th>
                  <th class="text-center">Department</th>
                  <th class="text-center">Section</th>
                  <th class="text-center">Machine Id</th>
                  <th class="text-center">Date</th> 
                </tr> 
              </thead>

              <?php $i=1;?>
              @foreach($rows as $row)   
              <tr>
                <td>{{ $i++ }}</td>
                <td align="center">{{ $row->emp_id_no }}</td>
                <td>{{ $row->emp_name }}</td>
                <td align="center">{{ $row->designation }}</td>
                <td align="center">{{ $row->department }}</td>
                <td align="center">{{ $row->section }}</td>
                <td align="center">{{ $row->vFingerId }}</td>
                <td align="center">{{ $row->dtCheckInTime }}</td> 
                
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
  </script>
@stop
