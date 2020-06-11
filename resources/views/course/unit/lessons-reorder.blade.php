@csrf
<input type="hidden" name="unit_id" value="{{ $unit->id }}">
<ol class="lessons-reorder-section" style="max-height:400px;overflow-y:scroll;">
    @foreach($unit->lessons as $lesson)
        <li class="m-1">
            <a class="btn btn-secondary btn-block text-left text-white">
                {{ $lesson->title }}
            </a>
            <input type="hidden" name="lesson_ids[]" value="{{ $lesson->id }}">
        </li>
    @endforeach
</ol>
