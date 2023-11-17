@extends('layouts.app')
@section('content')

<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="ACC@1" class="form-control" required>
  <div class="row">
    <div class="col-12">
    <br/>
    </div>
  </div>

<div class="container">
 <div class="panel panel-success">
   <div class="row">
     <div class="card-header">
       <p1 class="card-title">
         <h4 class="card-title right">Companies Chart Of Accounts List</h4>
        </p1>
     </div>
   </div>


   <div class="row">
       <div class="col-md-12">
         <div class="card-body table-responsive p-0">
         @csrf
          <table id ="datatable" class="table table-striped table-data table-report">
               <thead class="thead-dark">
                 <tr>
                   <th class="text-center" scope="col">Code</th>
                   <th class="text-center" scope="col">Name</th>
                   <th class="text-center" colspan="2">&nbsp;</th>
                 </tr>
               </thead>
               <tbody>
                 @foreach($data as $d)
                 <tr>
                   <td>{{ $d->company_id }}</td>
                   <td>{{ $d->name }}</td>
                   <td><a href="{{ route('acchead.tree.view', $d->company_id ) }}" target="_blank">View</a></td>
                 </tr>
                 @endforeach
               </tbody>
          </table>
        </div>
      </div>
  </div>

</div>
</section>
@stop

@section('pagescript')

@stop
