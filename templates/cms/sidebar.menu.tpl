<div class="list-group">
    {foreach from=$pages item=item}
        <a href="{$c.localUrl}{$item.url}" class="list-group-item {$item.active}">
            {$item.name}
        </a>
    {/foreach}
</div>