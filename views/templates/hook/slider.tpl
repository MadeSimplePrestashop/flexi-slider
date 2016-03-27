{counter assign='flexicounter'}
{if $slider} 
    <script type="text/javascript">
        var flexislider;
                $(document).ready(function () {
        $('.flexislider{$flexicounter|intval}').moodular({
        effects: '{if $slider->options->effect|default == 'carousel'}{$slider->options->move|default}{else}{$slider->options->effect|default}{/if}',
                controls: '{if $slider->options->keys|default}keys{/if} {if $slider->options->buttons|default}buttons{/if} {if $slider->options->touch|default}touch{/if} {if $slider->options->pagination|default}pagination{/if} {if $slider->options->startOnMouseOver|default}startOnMouseOver{/if} {if $slider->options->stopOnMouseOver|default}stopOnMouseOver{/if}',
                            easing: '{$slider->options->easing|default}',
                            step: {$slider->options->step|default:1},
                            selector: 'li',
                            timer: {$slider->options->timer|default:0},
                            speed: {$slider->options->speed|default:500},
                            queue: true,
                            keyPrev: 37,
                            keyNext: 39,
        {if $slider->options->effect|default == 'mosaic'}
            {if $slider->options->slicesx|default && $slider->options->slicesy|default}slices: [{$slider->options->slicesx|default:0}, {$slider->options->slicesy|default:0}],{/if}
            {if $slider->options->mode|default}mode : '{$slider->options->mode}',{/if}
        {/if}
        {if $slider->options->effect|default == 'stripes'}
            {if $slider->options->stripes|default}stripes : {$slider->options->stripes},{/if}
            {if $slider->options->orientation|default}orientation : '{$slider->options->orientation}',{/if}
        {/if}
        {if $slider->options->effect|default == 'slide'}
            {if $slider->options->direction|default}direction : '{$slider->options->direction}',{/if}
        {/if}
        {if $slider->options->effect|default == 'carousel'}
            {if $slider->options->view|default}view : {$slider->options->view},{/if}
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
                        <div class="case" style="{$slide.options->size|escape:'UTF-8'}{$slide.options->imagePosition|escape:'UTF-8'}background-image: url('{$link->getMediaLink($slide.image_helper.dir|cat:$slide.image)|escape:'htmlall':'UTF-8'}'); {if $slide.options->backgroundColor}background-color:{$slide.options->backgroundColor|escape:'UTF-8'}{/if}">
                            {if $slide.url}<a href="{$slide.url|escape:'html':'UTF-8'}" {if $slide.target}target="{$slide.target|escape:'html':'UTF-8'}"{/if}>{/if}
                                {if $slide.caption}<div style="{$slide.options->displayCaption|escape:'UTF-8'}{$slide.options->captionPosition|escape:'UTF-8'} color:{$slide.options->captionFontColor|escape:'UTF-8'};" class="caption">
                                        <div  class="blayer" style="opacity:{$slide.options->captionOpacity|escape:'UTF-8'};{if $slide.options->captionBackgroundColor|default}background-color:{$slide.options->captionBackgroundColor|escape:'UTF-8'};{/if}"></div>
                                        <div style="{if $slide.options->captionPadding}padding:{$slide.options->captionPadding|escape:'UTF-8'};{/if}" class="caption-case">{$slide.caption|escape:'UTF-8'}</div>
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