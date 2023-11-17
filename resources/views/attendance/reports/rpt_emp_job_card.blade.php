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
        <font size="3" color="blue"><b>Job Card Report</b></font>
      </h6>
    </div>
  </div>
  <div class="container">
    <form id='myform' action="{{route('get.job.card.report')}}" id="acc_form" method="post">
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
              <select name="employee_id" class="col-xs-6 col-sm-4 chosen-select" id="employee_id">
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
          &nbsp;<button type="submit" name="submit" id='btn2' value='pdf' data-target="_blank" class="btn btn-sm btn-info"><span class="fa fa-search">PDF</span></button>
        </div>

      </div>
      </form>

      <div class="row justify-content-center">
        <div class="col-md-12">
          <table class="table table-striped table-data table-report"> 
            <tbody>
              <?php $SL=1; $i=0; $department = ''; $section = ''; $emp_id_no = '';
              $total_present=0; $total_leave=0; $total_absent=0;?>
              @foreach($rows as $row)  
                <thead class="thead-light">
                <?php if($section == '' || $section != $row->section) {  
                  $i = 1;?> 
                  <tr> 
                    <th class="text-left" colspan="4">Department : {{ $row->department }}</th> 
                    <th class="text-right" colspan="8">Section : {{ $row->section }}</th>
                  </tr>  
                <tr>
                  <th class="text-center">SL</th>
                  <th class="text-center">Employee Id</th>
                  <th class="text-center">Employee Name</th>
                  <th class="text-center">Designation</th>
                  <th class="text-center">Joining Date</th>
                  <th class="text-center">Date</th>
                  <th class="text-center">In Time</th>
                  <th class="text-center">Out Time</th>
                  <th class="text-center">Late</th> 
                  <th class="text-center">Early</th>
                  <th class="text-center">Over Time</th> 
                  <th class="text-center">Status</th> 
                </tr>
                <?php } ?>
              </thead>

              <?php if($emp_id_no != '' && $emp_id_no != $row->emp_id_no) { ?>
                <tr>
                    <td colspan="12" bgcolor="white"><b>Number Of Days:</b> {{$row->no_days}}  
                    &nbsp;&nbsp;&nbsp;<b>Total Present:</b> {{$total_present}}
                    &nbsp;&nbsp;&nbsp;<b>Total Absent:</b> {{$total_absent}}
                    &nbsp;&nbsp;&nbsp;<b>Total Leave:</b> {{$total_leave}}</td> 
                </tr>
              <?php 
                $total_present=0; $total_absent =0; $total_leave =0;
                } ?>
              
               <?php 
                  if($row->vEmpStatus != 'Absent'){
                    $total_present +=1;
                  }
                  if($row->vEmpStatus == 'Absent'){
                    $total_absent +=1;
                  }
                  if($row->vEmpStatus == 'Leave'){
                    $total_leave +=1;
                  }
               ?>
                
              <?php if($emp_id_no == '' || $emp_id_no != $row->emp_id_no) { ?> 
              <tr>
                <td>{{ $SL++ }}</td>
                <td align="center">{{ $row->emp_id_no }}</td>
                <td>{{ $row->emp_name }}</td>
                <td>{{ $row->designation }}</td> 
                <td align="center">{{ date('d-m-Y',strtotime($row->emp_joining_dt)) }}</td> 
               
              <?php } else { ?>
                <tr>
                  <td>&nbsp;</td>
                  <td align="center">&nbsp;</td>
                  <td>&nbsp;</td>
                  <td align="center">&nbsp;</td> 
                  <td align="center">&nbsp;</td> 
               
              <?php } ?>
                 
                <td align="center">{{ date('d-m-Y',strtotime($row->attDate)) }}</td> 
                <td align="center">{{ $row->tInTime }}</td> 
                <td align="center">{{ $row->tOutTime }}</td> 
                <td align="center">{{ $row->tLate }}</td> 
                <td align="center">{{ $row->tEleave }}</td> 
                <td align="center">{{$row->tOT }}</td>  
                <td align="center">{{ $row->vEmpStatus }}</td> 
              </tr> 
               
              <?php $i++; $department = $row->department; $section = $row->section; 
               $emp_id_no = $row->emp_id_no;?>
              @endforeach 
              </tbody>
            </table>
            <table class="table table-bordered table-report">
              <tr>
                <td colspan="12" bgcolor="white"><b>Number Of Days:</b> {{$row->no_days}}  
                &nbsp;&nbsp;&nbsp;<b>Total Present:</b> {{$total_present}}
                &nbsp;&nbsp;&nbsp;<b>Total Absent:</b> {{$total_absent}}
                &nbsp;&nbsp;&nbsp;<b>Total Leave:</b> {{$total_leave}}</td> 
              </tr>
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
