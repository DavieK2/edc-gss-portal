@extends('officials.layouts.app')
@section('content')
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="title">
                                <h4>{{ $page }}</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ $page }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

            
                <x-datatable page="{{ $page }}" :filters="$filters" :headings="$tableHeadings" name="{{ $name }}" role="{{ $role }}" ajaxUrl="{{ $ajaxUrl }}" searchLabel="Search" :checkFormActions="['activate' =>  route('officials.vendors.bulkactivate', [$role]), 'deactivate' => route('officials.vendors.bulkdeactivate', [$role])]">
                    <x-slot:actions>
                       <div style="display:flex; align-items:center" >
                            <button type="button" id="activate" class="btn btn-sm btn-success mr-2">Activate</button>
                            <button type="button" id="deactivate" class="btn btn-sm btn-danger">Deactivate</button>
                       </div>
                    </x-slot>
                </x-datatable> 
            </div>

        </div>
    </div>
    
@endsection