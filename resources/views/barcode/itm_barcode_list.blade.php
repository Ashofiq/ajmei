@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
@stop
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="MM@1" class="form-control" required>

  <div class="title">
    <legend>
    <div class="widget-header widget-header-small">
        <h6 class="widget-title smaller">
          <font size="3" color="blue"><b>Item Master List</b></font>
        </h6>
       <div class="widget-toolbar">
         <a href="{{route('itm.barcode.view.rpt')}}" class="blue"><i class="fa fa-list"></i> List</a>
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
  <form id="myform" action="{{route('itm.barcode.view.rpt')}}" method="post">
  {{ csrf_field() }}
   <div class="row">
      <div class="col-md-6">
            <div class="input-group-prepend">
              <div class="input-group-text" style="min-width:110px">Item Name:</div>
              <select name="item_id" class="chosen-select" id="item_id">
                <option value="" selected>- Select Items -</option>
                @foreach($item_list as $item)
                    <option {{ old('item_id') == $item->id ? 'selected' : '' }} value="{{ $item->id }}">{{ $item->item_name }} - {{ $item->itm_cat_name }}-{{ $item->item_code }}</option>
                @endforeach
            </select>
            @error('item_id')
            <span class="text-danger">{{ $message }}</span>
            @enderror
           </div>
   </div>
   <div class="col-md-3">
     <button type="submit" name="submit" value='html' id='btn1' class="btn btn-sm btn-info">Search</button>
     &nbsp;<button type="submit" name="submit" value='html_1' id='btn2' class="btn btn-sm btn-info">HTML</button>
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
            <th>Category</th>
            <th>Item Code</th>
            <th>Name</th>
            <th>Desc</th>
            <th>Barcode</th>
            <!-- th>QR Code</th -->
            <th>Unit</th>
            <th>Options</th>
          </tr>
        </thead>
        <tbody>
          @foreach($rows as $row)
          <tr>
            <td>{{ $row->id }}</td>
            <td>{{ $row->itm_cat_name }} ( {{ $row->itm_cat_origin}})</td>
            <td>{{ $row->item_code }}</td>
            <td>{{ $row->item_name }}</td>
            <td>{{ $row->item_desc }}</td>
            <td>{{ $row->item_bar_code }}</td>
            <!-- td>{{ $row->item_qr_code }}</td -->
            <td style="display:none;">{{ $row->unit_id }}</td>
            <td>{{ $row->vUnitName }}</td>
            <td>
              <div class="col-sm-2">
                  <a href="#modal-import{{ $item->id }}" role="button" class="blue" data-toggle="modal"><i class="fa fa-print"></i></a>
                  {!! DNS1D::getBarcodeHTML($item->item_bar_code, "C39",1.3,44) !!}
                  <p><br>{{ $item->item_bar_code }}</p>
              </div>
            </td>

          </tr>
          @endforeach

          </tbody>
        </table>
        </div>
      </div>

</div>
</section>

<!--- Start Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">	<b>Edit:: Item Master</b></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="#" method="post" id="editForm" enctype="multipart/form-data">
        {{ csrf_field() }}
         {{ method_field('POST') }}
        <input type="text" name="id" id="id" class="form-control" readonly="true" />
        <br/>
        <div class="modal-body">

          <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Item Code&nbsp;:</span>
                 </div>
                 <input type="text" name="itm_code" id="itm_code" class="form-control" autocomplete="off" required readonly/>
               </div>
   	       </div>
          </div>

         <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Item Name&nbsp;:</span>
                 </div>
                 <input type="text" name="itm_name" id="itm_name" class="form-control" required/>

               </div>
   	       </div>
          </div>

         <div class="row">
            <div class="col-md-8">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Item Desc&nbsp;:</span>
                </div>
                 <input type="text" name="itm_desc" id="itm_desc" class="form-control"/>

              </div>
            </div>
          </div>

          <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Packing&nbsp;:</span>
                 </div>
                  <input type="text" name="itm_pack" id="itm_pack" class="form-control" autocomplete="off"/>

               </div>
             </div>
           </div>

          <div class="row">
           <div class="col-md-8">
             <div class="input-group mb-3">
               <div class="input-group-prepend">
                 <span class="input-group-text">Unit&nbsp;:</span>
               </div>
                  &nbsp;&nbsp;<select name="itm_unit" id="itm_unit" required>
                     @foreach($unit_list as $u)
                         <option {{ old('itm_unit') == $u->id ? 'selected' : '' }} value="{{ $u->id }}">{{ $u->vUnitName }}</option>
                     @endforeach
                 </select>
             </div>
           </div>
         </div>
        </div>
         <div class="modal-footer">
           <button type="submit" class="btn btn-primary">Update</button>
           <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
         </div>
       </form>

    </div>
  </div>
</div>
<!--- End Edit Modal -->
@stop

@section('pagescript')
  <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('assets/js/chosen.jquery.min.js') }}"></script>
  <script src="{{ asset('assets/js/ace-elements.min.js') }}"></script>
  <script src="{{ asset('assets/js/ace.min.js') }}"></script>
  <script src="{{ asset('assets/blogic_js/sel_box_search.js') }}"></script>

  <script type="text/javascript">
    var form = document.getElementById('myform');
    document.getElementById('btn2').onclick = function() {
      form.target = '_blank';
      form.submit();
    }
  </script>

@stop
