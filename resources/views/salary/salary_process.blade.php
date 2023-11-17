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
    <form method="post" action="{{ route('salary.process.post') }}">
      @csrf
      <div class="form-group">
        <label for="exampleInputEmail1">Ttile</label>
        <input type="text" name="title" class="form-control" value="{{ old('name') }}" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Ex: December 1st week salary">
      </div>

      <div class="row">
        <div class="col-md-3">
          <div class="form-group">
            <label for="exampleInputPassword1">From Date</label>
            <input type="date" name="fromDate" class="form-control" value="{{ old('fromDate') }}">
          </div>

        </div>

        <div class="col-md-3">

          <div class="form-group">
            <label for="exampleInputPassword1">To Date</label>
            <input type="date" name="toDate" class="form-control" name="{{ old('toDate') }}">
          </div>

        </div>

        <div class="col-md-3">
          <div class="form-group">
            <label for="exampleInputEmail1">Active</label>
            <select name="active" class="form-control" id="exampleFormControlSelect1">
              <option value="1">YES</option>
              <option value="0">NO</option>
            </select>
          </div>
        </div>

        <div class="col-md-3">
          <div class="form-group">
            <label for="exampleInputEmail1">Employee Type</label>
            <select name="employeeType" class="form-control" id="exampleFormControlSelect1">
              <option value="worker">Worker</option>
              <option value="officer">Officer</option>
            </select>
          </div>
        </div>


      </div>

      <div class="row">
        <div class="col-md-3">
      
          <div class="form-group">
            <label for="exampleInputEmail1">Payment Date</label>
            <input type="date" name="paymentDate" class="form-control" name="{{ old('paymentDate') }}">

          </div>
          
        </div>
      </div>
 
      <button type="submit" class="btn btn-primary">Submit</button>
    </form>
    
  
</form>

<br><br>

<table class="table table-bordered">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Title</th>
      <th scope="col">From Date</th>
      <th scope="col">To Date</th>
      <th scope="col">Payment Date</th>
      <th scope="col">Active</th>
      <th scope="col">Action</th>
    </tr>
  </thead>
  <tbody>
    <?php $i = 1; ?>
    @foreach($WeekEndEntry as $row)
    <tr>
      <th scope="row">{{ $i }}</th>
      <td>{{ $row->title }}</td>
      <td>{{ $row->fromDate }}</td>
      <td>{{ $row->toDate }}</td>
       <td>{{ $row->paymentDate }}</td>
      <td><?php echo ($row->active == 1) ? 'Active' : 'No'; ?></td>
      <td>
        <a href="{{ URL::to('salary-process-update') }}/{{ $row->id }}"><span class="btn btn-success">Active</span></a>
        <a href="{{ URL::to('edit-salary-process') }}/{{ $row->id }}"><span class="btn btn-success">Edit</span></a>
      </td>
    </tr>

    <?php $i++; ?>
    @endforeach
  </tbody>
</table>

</section>



@stop
@section('pagescript')
  <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('assets/js/chosen.jquery.min.js') }}"></script>
  <script src="{{ asset('assets/js/ace-elements.min.js') }}"></script>
   
@stop
