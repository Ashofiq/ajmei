@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
@stop
@section('content')

<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="MM@1" class="form-control" required>

  <div class="container">
  <div class="row">
    <div class="col-12">
        @if(Session::has('message'))
           <p class="alert alert-success"><b>{{ Session::get('message') }}</b></p>
        @endif
    </div>
  </div>
  <br/>
  <div class="row">
    <div class="col-md-1">
        <p1 class="card-title"></p1>
     </div>

     <div class="col-md-4">
       <form action="{{route('itm.cat.tree.view')}}" method="post" target="_blank">
       {{ csrf_field() }}
         <input type="hidden" name="comp_id" id="comp_id" value="{{$companycode}}" class="form-control" autocomplete="off" required readonly/>
       <div class="input-group">
           <select name="itm_category" class="chosen-select" id="itm_category" required>
               <option value="0" selected>- Select Category -</option>
               @foreach($itm_cat as $cat)
                   <option {{ old('itm_category') == $cat->id ? 'selected' : '' }} value="{{ $cat->id }}">{{ $cat->itm_cat_name }} - {{ $cat->itm_cat_origin }}</option>
               @endforeach
           </select>
           @error('itm_category')
           <span class="text-danger">{{ $message }}</span>
           @enderror
        </div>
      </div>
      <div class="col-md-1">
        <button type="submit" class="btn btn-default">View</button>
      </form>
     </div>
  </div>
  <br/>



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

  function company($com_id){
    $('#comp_id').val($com_id);
    //alert($com_id);
  }

  $(document).ready(function() {

  });
</script>

@stop
