@props(['page', 'role', 'headings', 'ajaxUrl', 'searchLabel', 'name', 'filters', 'checkFormActions'])

<div>

    <div class="pd-20 card-box mb-30">
        <div class="clearfix">
            <h5 class="mb-30 text-primary">Search</h5>
            <hr>
        </div>

        <div class="wizard-content py-3">
            <form action="" method="POST" id="search">
                @csrf
                <section>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label  style="font-size:16px ; font-weight:600" >{{ $searchLabel }}</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input  autocomplete="off" type="text" class="form-control" name="{{ $name }}" id="code" placeholder="{{ $searchLabel }}" >
                                    </div>
                                    <button class="btn btn-primary mr-1" type="submit">Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </div>
        <div class="wizard-content pb-3">
            <form action="" method="POST" id="search-date">
                @csrf
                <section>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label style="font-size:16px ; font-weight:600" >Select Date Range</label>
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <input autocomplete="off" type="text" class="form-control date-picker" name="startDate" id="startDate" placeholder="Select Start Date" >
                                    </div>
                                    <span>TO</span>
                                    <div class="col-md-4">
                                        <input autocomplete="off" type="text" class="form-control date-picker" name="endDate" id="endDate" placeholder="Select End Date" >
                                    </div>
                                    <button class="btn btn-primary mr-1" type="submit">Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </div>

        @if (! empty($filters ?? []) )
            <label style="font-size:16px ; font-weight:600" for="">Filters</label>
            <form class="row" id="filters">
                @foreach ($filters as $key => $filter)
                        <div class="col-md-3">
                            <select class="custom-select" name="{{ $key }}" id="{{ $key }}">
                                <option selected value="">Select {{ Illuminate\Support\Str::of($key)->headline() }}</option>
                                @foreach ($filter as $index => $value )
                                    <option value="{{ $index }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                @endforeach
            </form>
        @endif

        <div class="row mt-4">
            <div class="col-12">
                <form id="reset" action="">
                    <button class="btn btn-secondary" type="reset">Reset</button>
                </form>
            </div>
        </div>
    </div>

    
    <div class="card-box mb-30">
        <div class="pd-20">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="text-blue h4">{{ $page }}</h4>
                </div>
                <div style="display: flex; justify-content: end" class="col-md-6">{{ $actions ?? '' }}</div>
            </div>
        </div>
        <div class="pb-20">
            <form action="" id="checkbox-action" method="post">
                @csrf
                <table class="table hover table-bordered data-table-export">
                    <thead>
                        <tr>
                            @foreach ($headings as $heading)
                                @if (isset($heading['type']))
                                    <th class="text-uppercase">{{ $heading['title'] }}</th>
                                @else
                                    <th class="text-uppercase">{!! $heading  !!}</th>
                                @endif
                            @endforeach
                        </tr>
                    </thead>
                </table>
            </form>
        </div>
    </div>


</div>

