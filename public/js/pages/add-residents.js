$(document).ready(function () {
    $('#governorate_id').on('change', function () {
        let governorateId = $(this).val();
        let citiesSelect = $('#city_id');

        let governoratesCities = cities.filter((city) => {
            return city.governorate_id == governorateId;
        });

        citiesSelect.empty();
        console.log(governoratesCities);

        if (governoratesCities.length > 0) {
            governoratesCities.forEach((city) => {
                citiesSelect.append(new Option(city.name_en, city.id));
            });
             // Remove the 'disabled' attribute to enable the cities dropdown
             citiesSelect.prop('disabled', false);
            } else {
                // If no cities are found, add a default option
                citiesSelect.append(new Option('No cities available', '', true, true));
    
                // Disable the cities dropdown if no cities are available
                citiesSelect.prop('disabled', true);
            }
    });

    $('#faculty_id').on('change', function () {
        let facultyId = $(this).val();
        let programsSelect = $('#program_id');

        let facultysCities = programs.filter((program) => {
            return program.faculty_id == facultyId;
        });

        programsSelect.empty();
        console.log(facultysCities);

        if (facultysCities.length > 0) {
            facultysCities.forEach((program) => {
                programsSelect.append(new Option(program.name_en, program.id));
            });
             // Remove the 'disabled' attribute to enable the programs dropdown
             programsSelect.prop('disabled', false);
            } else {
                // If no programs are found, add a default option
                programsSelect.append(new Option('No programs available', '', true, true));
    
                // Disable the programs dropdown if no programs are available
                programsSelect.prop('disabled', true);
            }
    });
});
