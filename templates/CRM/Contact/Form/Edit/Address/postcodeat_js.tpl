{if $blockId}
{literal}
  <script type="text/javascript">
      cj(function() {
          postcodeat_init_addressBlock('{/literal}{$blockId}{literal}', 'table#address_table_{/literal}{$blockId}{literal}');
      });
  </script>
{/literal}
{/if}