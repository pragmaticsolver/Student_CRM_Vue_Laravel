

@extends('layouts.app')

@section('styles')
<style>
        
</style>    
@endsection

@section('content')
    <div style="font-size:20px;">

        <h1>Helper</h1>

        <textarea name="" id="input-text" rows="2" class="form-control" style="font-size:20px;"></textarea>
        
        <br>
        <br>
        <p id="output"></p>
        <button class="btn btn-primary btn-block" onclick="copyToClipboard(output_text)">Copy</button>

        <p id="output_1" class="mt-4"></p>
        <button class="btn btn-primary btn-block" onclick="copyToClipboard(output_text_1)">Copy</button>
    </div>

    
@endsection

@push('scripts')
<script>
    var output_text = '';
    var output_text_1 = '';
    window.addEventListener('DOMContentLoaded', function() {
        $('#input-text').keyup(function(){
            original_text = $(this).val();
            
            key = original_text.toLowerCase();
            key = key.replace(/ /g, '-');
            key = key.replace(/,/g, '');
            key = key.replace(/'/g, '');
            key = key.replace(/"/g, '');
            
            output_text = "__('messages." + key + "')"
            $('#output').text(output_text);

            output_text_1 = "'" + key + "' => " + "'" + original_text + "',"
            $('#output_1').text(output_text_1);
        });
    });

    window.copyToClipboard = function(string)
    {
        const el = document.createElement('textarea');  // Create a <textarea> element
        el.value = string;                                 // Set its value to the string that you want copied
        el.setAttribute('readonly', '');                // Make it readonly to be tamper-proof
        el.style.position = 'absolute';
        el.style.left = '-9999px';                      // Move outside the screen to make it invisible
        document.body.appendChild(el);                  // Append the <textarea> element to the HTML document
        const selected =
            document.getSelection().rangeCount > 0        // Check if there is any content selected previously
            ? document.getSelection().getRangeAt(0)     // Store selection if found
            : false;                                    // Mark as false to know no selection existed before
        el.select();                                    // Select the <textarea> content
        document.execCommand('copy');                   // Copy - only works as a result of a user action (e.g. click events)
        document.body.removeChild(el);                  // Remove the <textarea> element
        if (selected) {                                 // If a selection existed before copying
            document.getSelection().removeAllRanges();    // Unselect everything on the HTML document
            document.getSelection().addRange(selected);   // Restore the original selection
        }
    }
</script>
@endpush

