<% if $AttributeControls %>

$GridFieldAction

<div id="advanced-content-attribute-controls" class="outer hide">
    <div class="inner">
    
        <ul class="attribute-ui-controls">
        <% loop $AttributeControls %>
            <li data-action-href="$Up.ActionLink">$Field()</li>
        <% end_loop %>
        </ul>
    
    </div>
</div>
<% end_if %>
