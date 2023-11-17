@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
@stop
@section('content')
<section class="content">
  <input type="hidden" name="menu_selection" id="menu_selection" value="HRM@1" class="form-control" required>
  <div class="title">
    <div  style="background-color:#e0e0e0;" class="widget-header widget-header-small">
      <h6 class="widget-title smaller">
        <font size="3" color="blue"><b>Monthly Salary Report</b></font>
      </h6>
    </div>
  </div>
  <div class="container">
    <form id='myform' action="{{route('get.monthly.sal.report')}}" id="acc_form" method="post">
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
              <select class="col-xs-6 col-sm-4 chosen-select" name="month" required>
                <option value="" >--Select--</option>
                  @if ($months->count())
                      @foreach($months as $cmb)
                          <option {{ $month == $cmb->id ? 'selected' : '' }} value="{{ $cmb->id  }}" >{{ $cmb->vMonthName }}</option>
                      @endforeach
                  @endif
              </select>
           </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
              <select class="col-xs-6 col-sm-4 chosen-select" name="year" required>
                <option value="" >--Select--</option>
                  @if ($years->count())
                      @foreach($years as $cmb)
                          <option {{ $year == $cmb->iYear ? 'selected' : '' }} value="{{ $cmb->iYear  }}" >{{ $cmb->iYear }}</option>
                      @endforeach
                  @endif
              </select>
            </div>
        </div>
        <div class="col-md-2">
          <button type="submit" name="submit" id='btn1' value='html'  class="btn btn-sm btn-info"><span class="fa fa-search">Search</span></button>
          &nbsp;<button type="submit" name="submit" id='btn2' value='pdf' data-target="_blank" class="btn btn-sm btn-info"><span class="fa fa-search">PDF</span></button>
        </div>

      </div>
      </form>

      <div class="row justify-content-left">
        <div class="col-md-12">
        <font size="1.8">
          <table class="table table-striped table-data table-view"> 
            <tbody>
            <thead class="thead-light"> 
                <tr>
                  <th class="text-center">SL</th>
                  <th class="text-center">Name</th>
                  <th class="text-center">Designation</th>
                  <th class="text-center">D.O.J</th> 
                  <th class="text-center">ID NO</th>
                  <th class="text-center">Grade</th>
                  <th class="text-center">Dept</th>
                  <th class="text-center">Increment</th>
                  <th class="text-center">Leave(CL)</th>
                  <th class="text-center">Leave(SL)</th>
                  <th class="text-center">Leave(ML)</th>
                  <th class="text-center">Leave(EL)</th>
                  <th class="text-center">Total Absence</th> 
                  <th class="text-center">Gross Salary</th>
                  <th class="text-center">Work Day</th> 
                  <th class="text-center">Present Day</th>  

                  <th class="text-center">Basic</th>
                  <th class="text-center">H.Rent</th> 
                  <th class="text-center">Medi., Con. & Food</th>
                  <th class="text-center">Wages / Salary</th> 
                  <th class="text-center">Att. Bonus</th> 

                  <th class="text-center">OT Rate</th>
                  <th class="text-center">OT Hour</th> 
                  <th class="text-center">Total OT (Tk)</th>
                  <th class="text-center">Absence Deduction</th> 
                  <th class="text-center">Net Payment</th> 
                  <th class="text-center">Signature</th> 

                </tr> 
              </thead> 

              <?php $SL=1; $i=0; $department = ''; $section = ''; $emp_id_no = '';?>
              @foreach($rows as $row) 
                <?php 
                  $absent_deduction = ($row->dBasic/$row->iDays)*$row->iAbsentDays;
                  $wages_sal = $row->dGross - $absent_deduction
                ?>  
              <tr>
                <td>{{ $SL++ }}</td> 
                <td>{{ $row->emp_name }}</td>
                <td>{{ $row->designation }}</td> 
                <td align="center">{{ $row->emp_joining_dt ==''?'':date('d-m-Y',strtotime($row->emp_joining_dt)) }}</td> 
                <td align="center">{{ $row->emp_id_no }}</td>
 
                <td align="center">{{ $row->emp_skill_grade }}</td> 
                <td align="center">{{ $row->department }}</td> 
                <td align="center">&nbsp;</td> 
                <td align="center">{{ $row->dLeaveCL }}</td> 
                <td align="center">{{ $row->dLeaveSL }}</td> 
                <td align="center">{{ $row->dLeaveML }}</td>  
                <td align="center">{{ $row->dLeaveEL }}</td> 

                <td align="center">{{ $row->iAbsentDays }}</td> 
                <td align="right">{{ number_format($row->dGross,0) }}</td> 
                <td align="right">{{ $row->iDays }}</td>  
                <td align="right">{{ number_format($row->iDays - $row->iAbsentDays,0) }}</td> 
                
                <td align="center">{{ number_format($row->dBasic,0) }}</td> 
                <td align="right">{{ number_format($row->dHouseRent,0) }}</td> 
                <td align="right">{{ number_format($row->dConveyance,0) }}</td>  
                <td align="right">{{ number_format($wages_sal,0) }}</td>
                <td align="right">{{ number_format($row->dBonus,0) }}</td> 

                <td align="right">{{ number_format($row->dOTRate,2) }}</td> 
                <td align="right">{{ $row->dOTHour =="00:00:00"?'': $row->dOTHour }}</td> 
                <td align="right">{{ number_format($row->dOTAmount,0) }}</td> 
                <td align="right">{{ number_format($absent_deduction,0) }}</td>
                <td align="right">{{ number_format($wages_sal + $row->dOTAmount,0) }}</td>
                <td align="right">&nbsp;</td>
               
              </tr> 
               
              <?php $i++; $department = $row->department; $section = $row->section; 
               $emp_id_no = $row->emp_id_no;?>
              @endforeach 
              </tbody>
            </table></font>
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
