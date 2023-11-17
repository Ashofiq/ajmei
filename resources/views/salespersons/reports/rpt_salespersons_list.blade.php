@extends('layouts.app')
@section('css')

@stop
@section('content')
<!-- Start Add Modal -->
  @include('inc.showAccModal')
<!-- End Add Modal -->

<section class="content">
  <input type="hidden" name="menu_selection" id="menu_selection" value="SD@1" class="form-control" required>

  <div class="title">
    <div  style="background-color:#e0e0e0;" class="widget-header widget-header-small">

      <h6 class="widget-title smaller">
        <font size="3" color="blue"><b>Sales Person List</b></font>
      </h6>
    </div>
  </div>
<div class="container">
<form action="{{route('rpt.salesperson.list')}}" method="POST">
  {{ csrf_field() }}
  <div class="row justify-content-center">
    <div class="col-md-3">
       <div class="input-group mb-2">
         <div class="input-group-prepend">
           <span class="input-group-text">Company Code&nbsp;:</span>
         </div>
         <select class="form-control m-bot15" name="company_code" required>
           <option value="" >--Select--</option>
            @if ($companies->count())
                @foreach($companies as $company)
                    <option {{ $company_code == $company->comp_id ? 'selected' : '' }} value="{{ $company->comp_id  }}" >{{ $company->comp_id }}--{{ $company->name }}</option>
                @endforeach
            @endif
        </select>
       </div>
    </div>
    <div class="col-md-6">
      <input type="text" name="sale_per_name" id="sale_per_name" value="{{$sale_per_name}}" class="form-control" placeholder="Enter Sales Person Name"/>
    </div>

    <div class="col-md-2">
        <button type="submit" name="submit" value='html'  class="btn btn-sm btn-info"><span class="fa fa-search">Search</span></button>
        &nbsp;<!-- button type="submit" name="submit" value='pdf' data-target="_blank" class="btn btn-sm btn-info"><span class="fa fa-search">PDF</span></button-->
    </div>
  </div>
  </form>


  <div class="row justify-content-center">
    <div class="col-md-12">
      <table class="table table-striped table-data table-view">
        <thead class="thead-blue">
          <th style="display:none;" class="text-center" scope="col">Sys.ID</th>
          <th class="text-center" scope="col">Name</th>
          <th class="text-center" scope="col">Designation</th>
          <th class="text-center" scope="col">Mobile</th>
          <th class="text-center" scope="col">Email</th>
        </thead>
        <tbody>
          @foreach($rows as $row)
          <tr>
            <td style=display:none;>{{ $row->id }}</td>
            <td>{{ $row->sales_name }}</td>
            <td>{{ $row->vComboName }}</td>
            <td>{{ $row->sales_mobile }}</td>
            <td>{{ $row->sales_email }}</td>
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
// show modal
$('.viewModal').click(function(event) {
    event.preventDefault();

    var url = $(this).attr('href');
    //alert(url);
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
