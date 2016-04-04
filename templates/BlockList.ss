<% if $Blocks() %>
<div id="advanced-content-block-list">
    <% loop $Blocks() %>
        <div class="block-outer">
            <div class="block-inner $ClassName">
                $BlockView()
            </div>
        </div>
    <% end_loop %>
</div>
<% end_if %>