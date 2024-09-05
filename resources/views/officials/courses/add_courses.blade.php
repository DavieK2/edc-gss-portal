@extends('officials.layouts.app')
@section('content')
<div class="main-container">
    <div class="pd-20 card-box mb-30 m-4 p-4">
        <div class="clearfix">
            <h5 class="mb-30 text-primary">Add Courses</h5>
            <hr>
        </div>
        <div class="wizard-content py-3">
            
            <form action="{{ route('officials.courses.add_courses_to_session', [$role, $scheme]) }}" method="POST">
                @csrf
                <section>
                    <div class="row">
                        <div class="col-12">
                            <table class="table responsive table-striped mt-5 mb-0">
                                @foreach ($courses as $key => $course)
                                    <tr>
                                        <td style="width: 5%">
                                            <div class="custom-control custom-checkbox mb-5">
                                                <input type="checkbox" {{ in_array($course->id, $session_courses) ? 'checked' : '' }} class="custom-control-input" value="{{ $course->id }}" name="courses[]" id="course{{ $course->id }}">
                                                <label class="custom-control-label" for="course{{ $course->id }}"></label>
                                            </div>
                                        </td>
                                        <td><span class="text-primary">{{ $course->item_code }}</span>  - {{ $course-> title }}</td>
                                        <td style="width: 40%">
                                            <div class="d-flex flex-wrap align-items-center">
                                                @foreach ($levels as $level)
                                                    <div class="d-flex pr-4 align-items-center">
                                                        <input type="checkbox" name="levels[]" id="level-{{ $key }}-{{ $level->id }}">
                                                        <label class="pl-2 pt-2" for="level-{{ $key }}-{{ $level->id }}">{{ $level->level }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </section>

                <div class="input-group mb-0 py-3">
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection