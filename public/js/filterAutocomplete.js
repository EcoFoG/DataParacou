/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$('.multiple').on('select2:close', function (e){
            var VernNamesObj = $("#VernName").select2('data');
            var VernNamesSelected = VernNamesObj.map(function (obj) {
                return obj.text;
            });
            var FamiliesObj = $("#Family").select2('data');
            var FamiliesSelected = FamiliesObj.map(function (obj) {
                return obj.text;
            });
            var GenusObj = $("#Genus").select2('data');
            var GenusSelected = GenusObj.map(function (obj) {
                return obj.text;
            });
            var SpeciesObj = $("#Species").select2('data');
            var SpeciesSelected = SpeciesObj.map(function (obj) {
                return obj.text;
            });
            
            $.ajax({
                url: "<?php echo base_url()?>/main/ajax",
                data: {VernNamesSelected : VernNamesSelected, FamiliesSelected : FamiliesSelected, GenusSelected : GenusSelected, SpeciesSelected : SpeciesSelected },
                datatype: 'json',
                async: true
            }).done(function(data){
                // Faire une union entre array données selectionnées et array retour des filtres correspondants
                console.log(VernNamesObj);
                console.log(SpeciesObj);
                data = JSON.parse(data);
                console.log(data.VernName);
                
                var VernNamesObj = $("#VernName").select2('data');
                var mergedVernNames = Object.assign(data.VernName, VernNamesObj);
                var count = 0;
                VernNamesObj.forEach((key,index) => {
                   mergedVernNames.id = count;
                   console.log(key);
                   count++;
                });
                console.log(mergedVernNames);
                var mergedFamily = Object.assign(FamiliesObj, data.Family);
                let mergedGenus = Object.assign(GenusObj, data.Genus);
                let mergedSpecies = Object.assign(SpeciesObj, data.Species);
                
                if (data) {
                    $('#VernName').val(null).trigger('change');
                    $('#VernName').select2({
                        closeOnSelect: false,
                        data : mergedVernNames
                    });
                    $('#Family').val(null).trigger('change');
                    $('#Family').select2({
                        closeOnSelect: false,
                        data : mergedFamily
                    });
                    $('#Genus').val(null).trigger('change');
                    $('#Genus').select2({
                        closeOnSelect: false,
                        data : mergedGenus
                    });
                    $('#Species').val(null).trigger('change');
                    $('#Species').select2({
                        closeOnSelect: false,
                        data : mergedSpecies
                    });
                };
            });  
        });