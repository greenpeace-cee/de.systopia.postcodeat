{literal}
<script type="text/javascript">
    function insert_row_{/literal}{$blockId}{literal}() {
        postcodeat_init_addressBlock('{/literal}{$blockId}{literal}', '#address_table_{/literal}{$blockId}{literal}');
    }

    cj(function(e) {
        insert_row_{/literal}{$blockId}{literal}();
    });

    cj(function() {
      var blockId = {/literal}{$blockId}{literal};
      var postcode_field = cj('#postcodeat_postcode_' + blockId);
      var city_field = cj('#address_'+blockId+'_city');
      var street_field = cj('#address_'+blockId+'_street_address');

      postcode_field.autocomplete({
        source: function( request, response ) {
          cj.ajax( {
            url: CRM.url('civicrm/ajax/postcodeat/autocomplete'),
            dataType: "json",
            data: {
              term : request.term,
              plznr: cj('#postcodeat_postcode_' + blockId).val(),
              ortnam: cj('#address_'+blockId+'_city').val(),
              stroffi: cj('#address_'+blockId+'_street_address').val(),
              return: 'plznr'
            },
            success: function( data ) {
              response( data );
            },
            width: 280,
            selectFirst: true,
            matchContains: true,
            minLength: 2
          });
        }
      });

      city_field.autocomplete({
        source: function( request, response ) {
          cj.ajax( {
            url: CRM.url('civicrm/ajax/postcodeat/autocomplete'),
            dataType: "json",
            data: {
              term : request.term,
              plznr: cj('#postcodeat_postcode_' + blockId).val(),
              ortnam: cj('#address_'+blockId+'_city').val(),
              stroffi: cj('#address_'+blockId+'_street_address').val(),
              return: 'ortnam'
            },
            success: function( data ) {
              response( data );
            },
            width: 280,
            selectFirst: true,
            matchContains: true
          });
        }
      });

      street_field.autocomplete({
        source: function( request, response ) {
          cj.ajax( {
            url: CRM.url('civicrm/ajax/postcodeat/autocomplete'),
            dataType: "json",
            data: {
              term : request.term,
              plznr: cj('#postcodeat_postcode_' + blockId).val(),
              ortnam: cj('#address_'+blockId+'_city').val(),
              stroffi: cj('#address_'+blockId+'_street_address').val(),
              return: 'stroffi'
            },
            success: function( data ) {
              response( data );
            },
            width: 280,
            selectFirst: true,
            matchContains: true
          });
        }
      });
    });

</script>
{/literal}