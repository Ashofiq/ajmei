@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/blogic_css/acc_tb.css') }}" />
@stop
@section('content')
<section class="content">
<div class="title">
  <legend>
  <div class="widget-header widget-header-small">
      <h6 class="widget-title smaller">
        <font size="3" color="blue"><b>Item Master Entry Form</b></font>
      </h6>
     <div class="widget-toolbar">
       <a href="{{route('itm.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
      </div>
  </div></legend>
</div>
@if(Session::has('message'))
 <div class="row">
   <div class="col-md-12">
     <p class="alert alert-success"><b>{{ Session::get('message') }}</b></p>
   </div>
</div>
@endif
  <form id="acc_Form" action="{{route('itm.store')}}" method="post">
    {{ csrf_field() }}
    <div class="widget-body">
      <div class="widget-main">

        <div class="row">
          <div class="col-md-4">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text" style="min-width:130px">Category Name:</div>
                </div>
                <div class="col-xs-12 col-sm-5">
                  <select name="itm_category" class="chosen-select" tabindex="2" id="itm_category" onchange="customer()" required>
                    <option value="" disabled selected>- Select Category -</option>
                    @foreach($itm_cat as $cat)
                        <option {{ old('itm_category') == $cat->id ? 'selected' : '' }} value="{{ $cat->id }}">{{ $cat->itm_cat_name }}</option>
                    @endforeach
                </select>
                @error('itm_category')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>
          </div>
       </div>

       <div class="row">
          <div class="col-md-4">
              <div class="input-group ss-item-required">
                  <div class="input-group-prepend ">
                      <div class="input-group-text" style="min-width:130px">Item Code:</div>
                  </div>
                  <input type="text" name="itm_code" id = "itm_code" value="" class="form-control" autocomplete="off" required/>
              </div>
          </div>
          <div class="col-md-4">
              <div class="input-group">
                  <div class="input-group-prepend">
                      <div class="input-group-text" style="min-width:130px">Item BarCode:</div>
                  </div>
                  <input type="text" name="itm_barcode" id="itm_barcode"  value="" class="form-control" required/>
             </div>
          </div>

          <div class="col-md-4">
               <div class="input-group">
                 <div class="input-group-prepend">
                     <div class="input-group-text" style="min-width:130px">Item QRCode:</div>
                 </div>
                 <input type="text" name="itm_qrcode" id="itm_qrcode" value="" class="form-control" required/>
                </div>
           </div>
      </div>
      <div class="row">
           <div class="col-md-6">
               <div class="input-group ss-item-required">
                   <div class="input-group-prepend ">
                       <div class="input-group-text" style="min-width:130px">Item Name:</div>
                   </div>
                   <input type="text" name="itm_name" id="itm_name" value="" class="form-control" autocomplete="off" required/>
              </div>
           </div>
           <div class="col-md-6">
               <div class="input-group ss-item-required">
                   <div class="input-group-prepend ">
                       <div class="input-group-text" style="min-width:130px">Item Desc:</div>
                   </div>
                   <input type="text" name="itm_desc" id="itm_desc" value="" class="form-control" autocomplete="off" required/>
              </div>
           </div>
      </div>
      <div class="row">
        <div class="col-md-3">
          <div class="input-group ss-item-required">
              <div class="input-group-prepend">
                <div class="input-group-text" style="min-width:130px">Packing:</div>
              </div>
              <input type="text" name="itm_desc" id="itm_desc" value="" class="form-control" autocomplete="off" required/>
            </div>
         </div>
         <div class="col-md-3">
           <div class="input-group ss-item-required">
               <div class="input-group-prepend">
                 <div class="input-group-text" style="min-width:130px">Unit:</div>
               </div>
               <input type="text" name="itm_desc" id="itm_desc" value="" class="form-control" autocomplete="off" required/>
             </div>
          </div>
      </div>
   <br/>
    <div class="row justify-content-left">
          <div class="col-sm-12 text-left">
              <button class="btn btn-sm btn-success" type="button" onclick="formcheck(); return false"><i class="fa fa-save"></i> Save</button>
              <button class="btn btn-sm btn-success" type="button"><i class="fa fa-save"></i> Print</button>
              <a href="{{route('itm.index')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> List</a>
          </div>
      </div>
    </div>

    </div>
  </div>
  </form>
</section>
@stop
@section('pagescript')
  <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('assets/js/chosen.jquery.min.js') }}"></script>
  <script src="{{ asset('assets/js/ace-elements.min.js') }}"></script>
  <script src="{{ asset('assets/js/ace.min.js') }}"></script>
  <script src="{{ asset('assets/blogic_js/sel_box_search.js') }}"></script>


@stop
