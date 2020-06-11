@csrf
<input type="hidden" name="course_id" value="{{ $course->id }}">
<ol class="units-reorder-section" style="max-height:400px;overflow-y:scroll;">
    @foreach($course->units as $unit)
        <li class="m-1">
            <a class="btn btn-secondary btn-block text-left text-white">
                {{ $unit->name }}
            </a>
            <input type="hidden" name="unit_ids[]" value="{{ $unit->id }}">
        </li>
    @endforeach
</ol>
