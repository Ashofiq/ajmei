@extends('layouts.app')

@section('content')

<!-- Main content -->
<section class="content">
<input type="text" name="menu_selection" id="menu_selection" value="HOME@1" class="form-control" required>

<div class="container-fluid">
  <div class="row">
      <div class="col-12">
        <div class="card">
          @if(Session::has('message'))
           <p class="alert alert-success"><b>{{ Session::get('message') }}</b></p>
          @endif
        </div>
      </div>
  </div>
</div><!-- /.container-fluid -->
</section> <!-- /.content -->

@endsection
