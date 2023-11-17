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
    <div  style="background-color:#e0e0e0" class="widget-header widget-header-small">
        <h6 class="widget-title smaller">
        <font size="2" color="blue"><b>Employee Attendance Process</b></font>
        </h6>
        <div class="widget-toolbar">
            <a href="{{route('attendance.process')}}" class="blue"><i class="fa fa-list"></i> List</a>
        </div>
    </div>
</div>
@if(Session::has('message'))
<div class="row">
   <div class="col-md-12">
     <p class="alert alert-success"><b>{{ Session::get('message') }}</b></p>
   </div>
</div>
@endif
    <div class="widget-body">
      <div class="widget-main">  
       <form id="emp_Form" action="{{route('attendance.process1')}}" method="post">  
       {{ csrf_field() }}
        
        <div class="row">
          <div class="col-md-4">
            <div class="input-group ss-item-required">
                <div class="input-group-prepend ">
                    <div class="input-group-text" style="min-width:130px">Process Date:</div>
                </div>
                <input type="text" size = "15" name="process_date" onclick="displayDatePicker('process_date');"  value="{{ old('process_date') == "" ?  date('d-m-Y') :  date('d-m-Y',strtotime(old('process_date'))) }}"  required />
                <a href="javascript:void(0);" onclick="displayDatePicker('process_date');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
            </div> 
           </div> 
           <div class="col-md-4">
                <button class="btn btn-sm btn-success" type="submit" ><i class="fa fa-save"></i> Process</button>
           </div>
        </div>
           
    </div>
  </div>
</form>
</section>

@stop
@section('pagescript')
  <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('assets/js/chosen.jquery.min.js') }}"></script>
  <script src="{{ asset('assets/js/ace-elements.min.js') }}"></script>
   
@stop
