"use strict";

$(document).ready(function() {

  // Define translations for English and Arabic
  const translations = {
    en: {
      building: "Building",
      occupied: "Occupied",
      total_bedrooms: "Total Bedrooms",
      empty: "Empty",
      bedrooms_occupied: "Bedrooms Occupied"
    },
    ar: {
      building: "مبنى",
      occupied: "مشغول",
      total_bedrooms: "إجمالي الغرف",
      empty: "فارغ",
      bedrooms_occupied: "الغرف المشغولة"
    }
  };

  // Get the current language from the HTML lang attribute
  const currentLang = $('html').attr('lang') || 'en';  // Default to 'en' if lang is not set

  // Select the translation object based on the current language
  const translation = translations[currentLang];

  // Fetch and display the summary data
  function fetchSummaryData() {
    $.ajax({
      url: window.routes.fetchStats,  
      method: 'GET',
      dataType: 'json',
      success: function(data) {
        updateSummaryData(data);  
        populateBuildings(data.buildings);  
      },
      error: function(xhr, status, error) {
        console.error("Error fetching summary data: ", error);
      }
    });
  }

  // Update summary statistics
  function updateSummaryData(data) {
    if (data) {
      $('#totalStudentsCount').text(data.total_students);
      $('#totalMaleStudentsCount').text(data.male_students);
      $('#totalFemaleStudentsCount').text(data.female_students);
      $('#occupancyRate').text(data.occupancy_rate);
      $('#lastUpdateStudents').text(data.last_updated_room);
      $('#lastUpdateMaleStudents').text(data.last_create_student);
      $('#lastUpdateFemaleStudents').text(data.last_created_female_student);
      $('#lastUpdateOccupancyRate').text(data.last_updated_room);
    }
  }

  // Generate the building cards dynamically
  function populateBuildings(buildings) {
    const buildingSlider = $('#buildingSlider');
    buildingSlider.empty();  

    buildings.forEach(building => {
      const occupancyPercentage = calculateOccupancyPercentage(building);
      const buildingItemHTML = generateBuildingItemHTML(building, occupancyPercentage);
      buildingSlider.append(buildingItemHTML);  
    });

    initializeSlickSlider();  // Initialize the slider once content is added
  }

  // Calculate occupancy percentage
  function calculateOccupancyPercentage(building) {
    return building.total > 0 ? Math.round((building.occupied / building.total) * 100) : 0;
  }

  // Generate HTML for a building item
  function generateBuildingItemHTML(building, occupancyPercentage) {
    return `
      <div class="building-slider-item">
        <h4 class="my-0">${translation.building} ${building.name}</h4>
        <div class="row align-items-center my-4 py-3">
          <div class="col-4 p-0">
            <h4>${building.occupied}</h4>
            <p class="mb-0">${translation.occupied}</p>
          </div>
          <div class="col-4 py-3 px-0 bg-primary-rgba rounded">
            <h4 class="text-primary">${building.total}</h4>
            <p class="text-primary mb-0">${translation.total_bedrooms}</p>
          </div>
          <div class="col-4 p-0">
            <h4>${building.empty}</h4>
            <p class="mb-0">${translation.empty}</p>
          </div>
        </div>
        <div class="progress mb-2 mt-2" style="height: 5px;">
          <div class="progress-bar" role="progressbar" style="width: ${occupancyPercentage}%" aria-valuenow="${occupancyPercentage}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div class="row align-items-center">
          <div class="col-6 text-start">
            <p class="font-13">${occupancyPercentage}% ${translation.occupied}</p>
          </div>
          <div class="col-6 text-end">
            <p class="font-13">${building.occupied}/${building.total} ${translation.bedrooms_occupied}</p>
          </div>
        </div>
      </div>
    `;
  }

  // Initialize the Slick slider for the building items
  function initializeSlickSlider() {
    var isRtl = $('html').attr('dir') === 'rtl';  
    $('.building-slider').slick({
      arrows: true,
      dots: false,
      infinite: true,
      adaptiveHeight: true,
      slidesToShow: 1,
      slidesToScroll: 1,
      prevArrow: '<i class="feather icon-arrow-left"></i>',
      nextArrow: '<i class="feather icon-arrow-right"></i>',
      autoplay: true,                
      autoplaySpeed: 3000,
      rtl: isRtl, 
    });
  }

  fetchSummaryData();
});
