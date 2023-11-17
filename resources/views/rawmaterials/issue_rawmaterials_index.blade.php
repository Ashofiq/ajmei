@extends('layouts.app')
@section('css')

    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
@stop
@section('content')
<!-- Start Add Modal -->
  @include('inc.showAccModal')
<!-- End Add Modal -->
<section class="content">
  <input type="hidden" name="menu_selection" id="menu_selection" value="MM@1" class="form-control" required>
  <div class="title">
    <legend>
    <div class="widget-header widget-header-small">
        <h6 class="widget-title smaller">
          <font size="3" color="blue"><b>Issue to Production List</b></font>
        </h6>
       <div class="widget-toolbar">
         <a href="{{route('raw.itm.issue.prod.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
        </div>
    </div></legend>
  </div>
<div class="container">

  <div class="row">
    <div class="col-12">
        @if(Session::has('message'))
           <p class="alert alert-success">{{ Session::get('message') }}</p>
        @endif
    </div>
  </div>
  <form action="{{route('raw.itm.issue.prod.index')}}" method="post">
  {{ csrf_field() }}
   <div class="row">
       <div class="col-md-2">
         <a href="{{route('raw.itm.issue.create')}}" class="btn btn-success btn-sm">
                  <i class="fa fa-plus"></i>Add New</a>
      </div>
      <div class="col-md-4">
          <div class="input-group ss-item-required">
            <select name="item_id" id="item_id" class="chosen-select">
                <option value="" disabled selected>- Select Item -</option>
                @foreach($uniqueRawId as $item)
                    <option {{ $item_id == $item->id ? 'selected' : '' }} value="{{ $item->id }}">{{ $item->item_name }}</option>
                @endforeach
            </select>
            @error('supplier_id')
            <span class="text-danger">{{ $message }}</span>
            @enderror
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
     <div class="col-md-1">
       <button type="submit" name="submit"  class="btn btn-sm btn-info"><span class="fa fa-search">Search</span></button>
     </div>
 </div>
</form>
<br/>

<div class="row justify-content-center">
    <div class="col-md-12">
      <table class="table table-striped table-data table-view">
        <thead class="thead-dark">
          <tr>
            <th>Id</th> 
            <!--<th>Issue&nbsp;No</th>-->
            <th>Issue&nbsp;Invoice&nbsp;No</th>
            <th>Issue&nbsp;Date</th>  
            <th>Comments</th>
            <th>Voucher No</th>
            <th>Total Qty</th> 
            <th>Total Amount</th> 
            <th>Options</th> 
          </tr>
        </thead>
        <tbody>
          <?php 
            $qty = 0;
            $amount = 0;
          ?>
          @foreach($rows as $row)
          <tr>
            <td>{{ $row->id }}</td> 
            <!--<td>{{ $row->r_issue_order_no }}</td>-->
            <td>{{ $row->r_issue_order_ref }}</td>
            <td>{{ date('d-m-Y',strtotime($row->r_issue_order_date)) }}</td> 
            <td>{!! $row->r_issue_comments !!}</td>
            <th> {{ Helper::getVoucherNo($row->r_issue_order_no) }}</th>
            <td align="right">{{ number_format($row->r_issue_total_qty,2)}}</td> 
            <td align="right">{{ $row->r_issue_total_amount }}</td>
            <td>
                <form  method="post" action="{{ url('/itm-raw-issue/destroy/'.$row->id) }}" class="delete_form">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <div class="btn-group btn-corner">
                  <span href="{{ url('/') }}/itm-raw-issue/item-m-view/{{$row->id}}" data-toggle="modal" data-id="{{$row->id}}"
                      class="btn btn-sm btn-info viewModal" title="View Details" data-placement="top" >
                     View
                  </span> 
                  <a href="{{ route('raw.itm.issue.edit',$row->id) }}" target="_blank" class="btn btn-xs after-confirm{{ $row->id }}">Edit</a>


                  @if( $row->is_confirmed == 0 )  
                    <!-- <a href="{{ route('raw.itm.issue.edit',$row->id) }}" target="_blank" class="btn btn-xs after-confirm{{ $row->id }}">Edit</a> -->
                    <a href="{{ route('raw.itm.issue.confirm',$row->id) }}" target="_blank" id="{{ $row->id }}" onclick="afterClick(this.id)" class="btn btn-xs btn-warning confirm-btn after-confirm{{ $row->id }}">Confirm</a>  
                    <button class='btn btn-danger btn-sm delete after-confirm{{ $row->id }}'  type="submit" onclick="return confirm('Are You Sure? Want to Delete It.');">Delete</button>  
                  @endif  
                  <span href="{{ url('/') }}/raw-issue-invoice/acc-m-view/{{$row->r_issue_order_no}}/{{$row->r_issue_fin_year_id}}" data-toggle="modal" data-id="{{$row->r_issue_order_no}}" class="btn btn-sm btn-success viewModal" title="View">Acc.Doc</span> 
                </div>
              </form>
            </td>
          </tr>

          <?php 
              $amount += $row->r_issue_total_amount;
              $qty += $row->r_issue_total_qty;
          ?>
          @endforeach

          @if($item_id != '')
            <tr>
              <td colspan="5" align="right"><b>Total</b></td> 
              <td align="right"><b>{{ number_format($qty, 2) }}</b></td> 
              <td align="right"><b>{{ number_format($amount, 2) }}</b></td> 
              <td></td> 
            </tr>
          @endif

          </tbody>
        </table>
        </div>
      </div>
      <div class="col-md-12">
        <div class="card-tools">
            <ul class="pagination pagination-sm float-right">
              <p class="pull-right">
                 {{ $rows->withQueryString()->render("pagination::bootstrap-4") }}
              </p>
            </ul>
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

    function afterClick(id) {
      console.log(id);
      $('.after-confirm'+id).hide();
    }

    $(document).ready(function() {
      // show modal
      $('.viewModal').click(function(event) {
          event.preventDefault();

          var url = $(this).attr('href');
        //  alert(url);
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
