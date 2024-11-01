<script>
(function($) {
    $(function() {
        $('.tablenav.top, .search-box').hide();
        $('.subsubsub').append($('#attribution').html());
    });
})(jQuery);
</script>

<!--DISABLED
<div id="attribution" class="hidden">
    <li> | <a href="http://zeemgo.com/" target="_blank">Zeemgo Expansion Pack <span class="count">(v<?php echo $this->version; ?>)</span></a></li>
</div>
-->