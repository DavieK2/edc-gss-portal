@extends('officials.layouts.app')
@section('content')
<div class="main-container">
    <div class="pd-20 card-box mb-30 m-4 p-4">
        <div class="clearfix">
            <h5 class="mb-30 text-primary">Update Course Departments: {{ $course->item_code }}</h5>
            <hr>
        </div>
        @error('departments')
            <div class="alert alert-danger col-md-9" role="alert">
               {{ $message }}
            </div>
        @enderror
        @if(session('error'))
            @foreach (session('error') as $error)
                <div class="alert alert-danger col-md-9" style="padding: .5rem 1.25rem" role="alert">
                   <small>{{ $error }}</small> 
                </div>
            @endforeach
        @endif
        <div class="wizard-content py-3">
            <form action="{{ route('officials.courses.update.departments', [$role, $course, $scheme]) }}" method="POST">
                @method('PATCH')
                @csrf
                <section>
                    <div class="row">
                        <div class="col-12 pb-20">
                            <table class="table hover table-bordered multiple-select-row data-table-export nowrap">
                                <thead>
                                    <tr>
                                        <th class="table-plus datatable-nosort"></th>
                                        <th>Departments</th>
                                        <th>Semesters</th>
                                        <th>Levels</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($departments as $key => $department)
                                        <tr>
                                            <td class="table-plus text-center">
                                                <div class="custom-control custom-checkbox mb-5">
                                                    <input type="checkbox" 
                                                        @isset(session('departments')[$department->id]) 
                                                            {{ 'checked' }} 
                                                        @else 
                                                            {{ in_array($department->id, $course->departments->pluck('id')->toArray()) ? 'checked' : '' }} 
                                                        @endisset  
                                                        class="custom-control-input" value="{{ $department->id }}" name="departments[{{ $department->id }}][department]" id="department-{{ $department->id }}"
                                                    >
                                                    <label class="custom-control-label" for="department-{{ $department->id }}"></label>
                                                </div>
                                            </td>
                                            <td>{{ $department->name }}</td>
                                            <td>
                                                @foreach ($semesters as $semester)
                                                    <div class="" style="display:flex;">
                                                        <input type="checkbox" 
                                                            @isset(session('departments')[$department->id]['semesters']) 
                                                                {{ in_array($semester->id , session('departments')[$department->id]['semesters']) ? 'checked' : '' }}
                                                            @else
                                                                {{ in_array($semester->id, json_decode($department->courses()->where('course_id', $course->id)->first()?->pivot->semester_id) ?? []) ? 'checked' : '' }}
                                                            @endisset  
                                                            value="{{ $semester->id }}" name="departments[{{ $department->id }}][semesters][]" id="semester-{{ $department->id }}-{{ $semester->id }}"
                                                        >
                                                        <label class="" style="padding: 0.2rem; margin:0px" for="semester-{{ $department->id }}-{{ $semester->id }}">{{ $semester->semester }}</label>
                                                    </div>
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach ($levels as $key => $level)
                                                    <div class="" style="display:flex;">
                                                        <input type="checkbox" 
                                                        @isset(session('departments')[$department->id]['levels']) 
                                                            {{ in_array($level , session('departments')[$department->id]['levels']) ? 'checked' : '' }} 
                                                        @else
                                                            {{ in_array($level, json_decode($department->courses()->where('course_id', $course->id)->first()?->pivot->levels) ?? []) ? 'checked' : '' }}
                                                        @endisset  
                                                        value="{{ $level }}" name="departments[{{ $department->id }}][levels][]" id="level-{{ $department->id }}-{{ $key }}">
                                                        <label class="" style="padding: 0.2rem; margin:0px" for="level-{{ $department->id }}-{{ $key }}">{{ $level }}</label>
                                                    </div>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="input-group mb-0 py-3">
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </section>
    
    
            </form>
        </div>
    </div>
</div>
@endsection