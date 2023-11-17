@extends('layouts.app')
@section('css')

@stop
@section('content')
<!-- Start Add Modal -->
  @include('inc.showAccModal')
<!-- End Add Modal -->

<section class="content">
  <div class="title">
    <div class="widget-header widget-header-small">
      <h6 class="widget-title smaller">
        <font size="2" color="blue"><b>Journal Voucher</b></font>
      </h6>
      <div class="widget-toolbar">
          <a href="{{route('acctrans.jv.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
      </div>
    </div>
  </div>
<div class="container">

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
       <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are You Sure? Want to Save It.');"
              title="Delete">
          <i class="fa fa-trash">Search</i>
       </button>
     </div>
  </div>
  </form>


  <div class="row justify-content-center">
    <div class="col-md-12">
      <table class="table table-striped table-data table-report">
        <thead class="thead-dark">
          <tr>
            <th>Id</th>
            <th>Company Name</th>
            <th>Narration</th>
            <th>Voucher Date</th>
            <th>Voucher No</th>
            <th>Amount</th>
            <th>Options</th>
          </tr>
        </thead>
        <tbody>
          @foreach($journals as $row)
          <tr>
            <td>{{ $row->id }}</td>
            <td>{{ $row->name }}</td>
            <td>{{ $row->t_narration }}</td>
            <td>{{ date('d-m-Y', strtotime($row->voucher_date)) }} </td>
            <td>{{ $row->trans_type }}-{{ $row->voucher_no }}</td>
            <td>{{ number_format($row->d_amount,2) }}</td>
            <td>
              <div class="btn-group btn-corner">
                  <span href="{{ url('/') }}/acctrans/jv-m-view/{{$row->id}}" data-toggle="modal" data-id="{{$row->id}}" class="btn btn-sm btn-success viewModal" title="View">
                      <i class="fa fa-pencil-square-o">View</i>
                  </span> &nbsp;&nbsp;
                  <span href="#"  data-toggle="modal" class="btn btn-sm btn-success" title="Print">
                      <i class="fa fa-pencil-square-o">Print</i>
                  </span>
              </div>
            </td>
          </tr>
          @endforeach

          </tbody>
        </table>
        </div>

        <div class="col-md-12">
          <div class="card-tools">
              <ul class="pagination pagination-sm float-right">
                <p class="pull-right">
                  {!! $journals->render() !!}
                </p>
              </ul>
            </div>
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
    alert(url);
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
