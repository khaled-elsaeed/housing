$(document).ready(function () {
    // Detect current language from the <html> tag
    const currentLang = $('html').attr('lang') || 'en'; // Default to 'en' if not specified

    $('#governorate_id').on('change', function () {
        let governorateId = $(this).val();
        let citiesSelect = $('#city_id');

        let governoratesCities = cities.filter((city) => {
            return city.governorate_id == governorateId;
        });

        citiesSelect.empty();

        if (governoratesCities.length > 0) {
            governoratesCities.forEach((city) => {
                // Use the correct language field based on the detected language
                let cityName = currentLang === 'ar' ? city.name_ar : city.name_en;
                citiesSelect.append(new Option(cityName, city.id));
            });

            // Remove the 'disabled' attribute to enable the cities dropdown
            citiesSelect.prop('disabled', false);
        } else {
            // Use a localized default message
            let noCitiesMessage = currentLang === 'ar' ? 'لا توجد مدن متاحة' : 'No cities available';
            citiesSelect.append(new Option(noCitiesMessage, '', true, true));

            // Disable the cities dropdown if no cities are available
            citiesSelect.prop('disabled', true);
        }
    });

    $('#faculty_id').on('change', function () {
        let facultyId = $(this).val();
        let programsSelect = $('#program_id');

        let facultysPrograms = programs.filter((program) => {
            return program.faculty_id == facultyId;
        });

        programsSelect.empty();

        if (facultysPrograms.length > 0) {
            facultysPrograms.forEach((program) => {
                // Use the correct language field based on the detected language
                let programName = currentLang === 'ar' ? program.name_ar : program.name_en;
                programsSelect.append(new Option(programName, program.id));
            });

            // Remove the 'disabled' attribute to enable the programs dropdown
            programsSelect.prop('disabled', false);
        } else {
            // Use a localized default message
            let noProgramsMessage = currentLang === 'ar' ? 'لا توجد برامج متاحة' : 'No programs available';
            programsSelect.append(new Option(noProgramsMessage, '', true, true));

            // Disable the programs dropdown if no programs are available
            programsSelect.prop('disabled', true);
        }
    });
});