@section('scripts')

    <script src="{{ asset('assets/src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/src/plugins/datatables/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/src/plugins/datatables/js/responsive.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('assets/src/plugins/datatables/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/src/plugins/datatables/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/src/plugins/datatables/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/src/plugins/datatables/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/src/plugins/datatables/js/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('assets/src/plugins/datatables/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/src/plugins/datatables/js/vfs_fonts.js') }}"></script>
   
    {{-- <script src="{{ asset('assets/vendors/scripts/datatable-setting.js') }}"></script> --}}
    
   
    <script>
        $('document').ready(function(){

            $('.data-table-export').DataTable({
                processing: true,
                serverSide: true,
                scrollCollapse: true,
                autoWidth: false,
                responsive: true,
                ajax: {
                    "url" : '{{ $ajaxUrl }}',
                    "type" : "GET",
                    "dataSrc" : "data",
                    "data": function(data){

                        let startDate = $('#startDate');
                        let endDate = $('#endDate');
                        let column = $('#code');
                        let filters = [];
                        
                        data.startDate = startDate.val();
                        data.endDate = endDate.val();
                        data.column = column.attr('name');

                        @if( ! empty($filters) )
                            @foreach($filters as $key => $filter)
                                let {{ $key }} = $("#{{ $key }}");
                                filters.push({"{{ $key }}" : {{ $key }}.val()})
                                data.filters = filters;
                            @endforeach
                        @endif
                    }
                },
                columnDefs: [
                    { targets: '_all', orderable: false }
                ],
                columns: [
                    @foreach($headings as $key => $heading)

                        @if(isset($heading['type']) && $heading['type'] == 'list')
                            { data: "{{ $key }}",
                                render : function(data, type, row) {

                                    let ul = document.createElement('ul');

                                    data.forEach((item) => {
                                        let li = document.createElement('li');
                                        let value = document.createTextNode(item);
                                        li.appendChild(value);
                                        ul.appendChild(li);
                                    });
                                    return ul.outerHTML;    
                                } 
                            },
                        @elseif($key == 'action')
                            { data: "{{ $key }}",
                                render: function(data, type, row){

                                    let ul = document.createElement('ul');

                                    data.forEach((item) => {
                                        let a = document.createElement('a');
                                        let span = document.createElement('span');
                                        let value = document.createTextNode(item['title']);

                                        a.setAttribute('href', item['url']);
                                        a.setAttribute('class', 'btn btn-success btn-sm mr-1');
                                        
                                        a.appendChild(value);

                                        ul.appendChild(a);
                                    });

                                    return ul.outerHTML;
                                }
                            },
                        @else
                            { data: "{{ $key }}" },
                        @endif

                    @endforeach
                ],
                // 'pagingType': 'simple',
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                "language": {
                    "info": "_START_-_END_ of _TOTAL_ entries",
                    searchPlaceholder: "Search",
                    paginate: {
                        next: '<i class="ion-chevron-right"></i>',
                        previous: '<i class="ion-chevron-left"></i>'  
                    }
                },
                dom: 'lBrtip',
                buttons: [
                'copy', 'csv', 'pdf', 'print'
                ]
            });

            $('#search').on('submit', function(){
                event.preventDefault();
                $('.data-table-export').DataTable().search($('#code').val()).draw();
            });

            $('#search-date').on('submit', function(){
                event.preventDefault();
                $('.data-table-export').DataTable().search($('#code').val()).draw();
            });

            $('#reset').on('reset', function(){
                $('#search')[0].reset();
                $('#filters')[0].reset();
                $('#search-date')[0].reset();
                $('.data-table-export').DataTable().search('').draw();
            });
            
            @if( ! empty($filters) )
                let values = [];
                let total_filters = {{ count($filters) }}
                @foreach($filters as $key => $filter)
                    $("#{{ $key }}").on('change', function(){
                        $('.data-table-export').DataTable().search($('#code').val()).draw();
                    });
                @endforeach
            @endif


            @isset( $checkFormActions )
            
                @foreach($checkFormActions as $key => $action)

                    $("#{{ $key }}").on('click', function(){

                        $('#checkbox-action').prop('action', "{{ $action }}");
                        $('#checkbox-action').submit();
                    });
                @endforeach

            @endisset

            $('#checkall').on('click', function(){
                $('.check').prop('checked', this.checked);
            });
        });
    </script>

@endsection

@section('styles')
    <style>
        table.dataTable thead .sorting:before, 
        table.dataTable thead .sorting_asc:before, 
        table.dataTable thead .sorting_asc_disabled:before, 
        table.dataTable thead .sorting_desc:before, 
        table.dataTable thead .sorting_desc_disabled:before{
            content: '';
        }

        table.dataTable thead .sorting:after, 
        table.dataTable thead .sorting_asc:after, 
        table.dataTable thead .sorting_asc_disabled:after, 
        table.dataTable thead .sorting_desc:after, 
        table.dataTable thead .sorting_desc_disabled:after{
            content: '';
        }
    </style>
@endsection