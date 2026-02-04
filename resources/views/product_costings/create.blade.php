@extends('layouts.app')

@section('content')
<!-- Include Select2 CSS and JS from CDN -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<style type="text/css">
    .new_table_a table th, .new_table_a table td {
        min-width: 200px;
    }

    .new_table_a {
        overflow-x: scroll;
    }
    .d-flex-btn{
    justify-content: space-between;
    display: flex;
    align-items: center;
    margin-top: 30px;
}
    .pt-20{
        padding-top: 20px;
    }
</style>

 
<section class="content no-print">
    <div class="box box-solid">
        <div class="box-body">
            <div class="row">
                <h1 style="color:white;">Add New Product Costing</h1>
                <form action="{{ route('product-costings.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row" style="background:white;margin:5px;padding:5px;">
                        <div class="col-lg-6 pt-2">
                            <label for="date">Date</label>
                            <input type="date" name="date" class="form-control" required>
                        </div>
                        <div class="col-lg-6 pt-2">
                            <label for="container_number">Container Number</label>
                            <input type="text" name="container_number" placeholder="Container Number" class="form-control" required>
                        </div>
                        <div class="col-lg-6 pt-2">
                            <label for="bn_number">BN Number</label>
                            <input type="text" name="bn_number" placeholder="BN Number" class="form-control" required>
                        </div>
                        
                        <div class="col-lg-6 pt-2">
                            <label for="location">Location</label>
                            <select name="location" class="form-control select2" required>
                                <option readonly disabled selected>Select Location</option>
                                @if($locations)
                                    @foreach($locations as $location)
                                    <option value="{{ $location->name }}">{{ $location->name }}</option>
                                    @endforeach
                                @else
                                    <option readonly disabled>No Location found</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="new_table_a" style="background:white;margin:5px;padding:5px;">
                        <table class="table table-bordered" id="dynamic-fields-table">
                            <thead>
                                <tr>
                                    @foreach(json_decode($last_inserted_column->column_names) as $each_column)

                                    <th><input type="text" name="column_names[]" value="{{$each_column}}" class="form-control" required></th>

                                    @endforeach


                                <!--     <th><input type="text" name="column_names[]" value="Product" class="form-control" required></th>
                                    <th><input type="text" name="column_names[]" value="Product image" class="form-control" required></th>
                                    <th><input type="text" name="column_names[]" value="Box in Container" class="form-control" required></th>
                                    <th><input type="text" name="column_names[]" value="SQM in Container" class="form-control" required></th>
                                    <th><input type="text" name="column_names[]" value="SQM in Box" class="form-control" required></th>
                                    <th><input type="text" name="column_names[]" value="Price per SQM" class="form-control" required></th>
                                    <th><input type="text" name="column_names[]" value="Expense 1" class="form-control" required></th>
                                    <th><input type="text" name="column_names[]" value="Expense 2" class="form-control" required></th>
                                    <th><input type="text" name="column_names[]" value="Expense 3" class="form-control" required></th>
                                    <th><input type="text" name="column_names[]" value="Expense 4" class="form-control" required></th>
                                    <th><input type="text" name="column_names[]" value="Expense 5" class="form-control" required></th>
                                    <th><input type="text" name="column_names[]" value="Expense 6" class="form-control" required></th>
                                    <th><input type="text" name="column_names[]" value="Expense 7" class="form-control" required></th>
                                    <th><input type="text" name="column_names[]" value="Expense 8" class="form-control" required></th>
                                    <th><input type="text" name="column_names[]" value="Expense 9" class="form-control" required></th>
                                    <th><input type="text" name="column_names[]" value="Total Expenses" class="form-control" required></th>
                                    <th><input type="text" name="column_names[]" value="Final Price" class="form-control" required></th>
                                    <th><input type="text" name="column_names[]" value="Total Final Expense" class="form-control" required></th>
                                    <th><input type="text" name="column_names[]" value="Final Costing Price" class="form-control" required></th> -->
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="dynamic-field" data-index="0">
                                    <td>
                                        <select name="product_id[]" class="form-control select2 product-select" required>
                                            <option readonly selected value="">Select Product</option>
                                            @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-image="{{ url('uploads/img/' . $product->image) }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <img src="{{url('images/no-image-placeholder.png')}}" class='product_img' width="50">
                                    </td>
                                    <td><input type="number" name="box_in_container[]" class="form-control box_in_container" required></td>
                                    <td><input type="number" name="sqm_in_container[]" class="form-control sqm_in_container" step="0.01" required readonly></td>
                                    <td><input type="number" name="sqm_in_box[]" class="form-control sqm_in_box" step="0.01" required></td>
                                    <td><input type="number" name="price_per_sqm[]" class="form-control price_per_sqm" step="0.01" required></td>
                                    <td><input type="number" name="exp1[]" class="form-control expense" step="0.01"></td>
                                    <td><input type="number" name="exp2[]" class="form-control expense" step="0.01"></td>
                                    <td><input type="number" name="exp3[]" class="form-control expense" step="0.01"></td>
                                    <td><input type="number" name="exp4[]" class="form-control expense" step="0.01"></td>
                                    <td><input type="number" name="exp5[]" class="form-control expense" step="0.01"></td>
                                    <td><input type="number" name="exp6[]" class="form-control expense" step="0.01"></td>
                                    <td><input type="number" name="exp7[]" class="form-control expense" step="0.01"></td>
                                    <td><input type="number" name="exp8[]" class="form-control expense" step="0.01"></td>
                                    <td><input type="number" name="exp9[]" class="form-control expense" step="0.01"></td>
                                    <td><input type="number" name="total_exp[]" class="form-control total_exp" readonly step="0.01"></td>
                                    <td><input type="number" name="final_price[]" class="form-control final_price" step="0.01" readonly></td>
                                    <td><input type="number" name="total_final_exp[]" class="form-control total_final_exp" step="0.01" readonly></td>
                                    <td><input type="number" name="final_costing_price[]" class="form-control final_costing_price" step="0.01" readonly></td>
                                    <td><button type="button" class="btn btn-danger remove-field" onclick="removeField(this)">Remove</button></td>
                                </tr>
                            </tbody>
                        </table>    
                    </div>
                    
                    <div class="d-flex-btn">
                        <button type="button" class="btn btn-warning" id="add-more">Add More+</button>
                        <button type="submit" class="btn btn-info">Save</button><span></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

 

