<div class="file_wrapper">
    <textarea id="admin_file_content">{if $text}{$text|escape}{/if}</textarea>
    <div class="clearfix"></div>
    <input type="submit" value="{word module='common' term='submit'}" onclick="FilesForm.updateTextFile();">
</div>