<form class="user.forms.authorization" method="post" data-handler="User::onAjaxAuthorization">
    <div class="input-wrapper clearfix">
        {if $errors['email']}
            <div class="input-error">
                {foreach from=$errors['email'] item=$item}
                    <span class="error">$item</span>
                {/foreach}
            </div>
        {/if}
        <div class="input-title">
            <label for="auth_email">{word module="User" term="field_email"}</label>
        </div>
        <div class="input-body">
            <input type="text" name="email" id="auth_email">
        </div>
    </div>
    <div class="input-wrapper clearfix">
        {if $errors['password']}
            <div class="input-error">
                {foreach from=$errors['password'] item=$item}
                    <span class="error">$item</span>
                {/foreach}
            </div>
        {/if}
        <div class="input-title">
            <label for="auth_password">{word module="User" term="field_password"}</label>
        </div>
        <div class="input-body">
            <input type="password" name="password" id="auth_password">
        </div>
    </div>
    <div class="input-wrapper clearfix">
        <div class="input-body">
            <input type="submit">
        </div>
    </div>
</form>