<!-- Hidden template for new rows -->
<template id="dynamic-field-template">
    <tr class="dynamic-field">
        <td>
            <select name="product_id[]" class="form-control select2 product-select" required>
                <option readonly selected value="">Select Product</option>
                @foreach($products as $product)
                <option value="{{ $product->id }}" data-image="{{ url('uploads/img/' . $product->image) }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <img src="{{url('images/no-image-placeholder.png')}}" class='product_img' width="50">
        </td>
        <td><input type="number" name="box_in_container[]" class="form-control box_in_container" required></td>
        <td><input type="number" name="sqm_in_container[]" class="form-control sqm_in_container" step="0.01" required readonly></td>
        <td><input type="number" name="sqm_in_box[]" class="form-control sqm_in_box" step="0.01" required></td>
        <td><input type="number" name="price_per_sqm[]" class="form-control price_per_sqm" step="0.01" required></td>
        <td><input type="number" name="exp1[]" class="form-control expense" step="0.01"></td>
        <td><input type="number" name="exp2[]" class="form-control expense" step="0.01"></td>
        <td><input type="number" name="exp3[]" class="form-control expense" step="0.01"></td>
        <td><input type="number" name="exp4[]" class="form-control expense" step="0.01"></td>
        <td><input type="number" name="exp5[]" class="form-control expense" step="0.01"></td>
        <td><input type="number" name="exp6[]" class="form-control expense" step="0.01"></td>
        <td><input type="number" name="exp7[]" class="form-control expense" step="0.01"></td>
        <td><input type="number" name="exp8[]" class="form-control expense" step="0.01"></td>
        <td><input type="number" name="exp9[]" class="form-control expense" step="0.01"></td>
        <td><input type="number" name="total_exp[]" class="form-control total_exp" readonly step="0.01"></td>
        <td><input type="number" name="final_price[]" class="form-control final_price" step="0.01" readonly></td>
        <td><input type="number" name="total_final_exp[]" class="form-control total_final_exp" step="0.01" readonly></td>
        <td><input type="number" name="final_costing_price[]" class="form-control final_costing_price" step="0.01" readonly></td>
        <td><button type="button" class="btn btn-danger remove-field" onclick="removeField(this)">Remove</button></td>
    </tr>
