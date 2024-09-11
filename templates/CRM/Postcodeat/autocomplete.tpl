{*-------------------------------------------------------+
| SYSTOPIA - Postcode Lookup for Austria                 |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: M. Wire (mjw@mjwconsult.co.uk)                 |
|         B. Endres (endres@systopia.de)                 |
| http://www.systopia.de/                                |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+-------------------------------------------------------*}

{if $blockId}
{literal}
  <script type="text/javascript">
      cj(function() {
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

          var blockId = {/literal}{$blockId}{literal};
          autocomplete(blockId);

          cj('#address_' + blockId + '_country_id').change(function(e) {
            autocomplete(blockId);
          });

          function autocomplete(blockId) {
            // autocomplete
            var postcode_field = cj('#address_'+blockId+'_postal_code');
            var city_field = cj('#address_'+blockId+'_city');
            var street_field = cj('#address_'+blockId+'_street_address');

            // Init autocomplete
            postcode_field.autocomplete();
            street_field.autocomplete();
            city_field.autocomplete();

            if ((cj('#address_' + blockId + '_country_id').val()) == 1014) {
              postcode_field.autocomplete({
                  source: (request, response) => {
                      postcodeat_find_address_matches({
                          select: ["plznr"],
                          where: {
                              postcode: request.term || postcode_field.val(),
                              city: city_field.val(),
                              street: street_field.val(),
                          },
                      }).then((results) => {
                          response(results.map(result => result.plznr).sort());
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
                  source: (request, response) => {
                      postcodeat_find_address_matches({
                          select: ["gemnam38", "ortnam", "zustort"],
                          where: {
                              postcode: postcode_field.val(),
                              city: request.term || city_field.val(),
                              street: street_field.val(),
                          }
                      }).then((results) => {
                          const uniqueDeliveryAddresses = new Set();
                          const uniqueCityNames = new Set();

                          for (const { gemnam38, ortnam, zustort } of results) {
                              uniqueDeliveryAddresses.add(zustort);
                              uniqueCityNames.add(gemnam38).add(ortnam);
                          }

                          const options_1 =
                              Array.from(uniqueDeliveryAddresses)
                              .sort()
                              .map(value => ({ value, label: `${value} *` }));

                          const options_2 =
                              Array.from(uniqueCityNames.difference(uniqueDeliveryAddresses))
                              .sort()
                              .map(value => ({ value, label: value }));

                          response(options_1.concat(options_2));
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
                  source: (request, response) => {
                      postcodeat_find_address_matches({
                          select: ["stroffi"],
                          where: {
                              postcode: postcode_field.val(),
                              city: city_field.val(),
                              street: request.term || street_field.val(),
                          },
                      }).then((results) => {
                          response(results.map(result => result.stroffi).sort());
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

              postcode_field.autocomplete("enable");
              street_field.autocomplete("enable");
              city_field.autocomplete("enable");

            }
            else {
                // Disable autocomplete
                postcode_field.autocomplete("disable");
                street_field.autocomplete("disable");
                city_field.autocomplete("disable");
            }
          }
      });

  </script>
{/literal}
{/if}
