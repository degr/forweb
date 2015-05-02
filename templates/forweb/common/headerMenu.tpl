<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header active">
            {if $pageId == 1}
                <span class="navbar-brand">ForWeb</span>
            {else}
                <a class="navbar-brand" href="{$c.url}">ForWeb</a>
            {/if}
        </div>
        <ul class="nav navbar-nav">
            {foreach from=$pages item=item}
                <li class="{$item.active}">
                    <a href="{$c.url}{$item.url}" rel="tooltip" data-toggle="tooltip" data-placement="bottom" title="{$item.info}" >
                        {$item.name}
                    </a>
                </li>
            {/foreach}
        </ul>
        <form method="post" action="{$c.url}form/search/fromHeader" class="navbar-form navbar-right" role="search">
            <div class="form-group">
                <input type="text" class="form-control" name="value" placeholder="{word module="common" term="search"}">
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
        </form>
    </div>
</nav>





