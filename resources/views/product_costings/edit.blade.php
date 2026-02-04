@extends('layouts.app')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<style type="text/css">
    table {
        border: 1px solid grey;
        overflow-x: auto;
        display: block;
    }
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
    .product-images img {
        max-width: 100px;
        margin-right: 10px;
    }
</style>

 
<section class="content no-print">
    <div class="box box-solid">
        <div class="box-body">
    <h1>Edit Product Costing</h1>
    <form action="{{ route('product-costings.update', $productCosting->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" name="date" class="form-control" value="{{ $productCosting->date }}" required>
        </div>
        <div class="form-group">
            <label for="container_number">Container Number</label>
            <input type="text" name="container_number" class="form-control" value="{{ $productCosting->container_number }}" required>
        </div>
        <div class="form-group">
            <label for="bn_number">BN Number</label>
            <input type="text" name="bn_number" class="form-control" value="{{ $productCosting->bn_number }}" required>
        </div>
        <div class="form-group">
            <label for="location">Location</label>
            <select name="location" class="form-control" required>
                <option readonly disabled>Select Location</option>
                @foreach($locations as $location)
                    <option value="{{ $location->name }}" {{ $location->name == $productCosting->location ? 'selected' : '' }}>{{ $location->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="new_table_a">
            <table class="table table-bordered" id="dynamic-fields-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Product image</th>
                        <th>Box in Container</th>
                        <th>SQM in Container</th>
                        <th>SQM in Box</th>
                        <th>Price per SQM</th>
                        <th>Expense 1</th>
                        <th>Expense 2</th>
                        <th>Expense 3</th>
                        <th>Expense 4</th>
                        <th>Expense 5</th>
                        <th>Expense 6</th>
                        <th>Expense 7</th>
                        <th>Expense 8</th>
                        <th>Expense 9</th>
                        <th>Total Expenses</th>
                        <th>Final Price</th>
                        <th>Total Final Expense</th>
                        <th>Final Costing Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(json_decode($productCosting->product_id) as $index => $detail)
                    <tr class="dynamic-field" data-index="{{ $index }}">
                        <td>
                            <select name="product_id[]" class="form-control select2 product-select" required>
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}"  data-image="{{ url('uploads/img/' . $product->image) }}" {{ $product->id == json_decode($productCosting->product_id)[$index] ? 'selected' : '' }}>{{ $product->name }}</option>

                                        @if($product->id == json_decode($productCosting->product_id)[$index]):
                                          @php $p_image = $product->image;  @endphp
                                        @endif

                                @endforeach
                            </select>
                        </td>
                        <td>
                            <img src="{{ url('uploads/img/' . $products->firstWhere('id', json_decode($productCosting->product_id)[$index])->image) }}" class='product_img' width="50">
                        </td>
                        <td><input type="number" name="box_in_container[]" class="form-control box_in_container" value="{{ json_decode($productCosting->box_in_container)[$index] }}" required></td>
                        <td><input type="number" name="sqm_in_container[]" class="form-control sqm_in_container" step="0.01" value="{{ json_decode($productCosting->sqm_in_container)[$index] }}" required readonly></td>
                        <td><input type="number" name="sqm_in_box[]" class="form-control sqm_in_box" step="0.01" value="{{ json_decode($productCosting->sqm_in_box)[$index] }}" required></td>
                        <td><input type="number" name="price_per_sqm[]" class="form-control price_per_sqm" step="0.01" value="{{ json_decode($productCosting->price_per_sqm)[$index] }}" required></td>
                        <td><input type="number" name="exp1[]" class="form-control expense" step="0.01" value="{{ json_decode($productCosting->exp1)[$index] }}"></td>
                        <td><input type="number" name="exp2[]" class="form-control expense" step="0.01" value="{{ json_decode($productCosting->exp2)[$index] }}"></td>
                        <td><input type="number" name="exp3[]" class="form-control expense" step="0.01" value="{{ json_decode($productCosting->exp3)[$index] }}"></td>
                        <td><input type="number" name="exp4[]" class="form-control expense" step="0.01" value="{{ json_decode($productCosting->exp4)[$index] }}"></td>
                        <td><input type="number" name="exp5[]" class="form-control expense" step="0.01" value="{{ json_decode($productCosting->exp5)[$index] }}"></td>
                        <td><input type="number" name="exp6[]" class="form-control expense" step="0.01" value="{{ json_decode($productCosting->exp6)[$index] }}"></td>
                        <td><input type="number" name="exp7[]" class="form-control expense" step="0.01" value="{{ json_decode($productCosting->exp7)[$index] }}"></td>
                        <td><input type="number" name="exp8[]" class="form-control expense" step="0.01" value="{{ json_decode($productCosting->exp8)[$index] }}"></td>
                        <td><input type="number" name="exp9[]" class="form-control expense" step="0.01" value="{{ json_decode($productCosting->exp9)[$index] }}"></td>
                        <td><input type="number" name="total_exp[]" class="form-control total_exp" readonly step="0.01" value="{{ json_decode($productCosting->total_exp)[$index] }}"></td>
                        <td><input type="number" name="final_price[]" class="form-control final_price" step="0.01" value="{{ json_decode($productCosting->final_price)[$index] }}"></td>
                        <td><input type="number" name="total_final_exp[]" class="form-control total_final_exp" step="0.01" value="{{ json_decode($productCosting->total_final_exp)[$index] }}"></td>
                        <td><input type="number" name="final_costing_price[]" class="form-control final_costing_price" step="0.01" value="{{ json_decode($productCosting->final_costing_price)[$index] }}"></td>
                        <td><button type="button" class="btn btn-danger remove-field" onclick="removeField(this)">Remove</button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <button type="button" class="btn btn-info" id="add-more">Add More</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let index = {{ count(json_decode($productCosting->product_id)) }};

        // Function to calculate total expense
        function calculateTotalExpense(row) {
            let totalExp = 0;
            row.querySelectorAll('.expense').forEach(function(expense) {
                let value = parseFloat(expense.value);
                if (!isNaN(value)) {
                    totalExp += value;
                }
            });
            row.querySelector('.total_exp').value = totalExp.toFixed(2);
        }

        // Function to calculate derived fields
        function calculateDerivedFields(row) {
            let boxInContainer = parseFloat(row.querySelector('.box_in_container').value);
            let sqmInBox = parseFloat(row.querySelector('.sqm_in_box').value);
            let sqmInContainer = boxInContainer * sqmInBox;
            row.querySelector('.sqm_in_container').value = sqmInContainer.toFixed(2);

            let pricePerSqm = parseFloat(row.querySelector('.price_per_sqm').value);
            let finalPrice = sqmInContainer * pricePerSqm;
            row.querySelector('.final_price').value = finalPrice.toFixed(2);

            let totalExp = parseFloat(row.querySelector('.total_exp').value);
            let totalFinalExpense = totalExp + finalPrice;
            row.querySelector('.total_final_exp').value = totalFinalExpense.toFixed(2);

            let finalCostingPrice = sqmInContainer / totalFinalExpense;
            row.querySelector('.final_costing_price').value = finalCostingPrice.toFixed(2);
        }

        // Event listener for adding a new row
        document.getElementById('add-more').addEventListener('click', function () {
            const tableBody = document.getElementById('dynamic-fields-table').getElementsByTagName('tbody')[0];
            const newRow = document.createElement('tr');
            newRow.className = 'dynamic-field';
            newRow.dataset.index = index;

            newRow.innerHTML = `
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
                <td><input type="number" name="sqm_in_container[]" class="form-control sqm_in_container" step="0.01" readonly required></td>
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
                <td><input type="number" name="total_exp[]" class="form-control total_exp" readonly step="0.01" ></td>
                <td><input type="number" name="final_price[]" class="form-control final_price" step="0.01" ></td>
                <td><input type="number" name="total_final_exp[]" class="form-control total_final_exp" step="0.01" ></td>
                <td><input type="number" name="final_costing_price[]" class="form-control final_costing_price" step="0.01" ></td>
                <td><button type="button" class="btn btn-danger remove-field" onclick="removeField(this)">Remove</button></td>
            `;
            tableBody.appendChild(newRow);
            index++;

            // Initialize select2 for the new row
            $(newRow).find('.select2').select2();

            // Add event listeners for the new row's expense fields
            newRow.querySelectorAll('.expense').forEach(function(field) {
                field.addEventListener('input', function() {
                    calculateTotalExpense(newRow);
                    calculateDerivedFields(newRow);
                });
            });

            // Add event listeners for the new row's other fields
            newRow.querySelectorAll('.box_in_container, .sqm_in_box, .price_per_sqm').forEach(function(field) {
                field.addEventListener('input', function() {
                    calculateDerivedFields(newRow);
                });
            });

            // Initialize event listener for product selection to update image
            newRow.find('.product-select').on('change', function() {
                updateProductImage($(this));
            });
        });

        // Event listener for removing a field
        window.removeField = function(button) {
            button.closest('tr').remove();
        };

        // Initialize event listeners for existing expense fields
        document.querySelectorAll('.dynamic-field').forEach(function(row) {
            row.querySelectorAll('.expense').forEach(function(field) {
                field.addEventListener('input', function() {
                    calculateTotalExpense(row);
                    calculateDerivedFields(row);
                });
            });

            // Initialize event listeners for existing other fields
            row.querySelectorAll('.box_in_container, .sqm_in_box, .price_per_sqm').forEach(function(field) {
                field.addEventListener('input', function() {
                    calculateDerivedFields(row);
                });
            });
        });

        // Initialize select2 for existing fields
        $('.select2').select2();



        // Function to update the product image
        function updateProductImage(select) {
            let imageUrl = select.find(':selected').data('image');
            select.closest('tr').find('.product_img').attr('src', imageUrl);
        }

        $('.dynamic-field .product-select').on('change', function() {
            updateProductImage($(this));
        });


    });
</script>
@endsection
