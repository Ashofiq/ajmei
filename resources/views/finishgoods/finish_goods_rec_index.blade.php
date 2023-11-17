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
          <font size="3" color="blue"><b>Finish Goods Receive List</b></font>
        </h6>
       <div class="widget-toolbar">
         <!-- <a href="{{route('fin.goods.rec.index')}}" class="blue"><i class="fa fa-list"></i> List</a> -->
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
  <form action="{{route('fin.goods.rec.index')}}" method="post">
  {{ csrf_field() }}
   <div class="row">
       <div class="col-md-2">
         <a href="{{route('fin.goods.rec.create')}}" class="btn btn-success btn-sm">
                  <i class="fa fa-plus"></i>Add New</a>
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
            <th>Receive&nbsp;No</th>
            <th>Receive&nbsp;Date</th>  
            <th>Comments</th>
            <th align="center">Production Per Day <br> (PCS)</th>
            <th>Total Weight</th>
            <th>Per Bag <br> (KG)</th>
            <th>Issue Prod Ref No</th>
            <th>Options</th>
          </tr>
        </thead>
        <tbody>
          <?php $totalBagWeight = 0; $productionPerDay = 0; $totalWeight = 0; ?>
          @foreach($rows as $row)
          <?php $perbag = number_format($row->f_rec_total_qty / $row->f_rec_item_pcs, 3); ?>
          <tr>
            <td>{{ $row->id }}</td> 
            <td>{{ $row->f_rec_order_no }}</td>
            <td>{{ date('d-m-Y',strtotime($row->f_rec_order_date)) }}</td> 
            <td>{!! $row->f_rec_comments !!}</td>
            <td align="right">{{ $row->f_rec_item_pcs }}</td>
            <td align="right">{{ number_format($row->f_rec_total_qty,2)}}</td> 
            <td> {{ $perbag  }}</td>
            <td>{{ $row->r_issue_order_ref }}</td>
            <td>
                <form  method="post" action="{{ url('/fin-goods-rec/destroy/'.$row->id) }}" class="delete_form">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <div class="btn-group btn-corner">
                  <span href="{{ url('/') }}/fin-goods-rec/item-m-view/{{$row->id}}" data-toggle="modal" data-id="{{$row->id}}"
                      class="btn btn-sm btn-info viewModal" title="View Details" data-placement="top" >
                     View
                  </span>                     
                  <a href="{{ route('fin.goods.rec.edit',$row->id) }}" target="_blank" class="btn btn-xs after-confirm{{ $row->id }}">Edit</a>


                  @if( $row->is_confirmed == 0 )  
                    <a href="{{ route('fin.goods.rec.confirm',$row->id) }}" target="_blank" id="{{ $row->id }}" onclick="afterClick(this.id)" class="btn btn-xs btn-warning after-confirm{{ $row->id }}">Confirm</a>  
                    <button class='btn btn-danger btn-sm delete after-confirm{{ $row->id }}'  type="submit" onclick="return confirm('Are You Sure? Want to Delete It.');">Delete</button>  
                  @endif  
                  <span href="{{ url('/') }}/fin-goods-rec-invoice/acc-m-view/{{$row->f_rec_order_no}}/{{$row->f_rec_fin_year_id}}" data-toggle="modal" data-id="{{$row->f_rec_order_no}}" class="btn btn-sm btn-success viewModal" title="View">Acc.Doc</span> 
                </div>
              </form>
            </td>
          </tr>
          <?php $totalBagWeight += $perbag;
                $productionPerDay += $row->f_rec_item_pcs;
                $totalWeight += $row->f_rec_total_qty;
          ?>
          @endforeach

          <tr>
            <td></td> 
            <td></td>
            <td></td> 
            <td align="right"><b>Total:</b> </td>
            <td align="right"><b>{{ $productionPerDay }}</b></td>
            <td align="right"> <b>{{ $totalWeight }} </b> </td> 
            <td><b> {{ number_format($totalBagWeight / 26, 3)  }}</b>  </td>
            <td></td>

            <td>
               
            </td>
          </tr>

          <tr>
            <td></td> 
            <td></td>
            <td></td> 
            <td> </td> 
            
            <td align="right"><b>Average:</b> </td>
            <td align="right"><b>{{ ($productionPerDay == 0 && $totalWeight == 0) ? 0 : number_format($totalWeight / $productionPerDay, 3)  }}</b></td>
            <td>  </td>
            <td></td>

            <td>
               
            </td>
          </tr>
        

          </tbody>
        </table>
        </div>
      </div>
      <div class="col-md-12">
        <div class="card-tools">
            <ul class="pagination pagination-sm float-right">
              <p class="pull-right">
                 
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
