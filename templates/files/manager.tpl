{*
    @var $object string - java script object name

    @var $newFolder - java script $object method to create new folder
    @var $newFile - java script $object method to create new file
    @var $upload - java script $object method to upload file
    @var $uploadTarget - string - target url to upload

    @var uploadIframeOnload - string, javascript object onload handler
    @var upload_iframe_id string - string for upload iframe

    @var $path - string - current file system path
*}

<div class="file_manager">
    {if $object}
        {if $newFolder}
            <a href="#" onclick="{$object}.{$newFolder}();return false;" class="icon-file-32-new-folder"></a>
        {/if}
        {if $newFile}
            <a href="#" onclick="{$object}.{$newFile}();return false;" class="icon-file-32-new-file"></a>
        {/if}
        {if $upload && $uploadTarget}
            <form target="{if $uploadIframeId}{$uploadIframeId}{else}upload_iframe{/if}" method="post" action="{$uploadTarget}" enctype="multipart/form-data">
                <input type="hidden" name="path" value="{if $path}{$path}{/if}">
                <label href="#" for="{$object}_{$upload};return false;" class="icon-file-32-upload">
                    <input type="file" name="{$object}_{$upload}" id="{$object}_{$upload}" class="opacity-cover" onchange="{$object}.{$upload}(this)">
                </label>
            </form>
            <iframe {if $uploadIframeOnload}onload="{$object}.{$uploadIframeOnload}(this)"{/if}class="hidden" {if $uploadIframeId}id="upload_iframe"{/if} name="{if $uploadIframeId}{$uploadIframeId}{else}upload_iframe{/if}"></iframe>
        {/if}
    {/if}
</div>