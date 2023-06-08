@extends('layouts.app')

@section('content')
<script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">  
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://getbootstrap.com/docs/5.2/assets/css/docs.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
<link href="{{ asset('/') }}style.css" rel="stylesheet">

<div class="container">
    <div class="row justify-content-center ">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Laravel + AJAX + Jquery CRUD <fieldset></fieldset></div>
                <div class="card-body">
                    <div class="mt-3  d-flex">
                        <div style="justify-content: center;" class="d-flex ">
                            <div class="dropdown2">
                                <button class="btn-dropdown2 btn btn-primary">Preferences</button>
                                <div class="dropdown-content2 p-4">
                                <div style="justify-content: space-between;" class="col d-flex">    
                                    <label>Name</label>
                                    <label class="switch">
                                        <input type="checkbox" value="1" name="name" id="name-filter">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div style="justify-content: space-between;" class="col d-flex">
                                    <label>Details</label>
                                    <label class="switch">
                                        <input type="checkbox"  value="1" name="details" id="details-filter">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div style="justify-content: space-between;" class="col  d-flex">
                                    <label>SKU</label>
                                    <label class="switch">
                                        <input type="checkbox"  value="1" name="sku" id="sku-filter">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div style="justify-content: space-between;" class="col d-flex">
                                    <label>Price</label>     
                                    <label class="switch">
                                        <input type="checkbox"  value="1" name="price" id="price-filter">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div style="justify-content: space-between;" class="col d-flex">
                                    <label>Status</label>
                                    <label class="switch">
                                        <input type="checkbox" name="status" id="status-filter">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div class="pt-4">
                                    <button id="preferences-save" class="btn btn-primary"><i class="fa fa-save"></i></button>
                                    <button id="preferences-reset" class="btn btn-primary"><i class="fa fa-refresh"></i></button>
                                    <button id="preferences-close" class="btn btn-danger"><i class="fa fa-close"></i></button> 
                                </div>
                                </div>
                            </div>
                            &nbsp;&nbsp;
                            <button class="ml-5 btn btn-primary " onclick="createproduct()">Create Product</button>&nbsp;&nbsp;
                            <form class="d-flex border-1" action="{{ config('app.url') }}/api/products/import" enctype="multipart/form-data" method="post">
                                    @csrf
                                    <input id="product_csv" name="products_list" type="file" class="form-control"/>&nbsp;
                                    <button type="submit" class="btn btn-success">Import </button>
                            </form>
                            &nbsp;&nbsp;
                            <a href="javascript:void(0);" class="btn btn-secondary " onclick="exportVisibleRowsToCSV('product_table')">Export</a>&nbsp; 
                        </div>
                        &nbsp;
                        <div class="dropdown1">
                            <button class="btn-dropdown1 btn btn-primary">Filters</button> 
                            <div class="dropdown-content1">
                            <a>
                                <div class="form-group col ">
                                    <div class="d-flex">
                                        <input type="text" class="form-control" id="name-input" placeholder="Search by name">
                                    </div>
                                </div>
                            </a>
                            <a> 
                                <div class="form-group col">
                                    <div class="d-flex">
                                        <input type="text" class="form-control" id="sku-input" placeholder="Search by SKU">
                                    </div>
                                </div>
                            </a>
                            <a>
                                <div class="form-group col">
                                    <div class="d-flex">
                                        <input type="text" class="form-control" id="details-input" placeholder="Search by Details">
                                    </div>
                                </div>
                            </a>
                            <a>
                                <div class="form-group col">
                                    <div class="d-flex">
                                        <input type="text" class="form-control" id="price-input" placeholder="Search by Price">
                                    </div>
                                </div>
                            </a>
                            <a>
                                <div class="form-group col">
                                    <button id="btn-search" class="btn btn-primary"><i class="fa fa-search"></i></button> 
                                    <button id="btn-clear-search" class="btn btn-primary"><i class="fa fa-refresh"></i></button> 
                                    <button id="btn-close-search" class="btn btn-danger"><i class="fa fa-close"></i></button> 
                                </div>
                            </a>
                            </div>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            
                        </div> 
                    </div>
                    </div>
                    <div class="container mt-5">
                        <table id="product_table" class="table table-striped mt-4">
                            <thead id="product_list_heading grey">
                                <tr>
                                    <th id="plh_id" scope="col">Id</th>
                                    <th id="plh_name" scope="col">Name</th>
                                    <th id="plh_details" scope="col">Details</th>
                                    <th id="plh_sku" scope="col">SKU</th>
                                    <th id="plh_price" scope="col">Price</th>
                                    <th id="plh_status" scope="col">Status</th>
                                    <th id="plh_action" scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody id="product_list">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="productsModal" tabindex="-1" role="dialog" aria-labelledby="productsModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="product_form">
                <div class="modal-header">
                    <h5 class="modal-title" id="productsModalHeading">Products CRUD</h5>
                    <span style="cursor: pointer;" onclick="$('#productsModal').modal('hide');" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row row">
                    <div class="form-group col-md-4">
                        <label for="inputEmail4">Product Name</label>
                        <input type="text" area-controls="product_table" class="form-control" id="name" name="name" placeholder="Product Name" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputPassword4">Product Price</label>
                        <input type="text" area-controls="product_table" onkeypress=" return isNumericKey(event)" class="form-control" id="price" name="price" placeholder="Product Price" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputPassword4">Product SKU</label>
                        <input type="text" area-controls="product_table" class="form-control" id="sku" name="sku" placeholder="Product SKU" required>
                    </div>
                    </div>
                    <div class="form-group mt-3">
                        <label for="inputAddress">Details</label>
                        <textarea class="form-control" id="details" placeholder="Enter Product Details" required></textarea>
                    </div>
                    <div class="form-group mt-3">
                        <label for="inputAddress">Status</label>
                        <select  class="form-control" id="status" name="status" required>
                            <option value="">Select</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" id="saveSection">
                    <button type="submit" id="btnProductSave" class="btn btn-primary">Save</button> 
                </div>  
            </form>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://code.jquery.com/jquery-3.7.0.min.js" ></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>


<script>
    const apiPreferences = "{{ config('app.url') }}/api/preferences";
    const apiProducts = "{{ config('app.url') }}/api/products";
    var token = "{{ csrf_token() }}";
</script>

<script src="{{ asset('/') }}products.js"></script>

@endsection
