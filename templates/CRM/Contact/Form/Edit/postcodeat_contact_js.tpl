{if $blockId}
{literal}
<script type="text/javascript">
function init_postcodeat_contact_form() {
    var addressBlocks = cj('.crm-edit-address-block');
    addressBlocks.each(function(index, item) {
        var block = cj(item).attr('id').replace('Address_Block_', '');
        var addressTableId = 'table#address_'+block; //name of address table if 4.4
        //the id of the address table is renamed in 4.5
        //so also check the address table id for version 4.5
        if (cj(addressTableId).length <= 0) {
            addressTableId = 'table#address_table_'+block;
        }
        postcodeat_init_addressBlock(block, addressTableId);
    });
}

function reset_postcodeat_contact_form() {
    postcodeat_reset();
}

cj(function() {
    reset_postcodeat_contact_form();
    init_postcodeat_contact_form();
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
              }
        });
      },
        width: 280,
        selectFirst: true,
        matchContains: true,
        minLength: 0
    })
    .focus(function() {
      cj(this).autocomplete("search", "");
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
            }
        });
      },
        width: 280,
        selectFirst: true,
        matchContains: true,
        minLength: 0
    })
    .focus(function() {
      cj(this).autocomplete("search", "");
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
                }
            });
        },
        width: 280,
        selectFirst: true,
        matchContains: true,
        minLength: 0
    })
    .focus(function() {
      cj(this).autocomplete("search", "");
    });
});

</script>
{/literal}
{/if}