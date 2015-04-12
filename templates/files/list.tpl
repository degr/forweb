<ul class="file-manager" data-path="{$path}">
    {foreach from=$content item=item}
        <li class="manager-type-{$item.type}">
            {if $deleteFile}<a href="#" onclick="{$deleteFile}(this, '{$item.path}');return false;" class="left icon-delete" ></a>{/if}
            <span class="left icon-file-16-{$item.type}"></span>
            <a href="{$c.url}{$item.path}" class="file-manager-item">
                {$item.name}
            </a>
        </li>
    {/foreach}
</ul>