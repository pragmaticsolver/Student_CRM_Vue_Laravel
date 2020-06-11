<tr>
    <th class="p-2">
        <div class="pull-left">
            <div class="mt-2">{{  $yoyaku->student->getFullNameAttribute() }}</div>
        </div>
        <div class="pull-right">
            @if($yoyaku->schedule->isPastClassCheckPasses(\Auth::user(),$yoyaku->date))
                <form class="mb-0 cancel-reservation simple-cancel">
                    <input type="hidden" name="yoyaku_id" value="{{$yoyaku->id}}">
                    <button class="btn btn-danger" type="submit">{{ __('messages.cancel') }}</button>
                </form>
            @endif
        </div>
    </th>
</tr>
