{counter assign='flexicounter'}
{if $slider} 
    <script type="text/javascript">
        var flexislider;
                $(document).ready(function () {
        $('.flexislider{$flexicounter|intval}').moodular({
        effects: '{if $slider->options->effect|escape:'htmlall':'UTF-8' == 'carousel'}{$slider->options->move|escape:'htmlall':'UTF-8'}{else}{$slider->options->effect|escape:'htmlall':'UTF-8'}{/if}',
                controls: '{if $slider->options->keys|default}keys{/if} {if $slider->options->buttons|default}buttons{/if} {if $slider->options->touch|default}touch{/if} {if $slider->options->pagination|default}pagination{/if} {if $slider->options->startOnMouseOver|default}startOnMouseOver{/if} {if $slider->options->stopOnMouseOver|default}stopOnMouseOver{/if}',
                            easing: '{$slider->options->easing|escape:'htmlall':'UTF-8'}',
                            step: {$slider->options->step|escape:'htmlall':'UTF-8'|default:1},
                            selector: 'li',
                            timer: {$slider->options->timer|escape:'htmlall':'UTF-8'|default:0},
                            speed: {$slider->options->speed|escape:'htmlall':'UTF-8'|default:500},
                            queue: true,
                            keyPrev: 37,
                            keyNext: 39,
        {if $slider->options->effect|default == 'mosaic'}
            {if $slider->options->slicesx|default && $slider->options->slicesy|default}slices: [{$slider->options->slicesx|escape:'htmlall':'UTF-8'|default:0}, {$slider->options->slicesy|escape:'htmlall':'UTF-8'|default:0}],{/if}
            {if $slider->options->mode|default}mode : '{$slider->options->mode|escape:'htmlall':'UTF-8'}',{/if}
        {/if}
        {if $slider->options->effect|default == 'stripes'}
            {if $slider->options->stripes|default}stripes : {$slider->options->stripes|escape:'htmlall':'UTF-8'},{/if}
            {if $slider->options->orientation|default}orientation : '{$slider->options->orientation|escape:'htmlall':'UTF-8'}',{/if}
        {/if}
        {if $slider->options->effect|default == 'slide'}
            {if $slider->options->direction|default}direction : '{$slider->options->direction|escape:'htmlall':'UTF-8'}',{/if}
        {/if}
        {if $slider->options->effect|default == 'carousel'}
            {if $slider->options->view|default}view : {$slider->options->view|escape:'htmlall':'UTF-8'},{/if}
        {/if}
        {if $slider->options->buttons|default}
                buttonPrev: $('.flexislider-prev'),
                        buttonNext: $('.flexislider-next'),
        {/if}
                });
        {if $slider->options->element}
            {if $slider->options->insert == 'after'}
                $('.{$slider->alias|escape:'html':'UTF-8'}').insertAfter($('{$slider->options->element|escape:'html':'UTF-8'}'));
            {elseif $slider->options->insert == 'before'}
                $('.{$slider->alias|escape:'html':'UTF-8'}').insertBefore($('{$slider->options->element|escape:'html':'UTF-8'}'));
            {elseif $slider->options->insert == 'prepend'}
                $('.{$slider->alias|escape:'html':'UTF-8'}').prependTo($('{$slider->options->element|escape:'html':'UTF-8'}'));
            {elseif $slider->options->insert == 'append'}
                $('.{$slider->alias|escape:'html':'UTF-8'}').appendTo($('{$slider->options->element|escape:'html':'UTF-8'}'));
            {/if}
        {/if}
                })
    </script>
    {if $slides}
        <div style="max-width:{$slider->options->width|default}" class="flexislider {$slider->alias|escape:'html':'UTF-8'} clearfix"> 
            <ul class="flexislider{$flexicounter|intval}" style=" height:{$slider->options->height|default}px">
                {foreach from=$slides item='slide' name='slider'}
                    <li>
                        <div class="case" style="{$slide.options->size|escape:'html':'UTF-8'}{$slide.options->imagePosition|escape:'html':'UTF-8'}background-image: url('{$link->getMediaLink($slide.image_helper.dir|cat:$slide.image)|escape:'htmlall':'UTF-8'}'); {if $slide.options->backgroundColor}background-color:{$slide.options->backgroundColor|escape:'html':'UTF-8'}{/if}">
                            {if $slide.url}<a href="{$slide.url|escape:'html':'UTF-8'}" {if $slide.target}target="{$slide.target|escape:'html':'UTF-8'}"{/if}>{/if}
                                {if $slide.caption}<div style="{$slide.options->displayCaption|escape:'html':'UTF-8'}{$slide.options->captionPosition|escape:'html':'UTF-8'} color:{$slide.options->captionFontColor|escape:'html':'UTF-8'};" class="caption">
                                        <div  class="blayer" style="opacity:{$slide.options->captionOpacity|escape:'html':'UTF-8'};{if $slide.options->captionBackgroundColor|default}background-color:{$slide.options->captionBackgroundColor|escape:'html':'UTF-8'};{/if}"></div>
                                        <div style="{if $slide.options->captionPadding}padding:{$slide.options->captionPadding|escape:'html':'UTF-8'};{/if}" class="caption-case">{html_entity_decode($slide.caption|escape:'html':'UTF-8')}</div>
                                    </div>{/if}
                                    {if $slide.url}</a>{/if}
                            </div>
                        </li>
                        {/foreach}
                        </ul>
                        {if $slider->options->buttons|default}
                            <div class="flexislider-controls">
                                <a href="" class="flexislider-prev">{l s='Prev' mod='flexislider'}</a>
                                <a href="" class="flexislider-next">{l s='Next' mod='flexislider'}</a>
                            </div>
                        {/if}
                    </div>
                    {/if}
                        {/if}