</template>



<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script>
    window.onload = function() {

        let index = 1;

        // Function to initialize Select2
        function initializeSelect2() {
            $('.select2').select2();
        }

        // Function to calculate total expense
        function calculateTotalExpense(row) {
            let totalExp = 0;
            row.find('.expense').each(function() {
                let value = parseFloat($(this).val());
                if (!isNaN(value)) {
                    totalExp += value;
                }
            });
            row.find('.total_exp').val(totalExp.toFixed(2));
        }

        // Function to calculate the derived fields
        function calculateDerivedFields(row) {
            let boxInContainer = parseFloat(row.find('.box_in_container').val()) || 0;
            let sqmInBox = parseFloat(row.find('.sqm_in_box').val()) || 0;
            let sqmInContainer = boxInContainer * sqmInBox;
            row.find('.sqm_in_container').val(sqmInContainer.toFixed(2));

            let pricePerSqm = parseFloat(row.find('.price_per_sqm').val()) || 0;
            let finalPrice = sqmInContainer * pricePerSqm;
            row.find('.final_price').val(finalPrice.toFixed(2));

            let totalExp = parseFloat(row.find('.total_exp').val()) || 0;
            let totalFinalExpense = totalExp + finalPrice;
            row.find('.total_final_exp').val(totalFinalExpense.toFixed(2));

            let finalCostingPrice = totalFinalExpense !== 0 ? sqmInContainer / totalFinalExpense : 0;
            row.find('.final_costing_price').val(finalCostingPrice.toFixed(2));
        }

        // Event listener for adding a new row
        $('#add-more').click(function() {
            const template = document.getElementById('dynamic-field-template').content.cloneNode(true);
            const newRow = $(template).find('tr');

            // Append new row to the table body
            $('#dynamic-fields-table tbody').append(newRow);

            // Initialize select2 for the new row
            initializeSelect2();

            // Add event listeners for the new row's expense fields
            newRow.find('.expense').on('input', function() {
                calculateTotalExpense(newRow);
                calculateDerivedFields(newRow);
            });

            // Add event listeners for the new row's other fields
            newRow.find('.box_in_container, .sqm_in_box, .price_per_sqm').on('input', function() {
                calculateDerivedFields(newRow);
            });

             // Initialize event listener for product selection to update image
            newRow.find('.product-select').on('change', function() {
                updateProductImage($(this));
            });
        });

        // Event listener for removing a field
        $(document).on('click', '.remove-field', function() {
            $(this).closest('tr').remove();
        });

        // Initialize event listeners for existing expense fields
        $('.dynamic-field .expense').on('input', function() {
            calculateTotalExpense($(this).closest('tr'));
            calculateDerivedFields($(this).closest('tr'));
        });

        // Initialize event listeners for existing other fields
        $('.dynamic-field .box_in_container, .dynamic-field .sqm_in_box, .dynamic-field .price_per_sqm').on('input', function() {
            calculateDerivedFields($(this).closest('tr'));
        });

        // Initialize select2 for existing fields
        initializeSelect2();

        // Function to update the product image
        function updateProductImage(select) {
            let imageUrl = select.find(':selected').data('image');
            select.closest('tr').find('.product_img').attr('src', imageUrl);
        }

        $('.dynamic-field .product-select').on('change', function() {
            updateProductImage($(this));
        });

    };
</script>

@endsection
