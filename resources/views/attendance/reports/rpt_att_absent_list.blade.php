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
        <font size="3" color="blue"><b>Absent Report</b></font>
      </h6>
    </div>
  </div>
  <div class="container">
    <form id='myform' action="{{route('get.attend.absent.data')}}" id="acc_form" method="post">
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
          &nbsp;<!-- button type="submit" name="submit" id='btn2' value='pdf' data-target="_blank" class="btn btn-sm btn-info"><span class="fa fa-search">PDF</span></button-->
        </div>

      </div>
      </form>

      <div class="row justify-content-center">
        <div class="col-md-12">
          <table class="table table-striped table-data table-report"> 
            <tbody>
              <?php $sl =1; $i=0; $department = ''; $section = '';?>
              @foreach($rows as $row)  
              <?php if($section != '' && $section != $row->section) {  ?>
                    <tr>
                      <td colspan="7">Section Wise Count : {{ $i-1 }}</td> 
                    </tr>
                  <?php } ?>
                <thead class="thead-light">
                <?php if($section == '' || $section != $row->section) {  
                  $i = 1;?> 
                  <tr> 
                    <th class="text-left" colspan="4">Department : {{ $row->department }}</th> 
                    <th class="text-right" colspan="3">Section : {{ $row->section }}</th>
                  </tr>  
                <tr>
                  <th class="text-center">SL</th>
                  <th class="text-center">Employee Id</th>
                  <th class="text-center">Employee Name</th>
                  <th class="text-center">Designation</th>
                  <th class="text-center">Date</th>  
                  <th class="text-center">Status</th>
                </tr>
                <?php } ?>
              </thead>

              <tr> 
                <td>{{ $sl++ }}</td>
                <td align="center">{{ $row->emp_id_no }}</td>
                <td>{{ $row->emp_name }}</td>
                <td align="center">{{ $row->designation }}</td>
                <td align="center">{{ date('d-m-Y',strtotime($row->attDate)) }}</td>  
                <td align="center">{{ $row->vEmpStatus }}</td>
              </tr> 
              
              
              <?php  $i++; $department = $row->department; $section = $row->section; ?>
              @endforeach
              <tr>
                      <td colspan="7">Section Wise Count : {{ $i-1 }}</td> 
                    </tr>
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
