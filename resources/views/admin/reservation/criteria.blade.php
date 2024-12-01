@extends('layouts.admin')

@section('title', 'Criteria Management')

@section('links')
    

    <style>
        .loading {
            pointer-events: none;
        }
        .criteria-card {
            transition: transform 0.2s;
            height: 200px;
        }
        .criteria-card:hover {
            transform: scale(1.05);
        }
        .card-title {
            font-size: 1.25rem;
        }
        .card-text {
            color: #555;
            font-size: 0.9rem;
        }
        .criteria-icon {
            font-size: 1.5rem;
            margin-right: 10px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        @foreach($fields as $field)
            <div class="col-md-6 col-lg-4">
                <div class="card m-b-30 criteria-card">
                    <div class="card-header bg-primary d-flex align-items-center">
                        <i class="feather icon-settings criteria-icon text-white"></i>
                        <h5 class="card-title text-white">{{ $field->name }}</h5>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title font-18">Criteria</h5>
                        <a href="#" class="btn btn-primary btn-sm btn-view-details" data-field-id="{{ $field->id }}">
                            <i class="feather icon-eye"></i> View Details
                        </a>
                    </div>
                    <div class="card-footer bg-primary-rgba text-primary">
                        Last updated: {{ $field->updated_at->diffForHumans() }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

<!-- Modal -->
<div class="modal fade" id="exampleStandardModal" tabindex="-1" role="dialog" aria-labelledby="exampleStandardModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleStandardModalLabel">Modal title</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                <span aria-hidden="true"></span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
    <ul id="criteriaList" class="list-group"></ul>
</div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary">Save changes</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
@endsection

@section('scripts')
    
    <script>
        // Mock data for criteria
        const mockCriteriaData = {
            1: [
                { id: 101, criteria: "Condition A", weight: "High", type: "Type 1" },
                { id: 102, criteria: "Condition B", weight: "Medium", type: "Type 2" },
            ],
            2: [
                { id: 201, criteria: "Condition C", weight: "Low", type: "Type 3" },
                { id: 202, criteria: "Condition D", weight: "High", type: "Type 1" },
            ],
            // Additional mock data entries as needed
        };

        // Event listener for "View Details" button
        document.querySelectorAll('.btn-view-details').forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                let fieldId = this.getAttribute('data-field-id');

                // Simulate AJAX fetch with mock data
                let data = mockCriteriaData[fieldId] || []; // Use empty array if no data for the field ID
                let criteriaList = document.getElementById('criteriaList');
                criteriaList.innerHTML = ''; // Clear existing criteria

                // Loop through criteria and add to modal
                data.forEach(criteria => {
                    let listItem = document.createElement('li');
                    listItem.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
                    
                    listItem.innerHTML = `
                        <div>
                            <strong>Condition:</strong> ${criteria.criteria} <br>
                            <strong>Weight:</strong> ${criteria.weight} <br>
                            <strong>Type:</strong> ${criteria.type}
                        </div>
                        <div>
                            <button class="btn btn-sm btn-warning btn-edit" data-id="${criteria.id}">Edit</button>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${criteria.id}">Delete</button>
                        </div>
                    `;
                    criteriaList.appendChild(listItem);
                });

                // Show modal
                $('#exampleStandardModal').modal('show');

            });
        });

        // Event listener for Edit and Delete buttons
        document.getElementById('criteriaList').addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-edit')) {
                let criteriaId = e.target.getAttribute('data-id');
                // Handle edit logic here
                alert(`Edit criteria ID: ${criteriaId}`);
            }
            else if (e.target.classList.contains('btn-delete')) {
                let criteriaId = e.target.getAttribute('data-id');
                // Handle delete logic here
                alert(`Delete criteria ID: ${criteriaId}`);
            }
        });
    </script>
@endsection
