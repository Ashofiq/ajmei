@extends('layouts.app')
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="ACC@1" class="form-control" required>
<div class="title"><legend>Financial Year Declaration</legend></div>
<div class="container">

  <div class="row">
    <div class="col-12">
        @if(Session::has('message'))
           <p class="alert alert-success">{{ Session::get('message') }}</p>
        @endif
    </div>
    <br/>
  </div>

 <form action="{{route('finyeardec.store')}}" method="POST">
 {{ csrf_field() }}
  <div class="row justify-content-center">
     <div class="col-md-4">
       <div class="input-group mb-2">
         <div class="input-group-prepend">
           <span class="input-group-text">Company Code&nbsp;:</span>
         </div>
         <select class="form-control m-bot15" name="company_code" required>
           <option value="" >--Select--</option>
            @if ($companies->count())
                @foreach($companies as $company)
                    <option {{ $default_comp_code == $company->comp_id ? 'selected' : '' }} value="{{ $company->comp_id  }}" >{{ $company->comp_id }}--{{ $company->name }}</option>
                @endforeach
            @endif
        </select>
       </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
          <input type="date" name="fromdate" id="fromdate" class="form-control" placeholder="From Date" required/>
       </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
          <input type="date" name="todate" id="todate" class="form-control" placeholder="To Date" required/>
       </div>
    </div>
    <div class="col-md-1">
        <div class="form-group">
          <input type="checkbox" name="status" class="status la_checkbox" checked required>
       </div>
    </div>

     <div class="col-md-1">
       <button type="submit" class="btn btn-sm btn-info" onclick="return confirm('Are You Sure? Want to Save It.');"
              title="Save">Save
       </button>
     </div>
  </div>
  </form>


<div class="row justify-content-center">
    <div class="col-md-10">
      <table class="table table-striped table-data table-report">
        <thead class="thead-dark">
          <tr>
            <th>Id</th>
            <th>Company Name</th>
            <th>From Date</th>
            <th>To Date</th>
            <th>Status</th>
            <th>Options</th>
          </tr>
        </thead>
        <tbody>
          @foreach($finyeardecs as $row)
          <tr>
            <td>{{ $row->id }}</td>
            <td>{{ $row->name }}</td>
            <td>{{ date('d-m-Y', strtotime($row->date_from))}} </td>
            <td>{{ date('d-m-Y', strtotime($row->date_to))}} </td>
            <td class="text-center">{{ $row->status ==1?'Active':'In-Active' }}</td>
           <td>
                 <form  method="post" action="{{ url('/finyeardec/destroy/'.$row->id) }}" class="delete_form">
                 {{ csrf_field() }}
                 {{ method_field('DELETE') }}
                   <div class="btn-group btn-corner">
                 <button class='btn btn-danger btn-sm delete'  type="submit" onclick="return confirm('Are You Sure? Want to Delete It.');">Delete</button>

                 @if($row->status ==1)
                 &nbsp;
                    <!-- a href="{{ url('/finyeardec/finyeardec-status/0/'.$row->id.'/'.$row->comp_id) }}" class="btn btn-sm btn-success" title="Change">
                     In-Acitve
                    </a -->
                 @else
                 &nbsp;<a href="{{ url('/finyeardec/finyeardec-status/1/'.$row->id.'/'.$row->comp_id) }}" class="btn btn-sm btn-success" title="Change">
                     Do you want Acitve. Please click
                 </a>
                 @endif
                 <div
                 </form>
           </td>
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
<script type="text/javascript">
  $(document).ready(function() {
    $('.status.la_checkbox').bootstrapSwitch({
        size:'small',
        onText: 'ON',
        offText: 'OFF',
        onColor: 'primary',
        offColor: 'default',
        onSwitchChange: function(event, state) {
            $(event.target).closest('.bootstrap-switch').next().val(state ? 'on' : 'off').change();
        }
    });
});
</script>
@stop
