@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">
            <i class="fa fa-gear"></i>
           {{ trans('custom.roles') }}
        </h1>
        <h1 class="text-center">
           <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('roles.create') !!}">
                <i class="fa fa-fw fa-plus-square"></i>
               {{ trans('custom.add_new') }}
           </a>
        </h1>
        <ol class="breadcrumb" style="position:initial !important; float:left !important; margin-top:-2px;margin-left: 20px;">
            <li>
                <a href="{!! route('home.index') !!}">
                    <i class="fa fa-dashboard"></i> 
                    {{ trans('custom.home') }}
                </a>
            </li>
            <li class="active">
                {{ trans('custom.roles') }}
            </li>
        </ol>
    </section>
<br
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                    @include('roles.table')
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script type="text/javascript">
        $('#roles_table').dataTable().yadcf([
                    {   column_number : 0,
                        filter_type: "text",
                        filter_default_label: ""
                    },
                    {   column_number : 1,
                        filter_type: "text",
                        filter_default_label: ""
                    },
                  ]);
    $('#roles_table').addClass("cell-border hover");
      
      
    </script>
@endpush
