@extends('layouts.app')
@section('css')
      <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
@stop
@section('content')
<!-- Start Add Modal -->
  @include('inc.showAccModal')
<!-- End Add Modal -->

<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="ACC@1" class="form-control" required>
  <div class="title">
    <?php $title_color = "#e0e0e0"?>
    @if($trans_type  == 1)    <?php $title_color = "#e0e0e0"?>  <?php // Journal ?>
    @elseif($trans_type == 2) <?php $title_color = "#ece0cf"?> <?php // Contra ?>
    @elseif($trans_type == 3) <?php $title_color = "#FFFFE0"?> <?php // Cash Recived ?>
    @elseif($trans_type == 4) <?php $title_color = "#FFFF99"?> <?php // Bank Recived ?>
    @elseif($trans_type == 5) <?php $title_color = "#FFFFE0"?> <?php // Cash Payment ?>
    @elseif($trans_type == 6) <?php $title_color = "#FFFF99"?> <?php // Bank Payment ?>
    @endif
    <div  style="background-color:$title_color;" class="widget-header widget-header-small">

      <h6 class="widget-title smaller">
        <font size="3" color="blue"><b>{{$title}}</b></font>
      </h6>
      <div class="widget-toolbar">
        @if($trans_type  == 1)
          <a href="{{route('acctrans.jv.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
        @elseif($trans_type == 2)
          <a href="{{route('acctrans.con.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
        @elseif($trans_type == 3)
          <a href="{{route('acctrans.cr.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
        @elseif($trans_type == 4)
           <a href="{{route('acctrans.cp.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
        @elseif($trans_type == 5)
             <a href="{{route('acctrans.br.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
        @elseif($trans_type == 6)
            <a href="{{route('acctrans.bp.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
        @endif
      </div>
    </div>
  </div>
<div class="container">
  @if($trans_type  == 1)
     <form action="{{route('acctrans.jv.search')}}" method="POST">
  @elseif($trans_type == 2)
     <form action="{{route('acctrans.con.search')}}" method="POST">
  @elseif($trans_type == 3)
     <form action="{{route('acctrans.cr.search')}}" method="POST">
  @elseif($trans_type == 4)
      <form action="{{route('acctrans.cp.search')}}" method="POST">
  @elseif($trans_type == 5)
       <form action="{{route('acctrans.br.search')}}" method="POST">
  @elseif($trans_type == 6)
       <form action="{{route('acctrans.bp.search')}}" method="POST">
  @endif

   {{ csrf_field() }}
  <div class="row justify-content-center">
     <div class="col-md-2">
       <div class="input-group mb-2"> 
         <select class="form-control m-bot15" name="company_code" required>
           <option value="" >--Select Company--</option>
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
          <input type="text" name="voucherNo" id="voucherNo" class="form-control" placeholder="Voucher No"/>
       </div>
    </div>
    <div class="col-md-3">
         <div class="input-group mb-2">
           <select class="form-control chosen-select" name="acc_id">
             <option value="" >--Select Account Head--</option>
              @foreach($acc_list as $acc)
                <option {{ $acc_id == $acc->id ? 'selected' : '' }} value="{{ $acc->id  }}" >{{ $acc->acc_head }}--{{ $acc->p_acc_head }}</option>
              @endforeach
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
    
    <!-- div class="col-md-2">
        <div class="form-group">
          <input type="date" name="fromdate" id="fromdate" class="form-control" placeholder="From Date" required/>
       </div>
    </div> 
    
    <div class="col-md-2">
        <div class="form-group">
          <input type="date" name="todate" id="todate" class="form-control" placeholder="To Date" required/>
       </div>
    </div -->
    
    <div class="col-md-1">
        <button type="submit" class="btn btn-sm btn-info"><span class="fa fa-search">Search</span></button>
    </div>

  </div>
  </form>


  <div class="row justify-content-center">
    <div class="col-md-12">
      <table class="table table-striped table-data table-view">
        <thead class="thead-dark">
          <tr>
            <th>Id</th>
            <th>Voucher Date</th>
            <th>Voucher No</th>
            <th>Narration</th>
            <th>A/C Name</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Options</th>
          </tr>
        </thead>
        <tbody>
          @foreach($vouchers as $row)
          <tr>
            <td width="3%">{{ $row->id }}</td>
            <td width="5%">{{ date('d-m-Y', strtotime($row->voucher_date)) }} </td>
            <td width="8%">
                @if($row->is_billtobill == 0)
                    <a href="{{ url('/acctrans/acctrans-edit/'.$trans_type.'/'.$row->id) }}"  title="Edit">
                    {{ $row->trans_type }}-{{ $row->voucher_no }}</a>
               </td>
                @else
                    <a href="{{ url('/billtobill-jv-edit/'.$trans_type.'/'.$row->id) }}"  title="Edit">
                    {{ $row->trans_type }}-{{ $row->voucher_no }}</a>
                    </td>
                @endif
             
            </td>
            <td width="20%" >{{ $row->t_narration }}</td>
            <td width="20%" >{{ $row->acc_head }}</td>
            <td width="10%">{{ number_format($row->d_amount,2) }}</td>
            <td width="10%">{{ number_format($row->c_amount,2) }}</td>
            <td width="10%">
              <form  method="post" action="{{ url('/acctrans/destroy/'.$row->id) }}" class="delete_form">
              {{ csrf_field() }}
              {{ method_field('DELETE') }}
              <div class="btn-group btn-corner">
                <span href="{{ url('/') }}/acctrans/jv-m-view/{{$row->id}}" data-toggle="modal" data-id="{{$row->id}}" class="btn btn-sm btn-success viewModal" title="View">
                    View
                </span>
                @if($row->trans_type == 'JV')
                <span>
                  <a target="_blank" href="{{ route('acctrans.jv.voucherPrint', [$row->id,'print' => 1]) }}" title="Print Voucher"
                    class="btn btn-xs btn-primary">Voucher
                  </a>
                </span>
                @elseif($row->trans_type == 'CON')
                <span>
                  <a target="_blank" href="{{ route('acctrans.jv.voucherPrint', [$row->id,'print' => 1]) }}" title="Print Voucher"
                    class="btn btn-xs btn-primary">Voucher
                  </a>
                </span>
                @elseif($row->trans_type == 'CR')
                <span>
                  <a target="_blank" href="{{ route('acctrans.cr.moneyRecive', [$row->id,'print' => 1]) }}" title="Print Voucher"
                    class="btn btn-xs btn-primary">Voucher
                  </a>
                </span>
                @elseif($row->trans_type == 'BR')
                <span>
                  <!-- <a target="_blank" href="{{ route('acctrans.jv.voucherPrint', [$row->id,'print' => 1]) }}" title="Print Voucher"
                    class="btn btn-xs btn-primary">Voucher
                  </a> -->
                  <a target="_blank" href="{{ route('acctrans.cr.moneyRecive', [$row->id,'print' => 1]) }}" title="Print Voucher"
                    class="btn btn-xs btn-primary">Voucher
                  </a>
                </span>
                @else

                <span>
                  <a target="_blank" href="{{ route('voucher.print', [$row->id,'print' => 1]) }}" title="Print Voucher"
                    class="btn btn-xs btn-primary">Voucher
                  </a>
                </span>

                @endif
                <button class='btn btn-danger btn-sm delete'  type="submit" onclick="return confirm('Are You Sure? Want to Delete It.');">Delete</button>
              </div>
              </form>
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
                  {{ $vouchers->render("pagination::bootstrap-4") }} 
                </p>
              </ul>
            </div>
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
