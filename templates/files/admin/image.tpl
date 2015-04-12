<div class="image_wrapper">
    <div class="canvas-wrapper left">
        <img class="canvas" {if $image}src="{$image}"{/if}/>
    </div>
    <div class="clearfix"></div>
    <input type="text" class="path" readonly value="{if $image}{$image}{/if}">
</div>