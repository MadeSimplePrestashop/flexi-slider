<script type="text/javascript">
    $(document).ready(function () {
        $('#inspector-tools').prependTo($('body'));
        $('#inspector-note').prependTo($('body'));
    })
</script>
<div id="inspector-note" class="ignore">
    {l s='Note: Use click  for select element, doubleclick for location to link' mod='flexislider'}
</div>
<div id="inspector-tools" class="container ignore" style="display: none">
    <form action="">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <input class="cancelinspector" value="Cancel and select another element" type="submit">                
            </div>
            <div class="col-xs-12 col-sm-6">    
                <input class="submitinspector" value="Save and close" type="submit">                
            </div>
        </div>
        <input name="element" placeholder="Path to element" id="inspector-text" class="text" type="hidden">
    </form>
</div>