<ul style="position: fixed;right: 20px;top: 20px;">
{foreach from=$languages item=item}
    <li>
        <a href="?lang={$item.locale}"><img src="{$c.url}img/flags/{$item.locale}.gif" alt="{$item.locale}"></a>
    </li>
{/foreach}
</ul>