{if $blockId}
{literal}
  <script type="text/javascript">
      cj(function(){console.log('message from ajax-loaded page!!')});
      cj(function() {
          postcodeat_init_addressBlock('{/literal}{$blockId}{literal}', 'table#address_table_{/literal}{$blockId}{literal}');
      });
  </script>
{/literal}
{/if}