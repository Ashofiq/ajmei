@extends('master')

@section('title','Edit User')

@section('page_title')

    <i class="fa fa-users"></i> Users

@stop


@section('css')

    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.custom.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datepicker3.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-timepicker.min.css') }}" />

    <style>

        tbody.saleTable {
            display:block;
            height:200px;
            overflow:auto;
            scroll-padding: -1px;
            border: 1px solid #bababa;
        }
        thead.saleTable, tbody.saleTable tr {
            display:table;
            width:100%;
            table-layout:fixed;
        }
        thead.saleTable{
            width: calc( 100%);
            border: 1px solid #bababa;
        }

        input[readonly].totalVat{
            background-color: rgba(255,158,17,0.2) !important;
        }
        input[readonly].totalBill{
            background-color: rgba(3,255,13,0.2) !important;
        }
        input[readonly].netBill{
            background-color: rgba(37,255,202,0.2) !important;
        }

    </style>

@stop



@section('content')

    <div class="page-header">

        <h1 style="font-size: 18px;">
            <i class="fa fa-users"></i> Edit User

        </h1>
    </div>

    <div class="row">
        <div class="col-sm-12 border">
            <div class="col-md-12 col-lg-6 col-lg-offset-3 col widget-container-col ui-sortable" id="widget-container-col-7" style="min-height: 109px;">
                <div class="widget-box widget-color-blue2 ui-sortable-handle" id="widget-box-7" style="opacity: 1;">
                    <div class="widget-header widget-header-small">
                        <h6 class="widget-title smaller">
                            <i class="fa fa-pencil-square"></i> Edit User
                        </h6>


                        <div class="widget-toolbar">
                            <a href="{{route('user.index')}}" class="white"><i class="fa fa-list"></i> List</a>
                        </div>

                    </div>
                    <form action="{{route('user.update', $user)}}" method="post" class="form-horizontal">
                        @csrf

                        <div class="widget-body">
                            <div class="widget-main">
                                <div class="row">
                                    <div class="col-sm-12">

                                        @include('partial._errorMsg')

                                        <div class="row">
                                            <div class="col-xs-12">

                                                <div>

                                                    <div class="form-group">
                                                        <label for="inputError" class="col-xs-12 col-sm-3 control-label bolder">Name</label>

                                                        <div class="col-xs-12 col-sm-3">
                                                            <input type="text" class="form-control input-sm date" autocomplete="off" name="date" value="{{ $user->name  }}">
                                                            @error('name')
                                                            <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                </div>
                                                <div>

                                                    <div class="form-group">
                                                        <label for="inputError" class="col-xs-12 col-sm-3 control-label bolder">Email</label>

                                                        <div class="col-xs-12 col-sm-3">
                                                            <input type="email" class="form-control input-sm date" autocomplete="off" name="date" value="{{ $user->email  }}">
                                                            @error('email')
                                                            <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12 text-center">
                                                <button class="btn btn-sm btn-success" type="submit"><i class="fa fa-save"></i> Save</button>
                                                <a class="btn btn-xs btn-info" href="#modal-form" role="button" class="blue" data-toggle="modal"> Change Password</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>


                </div>
            </div>

        </div>
    </div>

    <div id="modal-form" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="blue bigger"><i class="fa fa-plus"></i> Change Password</h4>
                </div>


                <div class="modal-body">
                    <div class="row">
                        <form class="form-horizontal" action="{{ route('password.update') }}" method="post" role="form">
                            @csrf
                            <div class="col-sm-12">



                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="form-field-1-1"> Old Password </label>

                                    <div class="col-xs-12 col-sm-8 @error('old_password') has-error @enderror">
                                        <input type="text" class="form-control" name="name"
                                               value="{{  old('old_password')  }}" placeholder="Name">

                                        @error('name')
                                        <span class="text-danger">
                                                     {{ $message }}
                                                </span>
                                        @enderror

                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-sm" data-dismiss="modal">
                        <i class="ace-icon fa fa-times"></i>
                        Cancel
                    </button>
                </div>


            </div>
        </div>

    </div>
@stop


