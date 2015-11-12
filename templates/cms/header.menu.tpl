<nav class="navbar navbar-default">
    <div class="container-fluid">
        <ul class="nav navbar-nav">
            {foreach from=$pages item=item}
                <li class="{$item.active}">
                    <a href="{$c.localUrl}{$item.url}" rel="tooltip" data-toggle="tooltip" data-placement="bottom" title="{$item.info}" >
                        {$item.name}
                    </a>
                </li>
            {/foreach}
        </ul>
        <form method="post" action="{$c.localUrl}search" class="navbar-form navbar-right" role="search" onsubmit = "Search.onAjaxSearch(this);return false;">
            <div class="form-group">
                <input type="text" class="form-control searchbar" name="value" placeholder="{word module="common" term="search"}">
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
        </form>
    </div>
</nav>





