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
        <font size="2" color="blue"><b>Employee Salary Process</b></font>
        </h6>
        <div class="widget-toolbar">
            <a href="{{route('salary.process')}}" class="blue"><i class="fa fa-list"></i> List</a>
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
   

  <div class="container">
    <form method="post" action="{{ route('salary.processupdate', $WeekEndEntry->id) }}">
      @csrf
      <div class="form-group">
        <label for="exampleInputEmail1">Ttile</label>
        <input type="text" name="title" class="form-control" value="{{ $WeekEndEntry->title }}" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Ex: December 1st week salary">
      </div>
      <div class="form-group">
        <label for="exampleInputPassword1">From Date</label>
        <input type="date" name="fromDate" class="form-control" value="{{ $WeekEndEntry->fromDate }}">
      </div>

      <div class="form-group">
        <label for="exampleInputPassword1">To Date</label>
        <input type="date" name="toDate" class="form-control" value="{{ $WeekEndEntry->toDate }}">
      </div>

      <div class="form-group">
        <label for="exampleInputEmail1">Employee Type</label>
        <select name="employeeType" class="form-control" id="exampleFormControlSelect1">
          <option value="{{ $WeekEndEntry->employeeType }}">{{ $WeekEndEntry->employeeType }}</option>
          <option value="worker">Worker</option>
          <option value="officer">Officer</option>
        </select>
      </div>
      
      <div class="form-group">
        <label for="exampleInputEmail1">Payment Date</label>
        <input type="date" name="paymentDate" class="form-control" value="{{ $WeekEndEntry->paymentDate }}">

      </div>
 
      <button type="submit" class="btn btn-primary">Update</button>
    </form>
    
  
</form>

<br><br>

</section>



@stop
@section('pagescript')
  <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('assets/js/chosen.jquery.min.js') }}"></script>
  <script src="{{ asset('assets/js/ace-elements.min.js') }}"></script>
   
@stop
